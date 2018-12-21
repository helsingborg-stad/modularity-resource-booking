import AccordionTable from '../Components/AccordionTable';
import {getCustomerOrders} from '../../Api/orders';
import {Pagination, PreLoader} from 'hbg-react';

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
                    const data = this.mapData(result);
                    this.setState({
                            isLoaded: true,
                            items: data,
                            filteredItems: data,
                            totalPages: Math.ceil(data.length / perPage)
                        },
                        () => {
                            this.updateItemList();
                        });
                }, ({error}) => {
                    this.setState({isLoaded: true, error});
                }
            );
    };

    mapData = (jsonData) => {
        return jsonData.map(item => ({
            id: item.id,
            headings: [
                item.order_id,
                item.date,
                item.status
            ],
            articles: item.articles
        }));
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
        const headings = [translation.orderNumber, translation.date, translation.status];
        const articleHeadings = [translation.article, translation.type, translation.period, translation.price];

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
                </div>
            );
        } else {
            return (
                <div className="grid">
                    <AccordionTable
                        items={filteredItems}
                        headings={headings}
                        articleHeadings={articleHeadings}
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