const getCustomerOrders = (data, restUrl, page = 1) =>
    fetch(page ? restUrl + '&page=' + page : restUrl, {
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
            return getCustomerOrders(allData, restUrl, nextPage);
        })
        .catch(error => ({ error }));
export { getCustomerOrders };
