const getCustomerOrders = (restUrl, nonce, data = [], page = 1) => {
    const completeUrl = `${restUrl}ModularityResourceBooking/v1/MyOrders?_wpnonce=${nonce}`;

    return fetch(page ? `${completeUrl}&page=${page}` : completeUrl, {
        credentials: 'include',
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
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

const createOrder = (orderTitle, orders, files, restUrl, restNonce) => {
    const url = `${restUrl}ModularityResourceBooking/v1/CreateOrder`;
    const formData = new FormData();

    formData.append('order_title', orderTitle);

    orders.forEach((order, index) => {
        formData.append(`order_articles[${index}]`, JSON.stringify(order));
    });

    if (typeof files !== 'undefined' && files.length > 0) {
        files.forEach((media, index) => {
            formData.append(`files_${index}`, media.file);
        });
    }

    const options = {
        method: 'POST',
        body: formData,
        headers: {
            'X-WP-NONCE': restNonce,
        },
    };

    return fetch(url, options)
        .then(response => {
            return response.json();
        })
        .then(response => {
            if (response.state === 'error') {
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
            'X-WP-NONCE': nonce,
        },
    }).then(response => {
        if (response.ok) {
            return response.json();
        }
        throw Error(response.statusText);
    });

export { getCustomerOrders, postRequest, createOrder };
