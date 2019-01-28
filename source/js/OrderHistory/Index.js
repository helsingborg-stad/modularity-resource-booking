import OrderHistory from './Container/OrderHistory';

const domElements = document.getElementsByClassName('modularity-order-history');
const translation = modOrderHistory;

for (let i = 0; i < domElements.length; i++) {
    const element = domElements[i];
    const { nonce, restUrl } = element.dataset;

    ReactDOM.render(
        <OrderHistory translation={translation} perPage={10} restUrl={restUrl} nonce={nonce} />,
        element
    );
}
