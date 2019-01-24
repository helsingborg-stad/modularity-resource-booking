const mapData = () => {
    const { restUrl, packId } = modPackageMap;
    const url = restUrl + 'ModularityResourceBooking/v1/PackagePins/' + packageId;
    const options = {
        method: 'GET',
    };

    const Apidata = fetch(url, options)
        .then(response => {
            return response.json();
        })
        .then(response => {
            if (response.state === 'error') {
                throw new Error(response.message);
            }
            return response;
        });

    return Apidata;
};

module.exports = {
    mapData: mapData,
};
