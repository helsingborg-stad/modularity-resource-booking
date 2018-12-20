import OrderList from '../Components/OrderList';
import {getCustomerOrders} from '../../Api/orders';
import {Pagination} from 'hbg-react';
import PreLoader from "../Components/PreLoader";

class OrderHistory extends React.Component {
    constructor() {
        super();
        this.state = {
            error: null,
            isLoaded: false,
            items: [],
            itemValues: [],
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
            return <PreLoader/>;
        } else {
            return (
                <div className="grid">
                    <OrderList
                        items={filteredItems}
                        translation={translation}
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