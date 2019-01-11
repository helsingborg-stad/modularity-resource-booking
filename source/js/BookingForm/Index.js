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
        this.price = 0;
        this.avalibleSlots = [];
        this.disabledSlots = [];
        this.mediaRequirements = [];

        this.fetchData();
    }

    /**
     * Fetch article data and slots
     * @return {void}
     */
    fetchData() {
        getArticle(article_id, article_type)
            .then(article => {
                this.price = article[0].price;
                this.mediaRequirements = article[0].media_requirements;
                this.articleName = article[0].title;

                getSlots(article_id, article_type, user_id)
                    .then(slots => {
                        this.avalibleSlots = slots.filter(
                            slot =>
                                slot.available_stock > 0 &&
                                !slot.unlimited_stock
                        );

                        this.disabledSlots = slots.filter(
                            slot =>
                                slot.available_stock == 0 ||
                                slot.unlimited_stock
                        );

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
                    price={this.price}
                    avalibleSlots={this.convertSlotDates(this.avalibleSlots)}
                    disabledSlots={this.convertSlotDates(this.disabledSlots)}
                    mediaRequirements={this.mediaRequirements}
                />,
                element
            );
        }
    }
}

new App();
