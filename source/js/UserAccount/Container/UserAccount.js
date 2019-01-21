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

        const { translation } = this.props;

        const {notice, noticeType, lockInput} = this.state;

        let commonProps = {};

        if (lockInput) {
            commonProps.disabled = true;
        }

        return (
            <div>
                <form onSubmit={this.handleFormSubmit} className="grid u-p-2 u-pt-5">
                  
                    <div className="grid-xs-12 grid-md-6 u-mb-3">
                        <Input
                            type="text"
                            name="firstName"
                            value={firstName}
                            handleChange={this.handleInputChange}
                            label={translation.firstName}
                            placeholder={translation.firstName}
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
                            placeholder={translation.lastName}
                            label={translation.lastName}
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
                            label={translation.email}
                            placeholder={translation.email}
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
                            placeholder={translation.phoneNumber}
                            label={translation.phoneNumber}
                            {... commonProps}
                        />
                    </div>

                    <div className="grid-xs-12 grid-md-6 u-mb-3">
                        <Input
                            type="text"
                            name="website"
                            value={website}
                            handleChange={this.handleInputChange}
                            placeholder={translation.website}
                            label={translation.website}
                            {... commonProps}
                        />
                    </div>

                    <h4 className="u-m-2 u-mb-3 u-mt-4 u-p-0 u-pb-1 text-lg text-highlight u-border-bottom-2">{translation.headers.billing}</h4>
                    <div className="grid-xs-12 grid-md-6 u-mb-3">
                        <Input
                            type="text"
                            name="company"
                            value={company}
                            handleChange={this.handleInputChange}
                            placeholder={translation.company}
                            label={translation.company}
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
                            placeholder={translation.organizationNumber}
                            label={translation.organizationNumber}
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
                            placeholder={translation.contactPerson}
                            label={translation.contactPerson}
                            {... commonProps}
                        />
                    </div>

                    <div className="grid-xs-12 grid-md-6 u-mb-3">
                        <Textarea
                            type="text"
                            name="billingAddress"
                            value={billingAddress}
                            handleChange={this.handleInputChange}
                            placeholder={translation.billingAddress}
                            label={translation.billingAddress}
                            {... commonProps}
                        />
                    </div>

                    <div className="grid-xs-12 grid-md-6 u-mb-3">
                        <Input
                            type="text"
                            name="glnrNumber"
                            value={glnrNumber}
                            handleChange={this.handleInputChange}
                            placeholder={translation.glnrNumber}
                            label={translation.glnrNumber}
                            explainer={translation.explanation.glnr}
                            {... commonProps}
                        />
                    </div>

                    <div className="grid-xs-12 grid-md-6 u-mb-3">
                        <Input
                            type="text"
                            name="vatNumber"
                            value={vatNumber}
                            handleChange={this.handleInputChange}
                            placeholder={translation.vatNumber}
                            label={translation.vatNumber}
                            explainer={translation.explanation.vat}
                            {... commonProps}
                        />
                    </div>

                    <h4 className="u-m-2 u-mb-3 u-mt-4 u-p-0 u-pb-1 text-lg text-highlight u-border-bottom-2">{translation.headers.password} <span className="label label-sm label-theme pull-right">{translation.optional}</span></h4>
                    <div className="grid-xs-12 grid-md-6 u-mb-3">
                        <Input
                            type="password"
                            name="password"
                            value={password}
                            handleChange={this.handleInputChange}
                            placeholder={translation.password}
                            label={translation.password}
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
                            placeholder={translation.confirmPassword}
                            label={translation.confirmPassword}
                            minLength="6"
                            confirmField="password"
                            confirmFieldMessage={translation.passwordMisMatch}
                            {... commonProps}
                        />
                    </div>

                    <div className="grid-xs-12 u-mt-4">
                        <Button
                            color="primary"
                            title={translation.save}
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
