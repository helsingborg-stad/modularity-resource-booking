const getCustomerOrders = (restUrl, nonce, data = [], page = 1) => {
    const completeUrl = restUrl + 'ModularityResourceBooking/v1/MyOrders?_wpnonce=' + nonce;

    return fetch(page ? completeUrl + '&page=' + page : completeUrl, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP error, status = ' + response.status);
            }
            return response.json();
        })
        .then(result => {
            const allData = data.concat(result);
            const nextPage = page + 1;

            if (result.length === 0) {
                return allData;
            }
            return getCustomerOrders(restUrl, nonce, allData, nextPage);
        })
        .catch(error => ({ error }));
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
