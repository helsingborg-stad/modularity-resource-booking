import {Button, Input, Textarea, Notice} from 'hbg-react';

class UserAccount extends React.Component {
    constructor(props)
    {
        super(props);

        this.state = {
            //User input
            user: {
                firstName: '',
                lastName: '',
                email: '',
                phone: '',
                company: '',
                companyNumber: '',
                billingAdress: '',
                website: '',
                contactPerson: ''
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

        // this.setState({lockInput: true, notice: ''});
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
            billingAdress,
            website,
            contactPerson } = this.state.user;

        const {notice, noticeType, accountCreated, lockInput} = this.state;

        let commonProps = {};

        if (lockInput) {
            commonProps.disabled = true;
        }

        return (
            <div>
                {!accountCreated &&
                    <form onSubmit={this.handleFormSubmit} className="grid u-mt-2">
                        <div className="grid-xs-12 grid-md-6 u-mb-3">
                            <Input
                                type="text"
                                name="firstName"
                                value={firstName}
                                handleChange={this.handleInputChange}
                                placeholder="First name"
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
                                placeholder="Last name"
                                required
                                {... commonProps}
                            />
                        </div>

                        <div className="grid-xs-12 grid-md-6 u-mb-3">
                            <Input
                                type="text"
                                name="company"
                                value={company}
                                handleChange={this.handleInputChange}
                                placeholder="Company"
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
                                placeholder="Organization number"
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
                                placeholder="Email"
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
                                placeholder="Contact Person"
                                {... commonProps}
                            />
                        </div>
                        <div className="grid-xs-12 grid-md-6 u-mb-3">
                            <Input
                                type="tel"
                                name="phone"
                                value={phone}
                                handleChange={this.handleInputChange}
                                placeholder="Phone number"
                                {... commonProps}
                            />
                        </div>
                        <div className="grid-xs-12 grid-md-6 u-mb-3">
                            <Input
                                type="text"
                                name="website"
                                value={website}
                                handleChange={this.handleInputChange}
                                placeholder="Website"
                                {... commonProps}
                            />
                        </div>
                        <div className="grid-xs-12 grid-md-6 u-mb-3">
                            <Textarea
                                type="text"
                                name="billingAdress"
                                value={billingAdress}
                                handleChange={this.handleInputChange}
                                placeholder="Billing Address"
                                {... commonProps}
                            />
                        </div>
                        <div className="grid-xs-12">
                            <Button
                                color="primary"
                                title="Submit"
                                submit
                                {... commonProps}
                            />
                        </div>
                    </form>
                }

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

export default RegistrationForm;
