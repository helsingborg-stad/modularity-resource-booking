const getCustomerOrders = () => {
    const {restUrl} = modOrderHistory;
    const url = restUrl + 'ModularityResourceBooking/v1/MyOrders';

    return fetch(url)
        .then(res => res.json())
        .then(
            (result) => ({result}),
            (error) => ({error})
        );
};

export {getCustomerOrders};
