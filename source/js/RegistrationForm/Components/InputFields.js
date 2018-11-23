import {Button} from 'hbg-react';

const InputFields = ({translation, createAccount}) =>
    <form onSubmit={createAccount}>
        <div className="grid gutter">
            <div className="grid-xs-12 grid-md-6 u-mb-2">
                <label htmlFor="first-name">First name</label>
                <input type="text" name="first-name" placeholder="Last name"/>
            </div>
            <div className="grid-xs-12 grid-md-6 u-mb-2">
                <label htmlFor="last-name">Last name</label>
                <input type="text" name="last-name" placeholder="Last name"/>
            </div>
            <div className="grid-xs-12 grid-md-6 u-mb-2">
                <label htmlFor="email">Email</label>
                <input type="email" name="email" placeholder="Email"/>
            </div>
            <div className="grid-xs-12 grid-md-6 u-mb-2">
                <label htmlFor="company">Company</label>
                <input type="text" name="company" placeholder="Company"/>
            </div>
            <div className="grid-xs-12 grid-md-6 u-mb-2">
                <label htmlFor="password">Password</label>
                <input type="password" name="password" placeholder="Password"/>
            </div>
            <div className="grid-xs-12 grid-md-6 u-mb-2">
                <label htmlFor="confirm-password">Confirm password</label>
                <input type="password" name="confirm-password" placeholder="Confirm password"/>
            </div>
            <div className="grid-xs-12 u-mt-2">
                <Button title="Create account" color="primary" submit block/>
            </div>
        </div>
    </form>;

export default InputFields;