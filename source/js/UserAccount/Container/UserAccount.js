import {Button, Input, Textarea, Notice} from 'hbg-react';
import {updateUser} from '../../Api/user.js';

class UserAccount extends React.Component {
    constructor(props)
    {
        super(props);

        const {id, firstName, lastName, email, phone, website, company, companyNumber, billingAddress, contactPerson, vatNumber, glnrNumber} = props.user;

        this.state = {
            //User data
            user: {
                id: id,
                firstName: firstName,
                lastName: lastName,
                email: email,
                phone: phone,
                company: company,
                companyNumber: companyNumber,
                billingAddress: billingAddress,
                website: website,
                contactPerson: contactPerson,
                password: '',
                passwordConfirm: '',
                vatNumber: vatNumber,
                glnrNumber: glnrNumber
            },

            //Notice
            notice: '',
            noticeType: '',

            //Lock input
            lockInput: false
        }

        this.handleInputChange = this.handleInputChange.bind(this);
        this.handleFormSubmit = this.handleFormSubmit.bind(this);
    }

    handleFormSubmit(e) {
        e.preventDefault();
        const {user, lockInput} = this.state;

        if (lockInput) {
            return;
        }

        //Lock fields
        this.setState({
            lockInput: true
        });

        updateUser(user)
        .then((response) => {
            //Succesfully created user
            this.setState({
                lockInput: false,
                notice: response.message,
                noticeType: 'success',
            });
        })
        .catch((error) => {
            //Failed to create user
            this.setState({
                lockInput: false,
                notice: error.toString(),
                noticeType: 'warning'
            });
        });
    }

    handleInputChange(e)
    {
        let {name, value} = e.target;
        this.setState((state, props) => {
            let user = state.user;
            if (typeof(user[name]) != 'undefined') {
                user[name] = value;
            }

            return {user: user};
        });
    }

    render() {
        const {
            firstName,
            lastName,
            email, phone,
            company,
            companyNumber,
            billingAddress,
            website,
            contactPerson,
            password, passwordConfirm,
            vatNumber,
            glnrNumber } = this.state.user;

        const {notice, noticeType, lockInput} = this.state;

        let commonProps = {};

        if (lockInput) {
            commonProps.disabled = true;
        }

        return (
            <div>
                <form onSubmit={this.handleFormSubmit} className="grid u-mt-2">
                    <h4 className="u-mb-2">General</h4>
                    <div className="grid-xs-12 grid-md-6 u-mb-3">
                        <Input
                            type="text"
                            name="firstName"
                            value={firstName}
                            handleChange={this.handleInputChange}
                            label="First name"
                            required
                            {... commonProps}
                        />
                    </div>
                    <div className="grid-xs-12 grid-md-6 u-mb-3">
                        <Input
                            type="text"
                            name="lastName"
                            value={lastName}
                            handleChange={this.handleInputChange}
                            label="Last name"
                            required
                            {... commonProps}
                        />
                    </div>

                    <div className="grid-xs-12 grid-md-6 u-mb-3">
                        <Input
                            type="email"
                            name="email"
                            value={email}
                            handleChange={this.handleInputChange}
                            label="Email"
                            required
                            {... commonProps}
                        />
                    </div>

                    <div className="grid-xs-12 grid-md-6 u-mb-3">
                        <Input
                            type="tel"
                            name="phone"
                            value={phone}
                            handleChange={this.handleInputChange}
                            label="Phone number"
                            {... commonProps}
                        />
                    </div>

                    <div className="grid-xs-12 grid-md-6 u-mb-3">
                        <Input
                            type="text"
                            name="website"
                            value={website}
                            handleChange={this.handleInputChange}
                            label="Website"
                            {... commonProps}
                        />
                    </div>

                    <h4 className="u-mb-2">Billing</h4>
                    <div className="grid-xs-12 grid-md-6 u-mb-3">
                        <Input
                            type="text"
                            name="company"
                            value={company}
                            handleChange={this.handleInputChange}
                            label="Company"
                            required
                            {... commonProps}
                        />
                    </div>

                    <div className="grid-xs-12 grid-md-6 u-mb-3">
                        <Input
                            type="text"
                            name="companyNumber"
                            value={companyNumber}
                            handleChange={this.handleInputChange}
                            label="Organization number"
                            required
                            {... commonProps}
                        />
                    </div>

                    <div className="grid-xs-12 grid-md-6 u-mb-3">
                        <Input
                            type="text"
                            name="contactPerson"
                            value={contactPerson}
                            handleChange={this.handleInputChange}
                            label="Contact Person"
                            {... commonProps}
                        />
                    </div>

                    <div className="grid-xs-12 grid-md-6 u-mb-3">
                        <Textarea
                            type="text"
                            name="billingAddress"
                            value={billingAddress}
                            handleChange={this.handleInputChange}
                            label="Billing Address"
                            {... commonProps}
                        />
                    </div>

                    <div className="grid-xs-12 grid-md-6 u-mb-3">
                        <Input
                            type="text"
                            name="glnrNumber"
                            value={glnrNumber}
                            handleChange={this.handleInputChange}
                            placeholder="Glnr number"
                            {... commonProps}
                        />
                    </div>

                    <div className="grid-xs-12 grid-md-6 u-mb-3">
                        <Input
                            type="text"
                            name="vatNumber"
                            value={vatNumber}
                            handleChange={this.handleInputChange}
                            placeholder="VAT number"
                            {... commonProps}
                        />
                    </div>

                    <h4 className="u-mb-2">Change Password (optional)</h4>
                    <div className="grid-xs-12 grid-md-6 u-mb-3">
                        <Input
                            type="password"
                            name="password"
                            value={password}
                            handleChange={this.handleInputChange}
                            label="Password"
                            minLength="6"
                            {... commonProps}
                        />
                    </div>

                    <div className="grid-xs-12 grid-md-6 u-mb-3">
                        <Input
                            type="password"
                            name="passwordConfirm"
                            value={passwordConfirm}
                            handleChange={this.handleInputChange}
                            label="Confirm password"
                            minLength="6"
                            confirmField="password"
                            confirmFieldMessage="The password does not match."
                            {... commonProps}
                        />
                    </div>

                    <div className="grid-xs-12">
                        <Button
                            color="primary"
                            title="Save"
                            submit
                            {... commonProps}
                        />
                    </div>
                </form>

                {notice.length > 0 &&
                    <div className="u-mt-2">
                        <Notice type={noticeType} icon>
                            {notice}
                        </Notice>
                    </div>
                }
            </div>
        );
    }
}

export default UserAccount;
