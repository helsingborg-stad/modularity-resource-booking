import UserAccount from './Container/UserAccount';

const domElements = document.getElementsByClassName('modularity-user-account');
const translation = modUserAccount;

for (let i = 0; i < domElements.length; i++) {
    const element = domElements[i];
    const { user, restUrl, nonce } = element.dataset;

    ReactDOM.render(
        <UserAccount
            translation={translation}
            user={JSON.parse(user)}
            restUrl={restUrl}
            nonce={nonce}
        />,
        element
    );
}
