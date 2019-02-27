import RegistrationForm from './Container/RegistrationForm';

const domElements = document.getElementsByClassName('modularity-registration-form');
const translation = modRegistrationForm;

for (let i = 0; i < domElements.length; i++) {
    const element = domElements[i];
    const { restUrl } = element.dataset;
    let { customerGroups } = element.dataset;

    customerGroups = JSON.parse(customerGroups);
    if (customerGroups.length > 0) {
        customerGroups = customerGroups.map(group => {
            return {
                label: group.name,
                value: group.id,
            };
        });
    }

    ReactDOM.render(
        <RegistrationForm
            translation={translation}
            restUrl={restUrl}
            organisationTypes={customerGroups}
        />,
        element
    );
}
