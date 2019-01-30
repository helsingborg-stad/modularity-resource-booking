import { Button, Calendar, Notice } from 'hbg-react';
import { getArticle, getSlots } from '../../Api/products';
import PropTypes from 'prop-types';
import dateFns from 'date-fns';
import Summary from '../Component/Summary';
import Files from '../Component/Files';
import { createOrder } from '../../Api/orders';
import classNames from 'classnames';
import { ValidateFileSize } from '../Helper/hyperForm';

class BookingForm extends React.Component {
    static propTypes = {
        translation: PropTypes.object.isRequired,
        userId: PropTypes.number.isRequired,
        articleType: PropTypes.string.isRequired,
        articleId: PropTypes.number.isRequired,
        restNonce: PropTypes.string.isRequired,
        restUrl: PropTypes.string.isRequired
    };

    constructor(props) {
        super(props);
        this.state = {
            //Article
            articleName: '',
            articlePrice: 0,

            //Slots
            avalibleSlots: [],
            selectedSlots: [],

            //Files
            files: [],

            //Notice
            notice: '',
            noticeType: '',

            isLoading: true,

            lockForm: false,
            formIsLoading: false,

            submitted: false
        };

        this.handleClickEvent = this.handleClickEvent.bind(this);
        this.handleEventClassName = this.handleEventClassName.bind(this);
        this.handleRemoveItem = this.handleRemoveItem.bind(this);
        this.handleFileUpload = this.handleFileUpload.bind(this);
        this.submitOrder = this.submitOrder.bind(this);
        this.handleEventContent = this.handleEventContent.bind(this);
        this.fetchData = this.fetchData.bind(this);
        this.fetchArticle = this.fetchArticle.bind(this);
        this.fetchSlots = this.fetchSlots.bind(this);
        this.resetForm = this.resetForm.bind(this);
    }

    componentDidMount() {
        this.fetchData();
    }

    /**
     * Submits an order to the rest API
     * @param  {[type]} e Click event
     * @return {void}
     */
    submitOrder(e) {
        e.preventDefault();
        const { articleType, articleId, restUrl, restNonce } = this.props;
        const { selectedSlots, files, notice, lockForm } = this.state;

        //Locked
        if (lockForm) {
            return;
        }

        //Lock & load
        this.setState({ lockForm: true, formIsLoading: true });

        //Make sure we have selected slots
        if (selectedSlots.length <= 0) {
            this.setState({
                formIsLoading: false,
                lockForm: false,
                notice: 'Please select atleast one date in the calendar.',
                noticeType: 'warning'
            });
            return;
        }

        //Reset notice
        if (notice.length > 0) {
            this.setState({ notice: '' });
        }

        //Orders
        let orders = [];

        selectedSlots.forEach(id => {
            orders.push({
                type: articleType,
                article_id: articleId,
                slot_id: id
            });
        });

        createOrder(orders, files, restUrl, restNonce)
            .then(result => {
                //Reset loading
                this.setState({ formIsLoading: false });

                //Dimension error
                if (
                    result.state === 'dimension-error' &&
                    Object.keys(result.data.invalid_dimensions).length > 0
                ) {
                    this.setState((state, props) => {
                        let files = state.files;
                        Object.keys(result.data.invalid_dimensions).forEach(fileIndex => {
                            files[fileIndex].error = result.data.invalid_dimensions[fileIndex];
                        });
                    });
                }

                //Unlock form if not succesful
                if (result.state !== 'success') {
                    this.setState({ lockForm: false });
                } else {
                    this.setState({ submitted: true });
                }

                this.setState((state, props) => {
                    return {
                        notice: result.message,
                        noticeType: result.state === 'success' ? 'success' : 'warning'
                    };
                });
            })
            .catch(result => {
                console.log(result);
                this.setState((state, props) => {
                    return { notice: result, noticeType: 'warning' };
                });
            });
    }

    /**
     * Fetches article and slot data and initiates filesize validation on file input fields
     * @return {void} [description]
     */
    fetchData() {
        this.fetchArticle()
            .then(() => {
                this.fetchSlots()
                    .then(() => {
                        this.setState({ isLoading: false });
                        new ValidateFileSize();
                    })
                    .catch(error => {
                        console.log(error);
                    });
            })
            .catch(error => {
                console.log(error);
            });
    }

    /**
     * Fetches article data such as name, price & files.
     * @return {Promise}
     */
    fetchArticle() {
        const { userId, articleType, articleId, restNonce, restUrl } = this.props;

        return getArticle(articleId, articleType, restUrl)
            .then(article => {
                this.setState((state, props) => ({
                    articleName: article[0]['title'],
                    articlePrice: article[0]['price'],
                    files: article[0].media_requirements.map(mediaObject => {
                        let media = mediaObject;
                        media.file = null;

                        return media;
                    })
                }));
            })
            .catch(result => {
                console.log(result);
            });
    }

    /**
     * Fetches slots and maps additional data to each slot
     * @return {Promise}
     */
    fetchSlots() {
        const { userId, articleType, articleId, restNonce, restUrl } = this.props;

        return getSlots(articleId, articleType, userId, restUrl)
            .then(slots => {
                this.setState((state, props) => ({
                    avalibleSlots: slots.map(slotData => {
                        let slot = slotData;
                        slot['start'] = dateFns.parse(slotData.start);
                        slot['stop'] = dateFns.parse(slotData.stop);
                        slot['articleName'] = state.articleName;
                        slot['articlePrice'] = state.articlePrice;

                        let startOfWeek = dateFns.startOfWeek(slot['start'], { weekStartsOn: 1 });
                        let endOfWeek = dateFns.endOfWeek(slot['start'], { weekStartsOn: 1 });

                        if (
                            dateFns.isSameDay(startOfWeek, slot['start']) &&
                            dateFns.isSameDay(endOfWeek, slot['stop'])
                        ) {
                            slot['title'] =
                                state.articleName +
                                ' (Vecka ' +
                                dateFns.getISOWeek(slot.start) +
                                ')';
                        } else {
                            slot['title'] = state.articleName;
                        }

                        return slot;
                    })
                }));
            })
            .catch(result => {
                console.log(result);
            });
    }

    /**
     * Resets the form by fetching new article & slot data and clearing all input.
     * @return {void}
     */
    resetForm() {
        this.setState({
            isLoading: true,
            submitted: false,
            lockForm: false,
            selectedSlots: [],
            notice: '',
            noticeType: ''
        });

        this.fetchData();
    }

    /**
     * Callback for customizing event content output, fires once for each event
     * @param  {object} event Event object data
     * @return {jsx} React Component object
     */
    handleEventContent(event) {
        const { selectedSlots } = this.state;
        let disabled = !event['unlimited_stock'] && event['available_stock'] <= 0 ? true : false;
        let isSelected = selectedSlots.includes(event.id) ? true : false;

        let stockCount = '';

        if (!event['unlimited_stock']) {
            let avalibleStock = event['total_stock'] - event['available_stock'];
            avalibleStock = isSelected ? avalibleStock + 1 : avalibleStock;

            stockCount = ' - ' + avalibleStock + '/' + event['total_stock'];
        }

        if (disabled) {
            return event.title + stockCount;
        }

        return (
            <div>
                <span className="calendar__event_content">{event.title + stockCount}</span>
                <span className="calendar__event_hidden">
                    <i
                        className={classNames('pricon', {
                            'pricon-minus-o': isSelected,
                            'pricon-plus-o': !isSelected
                        })}
                    />
                    {!isSelected ? ' Lägg till ' : ' Ta bort '}
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
            const slots = state.selectedSlots.filter(id => id !== slot.id);
            return {
                selectedSlots: slots
            };
        });
    }

    /**
     * Fires on file upload, saves the file in state
     * @param  {array} files Array of file objects uploaded through the file input
     * @param  {object} media The file object taken from files in states
     * @return {void}
     */
    handleFileUpload(files, media) {
        this.setState((state, props) => {
            let mediaRequirements = state.files;
            mediaRequirements[media.index].file = files.length > 0 ? files[0] : null;
            mediaRequirements[media.index].error = '';
            return { files: mediaRequirements };
        });
    }

    render() {
        const { translation, fileUploadTitle, orderHistoryPage } = this.props;
        const {
            avalibleSlots,
            selectedSlots,
            files,
            notice,
            noticeType,
            isLoading,
            lockForm,
            formIsLoading,
            submitted
        } = this.state;

        if (isLoading) {
            return (
                <div className="gutter gutter-xl">
                    <div className="loading">
                        <div>{}</div>
                        <div>{}</div>
                        <div>{}</div>
                        <div>{}</div>
                    </div>
                </div>
            );
        } else {
            return (
                <form onSubmit={this.submitOrder}>
                    <div className="grid">
                        <div className="grid-xs-12 u-mb-3">
                            <Calendar
                                events={avalibleSlots}
                                onClickEvent={this.handleClickEvent}
                                eventClassName={this.handleEventClassName}
                                eventContent={this.handleEventContent}
                                maxDate={
                                    avalibleSlots.length > 0
                                        ? avalibleSlots[avalibleSlots.length - 1].stop
                                        : null
                                }
                                minDate={avalibleSlots.length > 0 ? avalibleSlots[0].start : null}
                                currentMonth={
                                    avalibleSlots.length > 0 ? avalibleSlots[0].start : new Date()
                                }
                                disable={lockForm ? true : false}
                            />
                        </div>

                        {files.length > 0 ? (
                            <div className="grid-xs-12 u-mb-3">
                                <h4 className="u-mb-2">{fileUploadTitle}</h4>
                                <Files
                                    onFileUpload={this.handleFileUpload}
                                    disabled={lockForm ? true : false}
                                >
                                    {files}
                                </Files>
                            </div>
                        ) : null}

                        {selectedSlots.length > 0 ? (
                            <div className="grid-xs-12 u-mb-3">
                                <Summary
                                    onClickRemoveItem={this.handleRemoveItem}
                                    translation={translation}
                                    disabled={lockForm ? true : false}
                                >
                                    {avalibleSlots.filter(slot => selectedSlots.includes(slot.id))}
                                </Summary>
                            </div>
                        ) : null}

                        <div className="grid-xs-12">
                            <div className="grid grid-va-middle">
                                <div className="grid-fit-content">
                                    <Button
                                        color="primary"
                                        submit
                                        disabled={lockForm ? true : false}
                                        title={translation.order}
                                    />
                                </div>

                                {submitted ? (
                                    <div className="grid-fit-content u-pl-0">
                                        <Button onClick={this.resetForm}>Make a new order</Button>
                                    </div>
                                ) : null}

                                {formIsLoading ? (
                                    <div className="grid-fit-content u-pl-0">
                                        {' '}
                                        <div className="spinner spinner-dark" />
                                    </div>
                                ) : null}
                            </div>
                        </div>

                        {notice.length > 0 && (
                            <div className="grid-xs-12 u-mt-2">
                                <Notice type={noticeType} icon>
                                    <span dangerouslySetInnerHTML={{ __html: notice }} />
                                </Notice>
                            </div>
                        )}
                    </div>
                </form>
            );
        }
    }
}

export default BookingForm;
