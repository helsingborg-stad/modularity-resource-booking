import BookingForm from './Container/BookingForm';
import { getArticle, getSlots } from '../Api/products';
import dateFns from 'date-fns';

const { translation, article_type, article_id, user_id, order_nonce } = modResourceBookingForm;

let avalibleSlots = [],
    disabledSlots = [];

class App {
    constructor() {
        this.articleName = '';
        this.articlePrice = 0;
        this.mediaRequirements = [];
        this.slots = [];

        this.fetchData().then(() => {
            this.init();
        });
    }

    /**
     * Fetch article data and slots
     * @return {void}
     */
    fetchData() {
        return getArticle(article_id, article_type)
            .then(article => {
                //Article data
                this.articlePrice = article[0].price;
                this.mediaRequirements = article[0].media_requirements.map(mediaObject => {
                    let media = mediaObject;
                    media.file = null;

                    return media;
                });
                this.articleName = article[0].title;

                return getSlots(article_id, article_type, user_id)
                    .then(slots => {
                        //Map slots
                        this.slots = slots.map(slotData => {
                            let slot = slotData;
                            slot['start'] = dateFns.parse(slotData.start);
                            slot['stop'] = dateFns.parse(slotData.stop);
                            slot['articleName'] = this.articleName;
                            slot['articlePrice'] = this.articlePrice;

                            slotData.title = 'Vecka ' + dateFns.getISOWeek(slot.start);

                            return slot;
                        });

                        return slots;
                    })
                    .catch(result => {
                        console.log(result);
                        console.log('Failed to fetch slots');
                    });
            })
            .catch(result => {
                console.log(result);
                console.log('Failed to fetch article');
            });
    }

    /**
     * Convets dates in a slot array to Date object
     * @param  {array} slots Array containing slots
     * @return {array}       Slots array with dates converted to Date Object
     */
    convertSlotDates(slots) {
        return slots.map(slot => {
            slot.start = dateFns.parse(slot.start);
            slot.stop = dateFns.parse(slot.stop);

            return slot;
        });
    }

    /**
     * Renders React Component
     * @return {void}
     */
    init() {
        const domElements = document.getElementsByClassName('modularity-resource-booking-form');
        for (let i = 0; i < domElements.length; i++) {
            const element = domElements[i];
            ReactDOM.render(
                <BookingForm
                    translation={translation}
                    avalibleSlots={this.convertSlotDates(this.slots)}
                    mediaRequirements={this.mediaRequirements}
                    articleName={this.articleName}
                    articlePrice={this.articlePrice}
                    articleType={article_type}
                    articleId={parseInt(article_id)}
                    restNonce={order_nonce}
                />,
                element
            );
        }
    }
}

new App();
