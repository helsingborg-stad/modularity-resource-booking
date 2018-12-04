import '@babel/polyfill';
import UserAccount from './Container/UserAccount';

const domElements = document.getElementsByClassName("modularity-user-account");
const {translation} = modUserAccount;

for (let i = 0; i < domElements.length; i++) {
    const element = domElements[i];
    ReactDOM.render(
        <UserAccount
            translation={translation}
        />,
        element
    );
}
