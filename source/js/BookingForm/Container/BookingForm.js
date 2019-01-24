import { Button, Calendar } from 'hbg-react';
import { getArticle } from '../../Api/products';
import PropTypes from 'prop-types';
import dateFns from 'date-fns';
import Summary from '../Component/Summary';
import Files from '../Component/Files';
import { createOrder } from '../../Api/orders';
import classNames from 'classnames';

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
            files: props.mediaRequirements,

            //Notice
            notice: '',
            noticeType: '',

            //Lock input
            lockInput: false
        };

        this.handleClickEvent = this.handleClickEvent.bind(this);
        this.handleEventClassName = this.handleEventClassName.bind(this);
        this.handleRemoveItem = this.handleRemoveItem.bind(this);
        this.handleFileUpload = this.handleFileUpload.bind(this);
        this.submitOrder = this.submitOrder.bind(this);
        this.handleEventContent = this.handleEventContent.bind(this);
    }

    submitOrder() {
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
                console.log(result);
                console.log('submitOrder() in BookingForm.js failed');
            });
    }

    /**
     * Callback for customizing event content, fires once for each event
     * @param  {object} event Event object data
     * @return {jsx} React Component object
     */
    handleEventContent(event) {
        const {selectedSlots} = this.state;
        let disabled = !event['unlimited_stock'] && event['available_stock'] <= 0 ? true : false;
        let exists = selectedSlots.includes(event.id) ? true : false;

        if (disabled) {
            return event.title;
        }

        return (
            <div>
                <span className="calendar__event_content">{event.title}</span>
                <span className="calendar__event_hidden">
                    <i className={classNames(
                        'pricon',
                        {
                            'pricon-minus-o': exists,
                            'pricon-plus-o': !exists
                        }
                    )}></i>
                    {!exists ? ' Lägg till ' : ' Ta bort '}
                </span>
            </div>
        );
    }

    /**
     * Callback that fires when clicking a calendar event
     * @param  {Date object} date The date being clicked
     * @return {void}
     */
    handleClickEvent(event) {
        const { selectedSlots } = this.state;

        //Add slot
        if (
            (!selectedSlots.includes(event.id) && event['available_stock'] > 0) ||
            (!selectedSlots.includes(event.id) && event['available_stock'] === null)
        ) {
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

        classes.push('calendar__event--slot');

        if (
            (event['unlimited_stock'] && event['available_stock'] === null) ||
            event['available_stock'] > 0
        ) {
            classes.push('calendar__event--action');
        }

        if (selectedSlots.includes(event.id)) {
            classes.push('is-active');
        }

        if (!event['unlimited_stock'] && event['available_stock'] <= 0) {
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
        const { avalibleSlots, price, translation } = this.props;
        const { selectedSlots, calendarView, files } = this.state;
        return (
            <div>
                {calendarView ? (
                    <Calendar
                        events={avalibleSlots}
                        onClickEvent={this.handleClickEvent}
                        eventClassName={this.handleEventClassName}
                        eventContent={this.handleEventContent}
                    />
                ) : null}

                {selectedSlots.length > 0 ? (
                    <Summary onClickRemoveItem={this.handleRemoveItem} translation={translation}>
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
                        {translation.goback}
                    </Button>
                )}
                {files.length > 0 ? (
                    <div>
                        <Files onFileUpload={this.handleFileUpload}>{files}</Files>
                    </div>
                ) : null}

                {selectedSlots.length > 0 &&
                files.filter(media => media.file !== null).length === files.length ? (
                    <Button color="primary" onClick={this.submitOrder}>
                        {translation.order}
                    </Button>
                ) : null}
            </div>
        );
    }
}

export default BookingForm;
