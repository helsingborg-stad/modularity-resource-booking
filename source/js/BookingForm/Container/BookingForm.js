import PropTypes from 'prop-types';
import dateFns from 'date-fns';
import classNames from 'classnames';
import { Button, Calendar, Notice, Input } from 'hbg-react';
import { getArticle, getSlots } from '../../Api/products';
import Summary from '../Component/Summary';
import Files from '../Component/Files';
import { createOrder } from '../../Api/orders';
import { ValidateFileSize } from '../Helper/hyperForm';

import Checkbox from '../Component/Checkbox';

class BookingForm extends React.Component {
    static propTypes = {
        translation: PropTypes.object.isRequired,
        userId: PropTypes.number.isRequired,
        articleType: PropTypes.string.isRequired,
        articleId: PropTypes.number.isRequired,
        restNonce: PropTypes.string.isRequired,
        restUrl: PropTypes.string.isRequired,
    };

    constructor(props) {
        super(props);
        this.state = {
            // Article
            articleName: '',
            articlePrice: 0,

            // Slots
            avalibleSlots: [],
            selectedSlots: [],

            // Files
            files: [],

            // Notice
            notice: '',
            noticeType: '',

            isLoading: true,

            lockForm: false,
            formIsLoading: false,

            submitted: false,

            orderTitle: '',

            skipFileUpload: false,
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
        this.fetchData().then(() => {
            // Lock if there is no avalible slots
            if (this.state.avalibleSlots.length <= 0) {
                this.setState({
                    lockForm: true,
                    notice: this.props.translation.noSlots,
                    noticeType: 'warning',
                });
            }
        });
    }

    /**
     * Fetches article and slot data and initiates filesize validation on file input fields
     * @return {Promise}
     */
    fetchData() {
        return this.fetchArticle().then(() => {
            return this.fetchSlots()
                .then(() => {
                    this.setState({ isLoading: false });
                    new ValidateFileSize();
                })
                .catch(error => {});
        });
    }

    /**
     * Fetches article data such as name, price & files.
     * @return {Promise}
     */
    fetchArticle() {
        const { articleType, articleId, restUrl } = this.props;

        return getArticle(articleId, articleType, restUrl)
            .then(article => {
                this.setState((state, props) => ({
                    articleName: article[0].title,
                    articlePrice: article[0].price,
                    files: article[0].media_requirements.map(mediaObject => {
                        const media = mediaObject;
                        media.file = null;

                        return media;
                    }),
                }));
            })
            .catch(error => {});
    }

    /**
     * Fetches slots and maps additional data to each slot
     * @return {Promise}
     */
    fetchSlots() {
        const { userId, articleType, articleId, restUrl } = this.props;

        return getSlots(articleId, articleType, userId, restUrl)
            .then(slots => {
                this.setState((state, props) => ({
                    avalibleSlots: slots.map(slotData => {
                        const slot = slotData;
                        slot.start = dateFns.parse(slotData.start);
                        slot.stop = dateFns.parse(slotData.stop);
                        slot.articleName = state.articleName;
                        slot.articlePrice = state.articlePrice;

                        const startOfWeek = dateFns.startOfWeek(slot.start, { weekStartsOn: 1 });
                        const endOfWeek = dateFns.endOfWeek(slot.start, { weekStartsOn: 1 });

                        if (
                            dateFns.isSameDay(startOfWeek, slot.start) &&
                            dateFns.isSameDay(endOfWeek, slot.stop)
                        ) {
                            slot.title = `Vecka ${dateFns.getISOWeek(slot.start)}`;
                        } else {
                            slot.title = state.articleName;
                        }

                        return slot;
                    }),
                }));
            })
            .catch(() => {});
    }

    /**
     * Submits an order to the rest API
     * @param  {[type]} e Click event
     * @return {void}
     */
    submitOrder(e) {
        e.preventDefault();
        const { articleType, articleId, restUrl, restNonce, translation } = this.props;
        const { selectedSlots, files, notice, lockForm, orderTitle, skipFileUpload } = this.state;

        // Locked
        if (lockForm) {
            return;
        }

        // Lock & load
        this.setState({ lockForm: true, formIsLoading: true });

        // Make sure we have selected slots
        if (selectedSlots.length <= 0) {
            this.setState({
                formIsLoading: false,
                lockForm: false,
                notice: translation.selectAtleastOneDate,
                noticeType: 'warning',
            });
            return;
        }

        // Reset notice
        if (notice.length > 0) {
            this.setState({ notice: '' });
        }

        // Orders
        const orders = [];

        selectedSlots.forEach(id => {
            orders.push({
                type: articleType,
                article_id: articleId,
                slot_id: id,
            });
        });

        createOrder(orderTitle, orders, files, restUrl, skipFileUpload, restNonce)
            .then(result => {
                // Reset loading
                this.setState({ formIsLoading: false });

                // Dimension error
                if (
                    result.state === 'dimension-error' &&
                    Object.keys(result.data.invalid_dimensions).length > 0
                ) {
                    this.setState((state, props) => {
                        const { files } = state;
                        Object.keys(result.data.invalid_dimensions).forEach(fileIndex => {
                            files[fileIndex].error = result.data.invalid_dimensions[fileIndex];
                        });
                    });
                }

                // Unlock form if not succesful
                if (result.state !== 'success') {
                    this.setState({ lockForm: false });
                } else {
                    this.setState({ submitted: true });
                }

                this.setState((state, props) => {
                    return {
                        notice: result.message,
                        noticeType: result.state === 'success' ? 'success' : 'warning',
                    };
                });
            })
            .catch(result => {
                this.setState((state, props) => {
                    return { notice: result, noticeType: 'warning' };
                });
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
            noticeType: '',
            orderTitle: '',
        });

        this.fetchData();
    }

    /**
     * Callback for customizing event content output, fires once for each event
     * @param  {object} event Event object data
     * @return {jsx} React Component object
     */
    handleEventContent(event) {
        const { translation } = this.props;
        const { selectedSlots } = this.state;
        const disabled = !!(!event.unlimited_stock && event.available_stock <= 0);
        const isSelected = !!selectedSlots.includes(event.id);

        let stockCount = '';

        if (!event.unlimited_stock) {
            let avalibleStock = event.total_stock - event.available_stock;
            avalibleStock = isSelected ? avalibleStock + 1 : avalibleStock;

            stockCount = ` - ${avalibleStock}/${event.total_stock}`;
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
                            'pricon-plus-o': !isSelected,
                        })}
                    />
                    {!isSelected ? ` ${translation.add} ` : ` ${translation.remove} `}
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

        // Add slot
        if (
            (!selectedSlots.includes(event.id) && event.available_stock > 0) ||
            (!selectedSlots.includes(event.id) && event.available_stock === null)
        ) {
            this.setState((state, props) => {
                const slots = state.selectedSlots;
                slots.push(event.id);

                return {
                    selectedSlots: slots,
                };
            });

            return;
        }

        // Remove slot
        if (selectedSlots.includes(event.id)) {
            this.setState((state, props) => {
                const slots = state.selectedSlots.filter(id => id !== event.id);
                return {
                    selectedSlots: slots,
                };
            });
        }
    }

    /**
     * Callback responsible for adding css classes to calendar event items
     * @param  {Date object} date The date being clicked
     * @return {void}
     */
    handleEventClassName(event) {
        const { selectedSlots } = this.state;
        const classes = [];

        classes.push('calendar__event--slot');

        if (
            (event.unlimited_stock && event.available_stock === null) ||
            event.available_stock > 0
        ) {
            classes.push('calendar__event--action');
        }

        if (selectedSlots.includes(event.id)) {
            classes.push('is-active');
        }

        if (!event.unlimited_stock && event.available_stock <= 0) {
            classes.push('is-disabled');
        }

        return classes;
    }

    /**
     * Callback responsible for removing selected slots through the summary
     * @param  {Date object} date The date being clicked
     * @return {void}
     */
    handleRemoveItem(slot) {
        this.setState((state, props) => {
            const slots = state.selectedSlots.filter(id => id !== slot.id);
            return {
                selectedSlots: slots,
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
            const mediaRequirements = state.files;
            mediaRequirements[media.index].file = files.length > 0 ? files[0] : null;
            mediaRequirements[media.index].error = '';
            return { files: mediaRequirements };
        });
    }

    render() {
        const { translation, headings, locale } = this.props;
        const {
            avalibleSlots,
            selectedSlots,
            files,
            notice,
            noticeType,
            isLoading,
            lockForm,
            formIsLoading,
            submitted,
            orderTitle,
            skipFileUpload,
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
        }
        return (
            <div className="c-card">
                <div className="c-card__body" style={{ backgroundColor: '#f4f4f4' }}>
                    <form onSubmit={this.submitOrder}>
                        <div className="grid">
                            <div className="grid-xs-12 u-mb-3">
                                <h4 className="u-mb-2">{headings.orderName}*</h4>
                                <Input
                                    type="text"
                                    name="orderTitle"
                                    value={orderTitle}
                                    handleChange={e => {
                                        this.setState({ orderTitle: e.target.value });
                                    }}
                                    placeholder={translation.campaignName}
                                    required
                                />
                            </div>
                            <div className="grid-xs-12 u-mb-3">
                                <h4 className="u-mb-2">{headings.calendar}</h4>
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
                                    minDate={
                                        avalibleSlots.length > 0 ? avalibleSlots[0].start : null
                                    }
                                    currentMonth={
                                        avalibleSlots.length > 0
                                            ? avalibleSlots[0].start
                                            : new Date()
                                    }
                                    disable={!!lockForm}
                                    locale={locale}
                                />
                            </div>

                            {files.length > 0 ? (
                                <div className="grid-xs-12 u-mb-3">
                                    <h4 className="u-mb-2">{headings.files}</h4>
                                    <Checkbox
                                        name="skipFileUpload"
                                        checked={skipFileUpload}
                                        label="Ladda upp material vid senare tillfälle"
                                        onChange={e => {
                                            this.setState((state, props) => {
                                                const { skipFileUpload } = state;

                                                return {
                                                    skipFileUpload: !skipFileUpload,
                                                };
                                            });
                                        }}
                                    />
                                    {!skipFileUpload && (
                                        <Files
                                            onFileUpload={this.handleFileUpload}
                                            disabled={!!lockForm}
                                            translation={translation}
                                        >
                                            {files}
                                        </Files>
                                    )}
                                </div>
                            ) : null}

                            {selectedSlots.length > 0 ? (
                                <div className="grid-xs-12 u-mb-3">
                                    <h4 className="u-mb-2">{headings.summary}</h4>
                                    <Summary
                                        onClickRemoveItem={this.handleRemoveItem}
                                        translation={translation}
                                        disabled={!!lockForm}
                                    >
                                        {avalibleSlots.filter(slot =>
                                            selectedSlots.includes(slot.id)
                                        )}
                                    </Summary>
                                </div>
                            ) : null}

                            <div className="grid-xs-12">
                                <div className="grid grid-va-middle">
                                    <div className="grid-fit-content">
                                        <Button
                                            color="primary"
                                            submit
                                            disabled={!!lockForm}
                                            title={translation.order}
                                        />
                                    </div>

                                    {submitted ? (
                                        <div className="grid-fit-content u-pl-0">
                                            <Button onClick={this.resetForm}>
                                                {translation.newOrder}
                                            </Button>
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
                </div>
            </div>
        );
    }
}

export default BookingForm;
