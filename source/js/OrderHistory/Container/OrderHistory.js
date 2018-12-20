import {getCustomerOrders} from '../../Api/orders';
import {Pagination, PreLoader, AccordionTable} from 'hbg-react';

class OrderHistory extends React.Component {
    constructor() {
        super();
        this.state = {
            error: null,
            isLoaded: false,
            items: [],
            filteredItems: [],
            totalPages: 0,
            currentPage: 1
        };
    }

    componentDidMount() {
        this.getOrders();
    }

    getOrders = () => {
        const {perPage} = this.props;

        getCustomerOrders()
            .then(
                ({result}) => {
                    console.log(result);

                    if (!result || Object.keys(result).length === 0) {
                        this.setState({
                            error: Error('Could not fetch data from URL.'),
                            isLoaded: true
                        });
                        return;
                    }
                    this.setState({
                            isLoaded: true,
                            items: result,
                            filteredItems: result,
                            totalPages: Math.ceil(result.length / perPage)
                        },
                        () => {
                            this.updateItemList();
                        });
                }, ({error}) => {
                    this.setState({isLoaded: true, error});
                }
            );
    };

    updateItemList = () => {
        const {items, currentPage} = this.state;
        const {perPage} = this.props;
        const begin = ((currentPage - 1) * perPage);
        const end = begin + perPage;

        this.setState({
            filteredItems: items.slice(begin, end)
        });
    };

    nextPage = () => {
        if (this.state.currentPage === this.state.totalPages) {
            return;
        }
        const currentPage = this.state.currentPage += 1;
        this.setState({currentPage: currentPage}, () => this.updateItemList());
    };

    prevPage = () => {
        if (this.state.currentPage <= 1) {
            return;
        }
        const currentPage = this.state.currentPage -= 1;
        this.setState({currentPage: currentPage}, () => this.updateItemList());
    };

    paginationInput = (e) => {
        let currentPage = e.target.value ? parseInt(e.target.value) : '';
        currentPage = (currentPage > this.state.totalPages) ? this.state.totalPages : currentPage;
        this.setState(
            {currentPage: currentPage},
            () => {
                if (currentPage) {
                    this.updateItemList();
                }
            }
        );
    };

    render() {
        const {filteredItems, error, isLoaded, totalPages, currentPage} = this.state;
        const {translation} = this.props;

        if (error) {
            return (
                <div className="gutter">
                    <div className="notice warning">
                        <i className="pricon pricon-notice-warning"></i> {translation.somethingWentWrong}
                    </div>
                </div>
            );
        } else if (!isLoaded) {
            return (
                <div className="gutter">
                    <PreLoader/>
                </div>);
        } else {
            return (
                <div className="grid">
                    <AccordionTable
                        items={filteredItems}
                        headings={headings}
                        showSearch={false}
                        langNoResults={translation.noOrdersFound}
                    />
                    <div className="grid gutter">
                        <div className="grid-fit-content u-ml-auto">
                            <Pagination
                                current={currentPage}
                                total={totalPages}
                                next={this.nextPage}
                                prev={this.prevPage}
                                input={this.paginationInput}
                                langPrev={translation.prev}
                                langNext={translation.next}
                            />
                        </div>
                    </div>
                </div>
            );
        }
    }
}

export default OrderHistory;