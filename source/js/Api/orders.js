const getCustomerOrders = (restUrl, nonce, data = [], page = 1) => {
    const completeUrl = restUrl + 'ModularityResourceBooking/v1/MyOrders?_wpnonce=' + nonce;

    return fetch(page ? completeUrl + '&page=' + page : completeUrl, {
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

export { getCustomerOrders, postRequest };
