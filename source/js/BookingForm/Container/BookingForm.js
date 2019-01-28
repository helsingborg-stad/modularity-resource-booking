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

            isLoading: true
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
     * [fetchData description]
     * @return {void} [description]
     */
    fetchData() {
        this.fetchArticle()
            .then(() => {
                this.fetchSlots()
                    .then(() => {
                        this.setState({ isLoading: false });
                        //Validate filesizes
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
     * [fetchArticle description]
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
     * [fetchSlots description]
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

                        slot['title'] = 'Vecka ' + dateFns.getISOWeek(slot.start);

                        return slot;
                    })
                }));
            })
            .catch(result => {
                console.log(result);
            });
    }

    /**
     * [resetForm description]
     * @return {void}
     */
    resetForm() {
        if (!this.state.isLoading) {
            this.setState({ isLoading: true });
        }

        if (this.state.selectedSlots.length > 0) {
            this.setState({ selectedSlots: [] });
        }

        this.setState({ notice: '', noticeType: '' });

        this.fetchData();
    }

    /**
     * [submitOrder description]
     * @param  {[type]} e Click event
     * @return {void}
     */
    submitOrder(e) {
        e.preventDefault();
        const { articleType, articleId, restUrl, restNonce } = this.props;
        const { selectedSlots, files, notice } = this.state;

        let orders = [];

        if (selectedSlots.length <= 0) {
            this.setState({
                notice: 'Please select atleast one date in the calendar.',
                noticeType: 'warning'
            });
            return;
        }

        if (notice.length > 0) {
            this.setState({ notice: '' });
        }

        selectedSlots.forEach(id => {
            orders.push({
                type: articleType,
                article_id: articleId,
                slot_id: id
            });
        });

        createOrder(orders, files, restUrl, restNonce)
            .then(result => {
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
     * Callback for customizing event content, fires once for each event
     * @param  {object} event Event object data
     * @return {jsx} React Component object
     */
    handleEventContent(event) {
        const { selectedSlots } = this.state;
        let disabled = !event['unlimited_stock'] && event['available_stock'] <= 0 ? true : false;
        let exists = selectedSlots.includes(event.id) ? true : false;

        if (disabled) {
            return event.title;
        }

        return (
            <div>
                <span className="calendar__event_content">{event.title}</span>
                <span className="calendar__event_hidden">
                    <i
                        className={classNames('pricon', {
                            'pricon-minus-o': exists,
                            'pricon-plus-o': !exists
                        })}
                    />
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
            mediaRequirements[media.index].file = files.length > 0 ? files[0] : null;
            mediaRequirements[media.index].error = '';
            return { files: mediaRequirements };
        });
    }

    render() {
        const { translation } = this.props;
        const { avalibleSlots, selectedSlots, files, notice, noticeType, isLoading } = this.state;

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
                    <div className="grid grid--columns">
                        <div className="grid-xs-12">
                            <Calendar
                                events={avalibleSlots}
                                onClickEvent={this.handleClickEvent}
                                eventClassName={this.handleEventClassName}
                                eventContent={this.handleEventContent}
                                maxDate={avalibleSlots[avalibleSlots.length - 1].stop}
                                minDate={avalibleSlots[0].start}
                            />
                        </div>
                        {selectedSlots.length > 0 ? (
                            <div className="grid-xs-12">
                                <Summary
                                    onClickRemoveItem={this.handleRemoveItem}
                                    translation={translation}
                                >
                                    {avalibleSlots.filter(slot => selectedSlots.includes(slot.id))}
                                </Summary>
                            </div>
                        ) : null}

                        {files.length > 0 ? (
                            <div className="grid-xs-12">
                                <h3>Ladda upp annons material</h3>
                                <Files onFileUpload={this.handleFileUpload}>{files}</Files>
                            </div>
                        ) : null}

                        <div className="grid-xs-12">
                            <Button color="primary" submit title={translation.order} />
                        </div>

                        {notice.length > 0 && (
                            <div className="grid-xs-12">
                                <Notice type={noticeType} icon>
                                    {notice}
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
