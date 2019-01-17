const createUser = (user) => {
    const {translation, restUrl} = modRegistrationForm;
    let url = restUrl + 'ModularityResourceBooking/v1/CreateUser';
    let formData = new FormData();

    const {email, firstName, lastName, company, companyNumber, password, phone, billingAdress, website, contactPerson, glnrNumber, vatNumber} = user;

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
    formData.append('billing_glnr_number', glnrNumber);
    formData.append('billing_vat_number', vatNumber);

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


const updateUser = (user) => {
    const {id, email, firstName, lastName, company, companyNumber, password, phone, billingAddress, website, contactPerson, glnrNumber, vatNumber} = user;
    const {translation, restUrl} = modUserAccount;

    let url = restUrl + 'ModularityResourceBooking/v1/ModifyUser/' + id;
    let formData = new FormData();

    formData.append('email', email);

    if (typeof(password) != 'undefined' && password.length > 0) {
        formData.append('password', password);
    }

    formData.append('first_name', firstName);
    formData.append('last_name', lastName);

    formData.append('phone', phone);
    formData.append('website', website);

    formData.append('billing_company', company);
    formData.append('billing_company_number', companyNumber);
    formData.append('billing_address', billingAddress);
    formData.append('billing_contact_person', contactPerson);
    formData.append('billing_glnr_number', glnrNumber);
    formData.append('billing_vat_number', vatNumber);

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
    createUser: createUser,
    updateUser: updateUser
};
