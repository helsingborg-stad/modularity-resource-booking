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

const createOrder = (orders, files) => {
    const { restUrl, order_nonce } = modResourceBookingForm;

    let url = restUrl + 'ModularityResourceBooking/v1/CreateOrder';
    let formData = new FormData();

    orders.forEach((order, index) => {
        formData.append('order_articles[' + index + ']', JSON.stringify(order));
    });

    files.forEach((media, index) => {
        formData.append('file[' + index + ']', media.file);
    });

    formData.append('_wpnonce', order_nonce);

    let options = {
        method: 'POST',
        body: formData
    };

    console.log(order_nonce);
    console.log(formData);

    return fetch(url, options)
        .then(response => {
            return response.json();
        })
        .then(response => {
            if (response.state == 'error') {
                throw new Error(response.message);
            }

            return response;
        });
};

export { getCustomerOrders, createOrder };
