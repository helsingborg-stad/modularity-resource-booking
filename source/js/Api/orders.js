const getCustomerOrders = () => {
    let { restUrl, nonce } = modOrderHistory;
    restUrl += 'ModularityResourceBooking/v1/MyOrders?_wpnonce=' + nonce;

    return fetch(restUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP error, status = ' + response.status);
            }
            return response.json();
        })
        .then(result => ({ result }), error => ({ error }))
        .catch(e => console.log('Request went wrong.'));
};

export { getCustomerOrders };
