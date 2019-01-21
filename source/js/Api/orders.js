const getCustomerOrders = (restUrl, nonce, data = [], page = 1) => {
    const completeUrl = restUrl + 'ModularityResourceBooking/v1/MyOrders?_wpnonce=' + nonce;

    return fetch(page ? completeUrl + '&page=' + page : completeUrl, {
        credentials: 'include',
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            throw Error(response.statusText);
        })
        .then(response => {
            const allData = data.concat(response);
            const nextPage = page + 1;

            if (response.length === 0) {
                return allData;
            }
            return getCustomerOrders(restUrl, nonce, allData, nextPage);
        });
};

const createOrder = (orders, files) => {
    const { restUrl, order_nonce } = modResourceBookingForm;

    let url = restUrl + 'ModularityResourceBooking/v1/CreateOrder';
    let formData = new FormData();

    orders.forEach((order, index) => {
        formData.append('order_articles[' + index + ']', JSON.stringify(order));
    });

    if (typeof files !== 'undefined' && files.length > 0) {
        files.forEach(media => {
            formData.append('files[]', media.file);
        });
    }

    let options = {
        method: 'POST',
        body: formData,
        headers: {
            'Content-Type': 'application/json',
            'X-WP-NONCE': order_nonce
        }
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

const postRequest = (restUrl, nonce) =>
    fetch(restUrl, {
        credentials: 'include',
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-WP-NONCE': nonce
        }
    }).then(response => {
        if (response.ok) {
            return response.json();
        }
        throw Error(response.statusText);
    });

export { getCustomerOrders, postRequest, createOrder };
