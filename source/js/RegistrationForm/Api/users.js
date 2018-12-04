//Localized var
const {translation, restUrl} = modRegistrationForm;

const createUser = (user) => {
    let url = restUrl + 'ModularityResourceBooking/v1/CreateUser';
    let formData = new FormData();

    const {email, firstName, lastName, company, companyNumber} = user;

    formData.append('email', email);
    formData.append('company', company);
    formData.append('first_name', firstName);
    formData.append('last_name', lastName);
    formData.append('corporate_number', companyNumber);
    formData.append('password', '123');

    let options = {
        method: 'POST',
        body: formData
    };

    return fetch(url, options)
    .then((response) => {
        return response.json();
    })
    .then((response) => {
        if (response.state == 'error') {
            throw new Error(response.message);
        }

        return response;
    });
};

module.exports = {
    createUser: createUser
};
