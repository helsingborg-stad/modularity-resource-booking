const getCustomerOrders = () => {
    const { restUrl, nonce } = modOrderHistory;
    const url =
        restUrl + 'ModularityResourceBooking/v1/MyOrders?nonce=' + nonce;

    return fetch(url)
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
