import '@babel/polyfill';
import Form from './Components/Form';

const domElements = document.getElementsByClassName("modularity-registration-form");
const {translation} = modRegistrationForm;

for (let i = 0; i < domElements.length; i++) {
    const element = domElements[i];
    ReactDOM.render(
        <Form
            translation={translation}
        />,
        element
    );
}