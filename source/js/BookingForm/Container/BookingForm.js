import { Button, Calendar } from 'hbg-react';
import { getArticle } from '../../Api/products';
import PropTypes from 'prop-types';
import dateFns from 'date-fns';
import Summary from '../Component/Summary';
import Files from '../Component/Files';
import { createOrder } from '../../Api/orders';

class BookingForm extends React.Component {
    static propTypes = {
        articlePrice: PropTypes.oneOfType([PropTypes.string, PropTypes.number]).isRequired,
        articleName: PropTypes.string.isRequired,
        articleType: PropTypes.string.isRequired,
        restNonce: PropTypes.string.isRequired,
        articleId: PropTypes.oneOfType([PropTypes.string, PropTypes.number]).isRequired,
        avalibleSlots: PropTypes.array.isRequired,
        mediaRequirements: PropTypes.array
    };

    constructor(props) {
        super(props);
        this.state = {
            selectedSlots: [],
            calendarView: true,
            files: props.mediaRequirements
        };

        this.handleClickEvent = this.handleClickEvent.bind(this);
        this.handleEventClassName = this.handleEventClassName.bind(this);
        this.handleRemoveItem = this.handleRemoveItem.bind(this);
        this.handleFileUpload = this.handleFileUpload.bind(this);
        this.createOrder = this.createOrder.bind(this);
    }

    createOrder() {
        const { articleType, articleId } = this.props;
        const { selectedSlots, files } = this.state;

        let orders = [];

        selectedSlots.forEach(id => {
            orders.push({
                type: articleType,
                article_id: articleId,
                slot_id: id
            });
        });

        createOrder(orders, files)
            .then(result => {
                console.log(result);
            })
            .catch(result => {
                console.log('createOrder() request failed');
            });
    }

    /**
     * Callback that fires when clicking a calendar event
     * @param  {Date object} date The date being clicked
     * @return {void}
     */
    handleClickEvent(event) {
        const { selectedSlots } = this.state;

        //Add slot
        if (!selectedSlots.includes(event.id) && event['available_stock'] > 0) {
            this.setState((state, props) => {
                let slots = state.selectedSlots;
                slots.push(event.id);

                return {
                    selectedSlots: slots
                };
            });

            return;
        }

        //Remove slot
        if (selectedSlots.includes(event.id)) {
            this.setState((state, props) => {
                let slots = state.selectedSlots.filter(id => id !== event.id);
                return {
                    selectedSlots: slots
                };
            });

            return;
        }
    }

    /**
     * Callback responsible for adding css classes to calendar event items
     * @param  {Date object} date The date being clicked
     * @return {void}
     */
    handleEventClassName(event) {
        const { selectedSlots } = this.state;
        let classes = [];

        if (event['available_stock'] > 0) {
            classes.push('calendar__event--action');
        }

        if (selectedSlots.includes(event.id)) {
            classes.push('is-active');
        }

        if (event['available_stock'] <= 0) {
            classes.push('is-disabled');
        }

        return classes;
    }

    /**
     * Callback responsible
     * @param  {Date object} date The date being clicked
     * @return {void}
     */
    handleRemoveItem(slot) {
        this.setState((state, props) => {
            const calendarView =
                state.selectedSlots.length === 1 && !state.calendarView ? true : state.calendarView;
            const slots = state.selectedSlots.filter(id => id !== slot.id);
            return {
                selectedSlots: slots,
                calendarView: calendarView
            };
        });
    }

    /**
     * [handleFileUpload description]
     * @param  {[type]} files [description]
     * @param  {[type]} media [description]
     * @return {[type]}       [description]
     */
    handleFileUpload(files, media) {
        this.setState((state, props) => {
            let mediaRequirements = state.files;
            mediaRequirements[media.index].file = files[0];

            return { files: mediaRequirements };
        });
    }

    render() {
        const { avalibleSlots, price } = this.props;
        const { selectedSlots, calendarView, files } = this.state;
        return (
            <div>
                {calendarView ? (
                    <Calendar
                        events={avalibleSlots}
                        onClickEvent={this.handleClickEvent}
                        eventClassName={this.handleEventClassName}
                    />
                ) : null}

                {selectedSlots.length > 0 ? (
                    <Summary onClickRemoveItem={this.handleRemoveItem}>
                        {avalibleSlots.filter(slot => selectedSlots.includes(slot.id))}
                    </Summary>
                ) : null}

                {calendarView ? (
                    <Button
                        onClick={() => {
                            this.setState((state, props) => ({
                                calendarView: false
                            }));
                        }}
                    >
                        Gå vidare
                    </Button>
                ) : (
                    <Button
                        onClick={() => {
                            this.setState((state, props) => ({
                                calendarView: true
                            }));
                        }}
                    >
                        Gå tillbaka
                    </Button>
                )}
                {files.length > 0 ? (
                    <div>
                        <Files onFileUpload={this.handleFileUpload}>{files}</Files>
                    </div>
                ) : null}

                {selectedSlots.length > 0 &&
                files.filter(media => media.file !== null).length === files.length ? (
                    <Button color="primary" onClick={this.createOrder}>
                        Order
                    </Button>
                ) : null}
            </div>
        );
    }
}

export default BookingForm;
