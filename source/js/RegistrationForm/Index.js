import RegistrationForm from './Container/RegistrationForm';

const domElements = document.getElementsByClassName('modularity-registration-form');
const { translation } = modRegistrationForm;

for (let i = 0; i < domElements.length; i++) {
    const element = domElements[i];
    ReactDOM.render(<RegistrationForm translation={translation} />, element);
}
