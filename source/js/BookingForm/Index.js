import BookingForm from './Container/BookingForm';
import { getArticle, getSlots } from '../Api/products';
import dateFns from 'date-fns';

const {
    translation,
    article_type,
    article_id,
    user_id,
    order_nonce
} = modResourceBookingForm;

let avalibleSlots = [],
    disabledSlots = [];

class App {
    constructor() {
        this.articleName = '';
        this.articlePrice = 0;
        this.mediaRequirements = [];
        this.slots = [];

        this.fetchData();
    }

    /**
     * Fetch article data and slots
     * @return {void}
     */
    fetchData() {
        getArticle(article_id, article_type)
            .then(article => {
                this.articlePrice = article[0].price;
                this.mediaRequirements = article[0].media_requirements.map(
                    mediaObject => {
                        let media = mediaObject;
                        media.file = null;

                        return media;
                    }
                );
                this.articleName = article[0].title;

                getSlots(article_id, article_type, user_id)
                    .then(slots => {
                        this.slots = slots.map(slotData => {
                            let slot = slotData;
                            slot.start = dateFns.parse(slotData.start);
                            slot.stop = dateFns.parse(slotData.stop);
                            slot.articleName = this.articleName;
                            slot.articlePrice = this.articlePrice;

                            slotData.title =
                                this.articleName +
                                ' (' +
                                dateFns.format(slot.start, 'DD-MM-YYYY') +
                                ' - ' +
                                dateFns.format(slot.stop, 'DD-MM-YYYY') +
                                ')';

                            return slot;
                        });

                        this.init();
                    })
                    .catch(result => {
                        console.log('Failed to fetch slots');
                    });
            })
            .catch(result => {
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
        const domElements = document.getElementsByClassName(
            'modularity-resource-booking-form'
        );
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
                    articleId={article_id}
                    restNonce={order_nonce}
                />,
                element
            );
        }
    }
}

new App();
