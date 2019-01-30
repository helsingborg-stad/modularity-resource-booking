import BookingForm from './Container/BookingForm';
import { getArticle, getSlots } from '../Api/products';
import dateFns from 'date-fns';

const { translation } = modResourceBookingForm;

class App {
    constructor() {
        this.init();
    }

    /**
     * Renders React Component
     * @return {void}
     */
    init() {
        const domElements = document.getElementsByClassName('modularity-resource-booking-form');
        for (let i = 0; i < domElements.length; i++) {
            const element = domElements[i];

            if (!element.getAttribute('data-booking-form')) {
                continue;
            }

            const moduleData = JSON.parse(element.getAttribute('data-booking-form'));

            const requiredKeys = [
                'restUrl',
                'restNonce',
                'articleType',
                'articleId',
                'userId',
                'orderHistoryPage',
                'fileUploadTitle'
            ];
            const requiedKeysExists = requiredKeys.reduce((accumulator, key) => {
                if (typeof moduleData[key] === 'undefined') {
                    return false;
                }

                return accumulator;
            }, true);

            if (!requiedKeysExists) {
                continue;
            }

            ReactDOM.render(
                <BookingForm
                    translation={translation}
                    userId={moduleData['userId']}
                    articleType={moduleData['articleType']}
                    articleId={moduleData['articleId']}
                    restNonce={moduleData['restNonce']}
                    restUrl={moduleData['restUrl']}
                    orderHistoryPage={moduleData['orderHistoryPage']}
                    fileUploadTitle={moduleData['fileUploadTitle']}
                    locale={moduleData['locale']}
                />,
                element
            );
        }
    }
}

new App();
