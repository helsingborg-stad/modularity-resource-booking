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

    formData.append('phone', phone);
    formData.append('website', website);

    formData.append('billing_company', company);
    formData.append('billing_company_number', companyNumber);
    formData.append('billing_address', billingAdress);
    formData.append('billing_contact_person', contactPerson);

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
