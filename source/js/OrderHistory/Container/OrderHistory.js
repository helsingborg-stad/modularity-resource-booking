import { Pagination, PreLoader, Notice } from 'hbg-react';
import AccordionTable from '../Components/AccordionTable';
import { getCustomerOrders, postRequest } from '../../Api/orders';
import update from 'immutability-helper';

class OrderHistory extends React.Component {
    constructor() {
        super();
        this.state = {
            error: null,
            isLoaded: false,
            items: [],
            filteredItems: [],
            totalPages: 0,
            currentPage: 1,
            cancelError: null,
        };
    }

    componentDidMount() {
        this.getOrders();
    }

    getOrders = () => {
        const { perPage, restUrl, nonce, translation } = this.props;

        getCustomerOrders(restUrl, nonce)
            .then(response => {
                const data = this.mapData(response);
                this.setState(
                    {
                        isLoaded: true,
                        items: data,
                        filteredItems: data,
                        totalPages: Math.ceil(data.length / perPage),
                    },
                    () => {
                        this.updateItemList();
                    }
                );
            })
            .catch(error => {
                console.error('Request failed:', error.message);
                this.setState({ isLoaded: true, error: Error(translation.somethingWentWrong) });
            });
    };

    mapData = jsonData =>
        jsonData.map(item => ({
            id: item.id,
            headings: [item.order_id, item.date, item.status],
            articles: item.articles,
            cancelable: item.cancelable,
        }));

    updateItemList = () => {
        const { items, currentPage } = this.state;
        const { perPage } = this.props;
        const begin = (currentPage - 1) * perPage;
        const end = begin + perPage;

        this.setState({
            filteredItems: items.slice(begin, end),
        });
    };

    nextPage = () => {
        if (this.state.currentPage === this.state.totalPages) {
            return;
        }
        const currentPage = (this.state.currentPage += 1);
        this.setState({ currentPage }, () => this.updateItemList());
    };

    prevPage = () => {
        if (this.state.currentPage <= 1) {
            return;
        }
        const currentPage = (this.state.currentPage -= 1);
        this.setState({ currentPage }, () => this.updateItemList());
    };

    paginationInput = e => {
        let currentPage = e.target.value ? parseInt(e.target.value) : '';
        currentPage = currentPage > this.state.totalPages ? this.state.totalPages : currentPage;
        this.setState({ currentPage: currentPage }, () => {
            if (currentPage) {
                this.updateItemList();
            }
        });
    };

    cancelOrder = (e, index, id) => {
        e.preventDefault();
        const { restUrl, nonce, translation } = this.props;
        const { filteredItems } = this.state;

        if (e.target.classList.contains('disabled')) {
            return;
        }

        if (window.confirm(translation.cancelOrderConfirm)) {
            const order = filteredItems[index];
            // Set success state instantly, before db request
            this.setState(
                update(this.state, {
                    filteredItems: {
                        [index]: {
                            headings: {
                                2: { $set: translation.canceled },
                            },
                            cancelable: { $set: false },
                        },
                    },
                })
            );

            postRequest(restUrl + 'ModularityResourceBooking/v1/CancelOrder/' + id, nonce)
                .then(response => {
                    // Do something
                })
                .catch(error => {
                    console.error('Request failed:', error.message);
                    // Undo state changes if something goes wrong
                    this.setState(
                        update(this.state, {
                            filteredItems: {
                                [index]: {
                                    headings: {
                                        2: { $set: order.headings[2] },
                                    },
                                    cancelable: { $set: true },
                                },
                            },
                        })
                    );
                    // Show error notice
                    this.setState(
                        {
                            cancelError: Error(translation.cancelFailed),
                        },
                        () => {
                            setTimeout(() => {
                                this.setState({ cancelError: null });
                            }, 4000);
                        }
                    );
                });
        }
    };

    render() {
        const { filteredItems, error, isLoaded, totalPages, currentPage, cancelError } = this.state;
        const { translation } = this.props;
        const headings = [translation.orderNumber, translation.date, translation.status];
        const articleHeadings = [
            translation.article,
            translation.type,
            translation.period,
            translation.price,
        ];

        if (error) {
            return (
                <div className="u-p-2">
                    <Notice type="warning" icon>
                        {error.message}
                    </Notice>
                </div>
            );
        }
        if (!isLoaded) {
            return (
                <div className="gutter">
                    <PreLoader />
                </div>
            );
        }
        return (
            <div>
                {cancelError && (
                    <div className="u-p-2">
                        <Notice type="danger" icon>
                            {cancelError.message}
                        </Notice>
                    </div>
                )}

                <div className="grid">
                    <AccordionTable
                        items={filteredItems}
                        headings={headings}
                        articleHeadings={articleHeadings}
                        translation={translation}
                        cancelOrder={this.cancelOrder}
                    />
                    {filteredItems.length > 0 && (
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
                    )}
                </div>
            </div>
        );
    }
}

export default OrderHistory;
