import { Button, Input, Textarea, Notice } from 'hbg-react';
import ReCAPTCHA from 'react-google-recaptcha';
import { createUser } from '../../Api/user';
import Select from '../components/Select';

class RegistrationForm extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            // User input
            newUser: {
                firstName: '',
                lastName: '',
                email: '',
                emailConfirm: '',
                password: '',
                passwordConfirm: '',
                phone: '',
                company: '',
                companyNumber: '',
                billingAdress: '',
                website: '',
                contactPerson: '',
                vatNumber: '',
                glnrNumber: '',
                organisationType: '',
            },

            // Notice
            notice: '',
            noticeType: '',

            // Account created
            accountCreated: false,

            // Lock input
            lockInput: false,

            reCaptchaVerified: false,
            reCaptchaErrorMessage: false,
        };

        this.handleInputChange = this.handleInputChange.bind(this);
        this.handleFormSubmit = this.handleFormSubmit.bind(this);
    }

    handleFormSubmit(e) {
        e.preventDefault();
        const { newUser, lockInput, reCaptchaVerified, reCaptchaErrorMessage } = this.state;
        const { restUrl } = this.props;

        if (lockInput) {
            return;
        }

        // Reset error message
        if (reCaptchaErrorMessage) {
            this.setState({ reCaptchaErrorMessage: false });
        }

        // Recaptcha
        if (!reCaptchaVerified) {
            this.setState({ reCaptchaErrorMessage: true });
            return;
        }

        this.setState({ lockInput: true, notice: '' });

        createUser(newUser, restUrl)
            .then(response => {
                // Succesfully created user
                this.setState({
                    lockInput: true,
                    notice: response.message,
                    noticeType: 'success',
                    accountCreated: true,
                    newUser: {
                        firstName: '',
                        lastName: '',
                        email: '',
                        emailConfirm: '',
                        password: '',
                        passwordConfirm: '',
                        phone: '',
                        company: '',
                        companyNumber: '',
                        billingAdress: '',
                        website: '',
                        contactPerson: '',
                        vatNumber: '',
                        glnrNumber: '',
                    },
                });
            })
            .catch(error => {
                // Failed to create user
                this.setState({
                    lockInput: false,
                    notice: error.message,
                    noticeType: 'warning',
                });
            });
    }

    handleInputChange(e) {
        const { name, value } = e.target;
        this.setState((state, props) => {
            const user = state.newUser;
            if (typeof user[name] !== 'undefined') {
                user[name] = value;
            }

            return { newUser: user };
        });
    }

    render() {
        const {
            firstName,
            lastName,
            email,
            emailConfirm,
            password,
            passwordConfirm,
            phone,
            company,
            companyNumber,
            billingAdress,
            website,
            contactPerson,
            vatNumber,
            glnrNumber,
            organisationType,
        } = this.state.newUser;

        const { translation, organisationTypes } = this.props;
        const labelPrefix = 'registration_form_';

        const { notice, noticeType, accountCreated, lockInput, reCaptchaErrorMessage } = this.state;

        const commonProps = {};

        if (lockInput) {
            commonProps.disabled = true;
        }

        return (
            <div>
                {!accountCreated && (
                    <form
                        onSubmit={this.handleFormSubmit}
                        className="grid u-p-2 u-pt-5 form-notice-fix"
                    >
                        <div className="grid-xs-12 grid-md-6 u-mb-3">
                            <Input
                                id={`${labelPrefix}first_name`}
                                type="text"
                                name="firstName"
                                value={firstName}
                                handleChange={this.handleInputChange}
                                placeholder={translation.firstName}
                                label={`${translation.firstName} *`}
                                required
                                {...commonProps}
                            />
                        </div>

                        <div className="grid-xs-12 grid-md-6 u-mb-3">
                            <Input
                                id={`${labelPrefix}last_name`}
                                type="text"
                                name="lastName"
                                value={lastName}
                                handleChange={this.handleInputChange}
                                placeholder={translation.lastName}
                                label={`${translation.lastName} *`}
                                required
                                {...commonProps}
                            />
                        </div>

                        <div className="grid-xs-12 grid-md-6 u-mb-3">
                            <Input
                                id={`${labelPrefix}email`}
                                type="email"
                                name="email"
                                value={email}
                                handleChange={this.handleInputChange}
                                label={`${translation.email} *`}
                                placeholder={translation.email}
                                required
                                {...commonProps}
                            />
                        </div>

                        <div className="grid-xs-12 grid-md-6 u-mb-3">
                            <Input
                                id={`${labelPrefix}confirm_email`}
                                type="email"
                                name="emailConfirm"
                                value={emailConfirm}
                                handleChange={this.handleInputChange}
                                placeholder={translation.confirmEmail}
                                label={`${translation.confirmEmail} *`}
                                confirmField="email"
                                confirmFieldMessage={translation.emailMisMatch}
                                required
                                {...commonProps}
                            />
                        </div>

                        <div className="grid-xs-12 grid-md-6 u-mb-3">
                            <Input
                                id={`${labelPrefix}phone`}
                                type="tel"
                                name="phone"
                                value={phone}
                                handleChange={this.handleInputChange}
                                placeholder={translation.phoneNumber}
                                label={translation.phoneNumber}
                                {...commonProps}
                            />
                        </div>

                        <div className="grid-xs-12 grid-md-6 u-mb-3">
                            <Input
                                id={`${labelPrefix}website`}
                                type="url"
                                name="website"
                                value={website}
                                handleChange={this.handleInputChange}
                                placeholder={translation.website}
                                label={translation.website}
                                {...commonProps}
                            />
                        </div>

                        <h4 className="u-m-2 u-mb-3 u-mt-4 u-p-0 u-pb-1 text-lg text-highlight u-border-bottom-2">
                            {translation.headers.password}
                        </h4>
                        <div className="grid-xs-12 grid-md-6 u-mb-3">
                            <Input
                                id={`${labelPrefix}password`}
                                type="password"
                                name="password"
                                value={password}
                                handleChange={this.handleInputChange}
                                placeholder={translation.password}
                                label={`${translation.password} *`}
                                minLength="6"
                                required
                                {...commonProps}
                            />
                        </div>

                        <div className="grid-xs-12 grid-md-6 u-mb-3">
                            <Input
                                id={`${labelPrefix}confirm_password`}
                                type="password"
                                name="passwordConfirm"
                                value={passwordConfirm}
                                handleChange={this.handleInputChange}
                                placeholder={translation.confirmPassword}
                                label={`${translation.confirmPassword} *`}
                                minLength="6"
                                confirmField="password"
                                confirmFieldMessage={translation.passwordMisMatch}
                                required
                                {...commonProps}
                            />
                        </div>

                        <h4 className="u-m-2 u-mb-3 u-mt-4 u-p-0 u-pb-1 text-lg text-highlight u-border-bottom-2">
                            {translation.headers.billing}
                        </h4>
                        <div className="grid-xs-12 grid-md-6 u-mb-3">
                            <Input
                                id={`${labelPrefix}company`}
                                type="text"
                                name="company"
                                value={company}
                                handleChange={this.handleInputChange}
                                placeholder={translation.company}
                                label={`${translation.company} *`}
                                required
                                {...commonProps}
                            />
                        </div>

                        <div className="grid-xs-12 grid-md-6 u-mb-3">
                            <Input
                                id={`${labelPrefix}organization_number`}
                                type="text"
                                name="companyNumber"
                                value={companyNumber}
                                handleChange={this.handleInputChange}
                                placeholder={translation.organizationNumber}
                                label={`${translation.organizationNumber} *`}
                                required
                                {...commonProps}
                            />
                        </div>

                        {typeof organisationTypes !== 'undefined' && organisationTypes.length > 0 && (
                            <div className="grid-xs-12 grid-md-6 u-mb-3">
                                <Select
                                    name="organisationType"
                                    value={organisationType}
                                    onChange={this.handleInputChange}
                                    label={`${translation.iAm}.. *`}
                                    placeholder={`${translation.selectCompanyType}`}
                                    required
                                    {...commonProps}
                                >
                                    {organisationTypes.map(option => (
                                        <option value={option.value}>{option.label}</option>
                                    ))}
                                </Select>
                            </div>
                        )}

                        <div className="grid-xs-12 grid-md-6 u-mb-3">
                            <Input
                                id={`${labelPrefix}contact_person`}
                                type="text"
                                name="contactPerson"
                                value={contactPerson}
                                handleChange={this.handleInputChange}
                                placeholder={translation.contactPerson}
                                label={translation.contactPerson}
                                {...commonProps}
                            />
                        </div>

                        <div className="grid-xs-12 grid-md-6 u-mb-3">
                            <Input
                                id={`${labelPrefix}vat_number`}
                                type="text"
                                name="vatNumber"
                                value={vatNumber}
                                handleChange={this.handleInputChange}
                                placeholder={translation.vatNumber}
                                label={`${translation.vatNumber} *`}
                                explainer={translation.explanation.vat}
                                required
                                {...commonProps}
                            />
                        </div>

                        <div className="grid-xs-12 grid-md-6 u-mb-3">
                            <Input
                                id={`${labelPrefix}glnr_number`}
                                type="text"
                                name="glnrNumber"
                                value={glnrNumber}
                                handleChange={this.handleInputChange}
                                placeholder={translation.glnrNumber}
                                label={translation.glnrNumber}
                                explainer={translation.explanation.glnr}
                                {...commonProps}
                            />
                        </div>

                        <div className="grid-xs-12 grid-md-12 u-mb-3">
                            <Textarea
                                id={`${labelPrefix}billing_address`}
                                type="text"
                                name="billingAdress"
                                value={billingAdress}
                                handleChange={this.handleInputChange}
                                placeholder={translation.billingAddress}
                                label={`${translation.billingAddress} *`}
                                required
                                {...commonProps}
                            />
                        </div>

                        <div className="grid-xs-12 grid-md-12 u-mb-3">
                            <ReCAPTCHA
                                sitekey="6LewZZYUAAAAAO_oT5zQQ0zjO2IsWY_2w3QbyTIF"
                                onChange={() => {
                                    this.setState({ reCaptchaVerified: true });
                                }}
                            />
                            {reCaptchaErrorMessage && (
                                <span className="text-sm text-danger">
                                    Please verify that you are a human
                                </span>
                            )}
                        </div>

                        <div className="grid-xs-12">
                            <Button
                                color="primary"
                                title={translation.register}
                                submit
                                {...commonProps}
                            />
                        </div>
                    </form>
                )}

                {notice.length > 0 && (
                    <div className="u-p-2">
                        <Notice type={noticeType} icon>
                            {notice}
                        </Notice>
                    </div>
                )}
            </div>
        );
    }
}

export default RegistrationForm;
