//Localized var
const {translation, restUrl} = modRegistrationForm;

const createUser = (user) => {
    let url = restUrl + 'ModularityResourceBooking/v1/CreateUser';
    let formData = new FormData();

    const {email, firstName, lastName, company, companyNumber, password, phone, billingAdress, website, contactPerson} = user;

    formData.append('email', email);
    formData.append('password', password);

    formData.append('first_name', firstName);
    formData.append('last_name', lastName);

    formData.append('company', company);
    formData.append('company_number', companyNumber);

    formData.append('phone', phone);
    formData.append('billing_address', billingAdress);
    formData.append('contact_person', contactPerson);
    formData.append('website', website);

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
