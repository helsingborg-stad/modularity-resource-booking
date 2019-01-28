import RegistrationForm from './Container/RegistrationForm';

const domElements = document.getElementsByClassName('modularity-registration-form');
const translation = modRegistrationForm;

for (let i = 0; i < domElements.length; i++) {
    const element = domElements[i];
    const { restUrl } = element.dataset;

    ReactDOM.render(<RegistrationForm translation={translation} restUrl={restUrl} />, element);
}
