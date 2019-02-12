import BookingForm from './Container/BookingForm';

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

            if (element.getAttribute('data-booking-form')) {
                const moduleData = JSON.parse(element.getAttribute('data-booking-form'));

                const requiredKeys = [
                    'restUrl',
                    'restNonce',
                    'articleType',
                    'articleId',
                    'userId',
                    'orderHistoryPage',
                    'headings',
                ];
                const requiedKeysExists = requiredKeys.reduce((accumulator, key) => {
                    if (typeof moduleData[key] === 'undefined') {
                        return false;
                    }

                    return accumulator;
                }, true);

                if (requiedKeysExists) {
                    ReactDOM.render(
                        <BookingForm
                            translation={translation}
                            userId={moduleData.userId}
                            articleType={moduleData.articleType}
                            articleId={moduleData.articleId}
                            restNonce={moduleData.restNonce}
                            restUrl={moduleData.restUrl}
                            orderHistoryPage={moduleData.orderHistoryPage}
                            headings={moduleData.headings}
                            locale={moduleData.locale}
                        />,
                        element
                    );
                }
            }
        }
    }
}

new App();
