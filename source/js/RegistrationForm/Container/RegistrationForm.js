import {Button, Input, Textarea} from 'hbg-react';

class RegistrationForm extends React.Component {
    constructor(props)
    {
        super(props);
        this.state = {
            newUser: {
                firstName: '',
                lastName: '',
                email: '',
                phone: '',
                company: '',
                orgNumber: '',
                billingAdress: '',
                website: '',
                contactPerson: ''
            }
        }

        this.handleInputChange = this.handleInputChange.bind(this);
        this.handleFormSubmit = this.handleFormSubmit.bind(this);
    }

    handleFormSubmit(e) {
        e.preventDefault();
        console.log(this.state.newUser);
    }

    handleInputChange(e)
    {
        let {name, value} = e.target;
        this.setState((state, props) => {
            let user = state.newUser;
            if (typeof(user[name]) != 'undefined') {
                user[name] = value;
            }

            return {newUser: user};
        });
    }

    render() {
        const {
            firstName,
            lastName,
            email, phone,
            company,
            orgNumber,
            billingAdress,
            website,
            contactPerson } = this.state.newUser;

        return (
            <form onSubmit={this.handleFormSubmit} className="grid">
                <div className="grid-xs-6 u-mb-3">
                    <Input
                        type="text"
                        name="firstName"
                        value={firstName}
                        handleChange={this.handleInputChange}
                        placeholder="First name"
                        required
                    />
                </div>
                <div className="grid-xs-6 u-mb-3">
                    <Input
                        type="text"
                        name="lastName"
                        value={lastName}
                        handleChange={this.handleInputChange}
                        placeholder="Last name"
                        required
                    />
                </div>

                <div className="grid-xs-6 u-mb-3">
                    <Input
                        type="text"
                        name="company"
                        value={company}
                        handleChange={this.handleInputChange}
                        placeholder="Company"
                        required
                    />
                </div>

                <div className="grid-xs-6 u-mb-3">
                    <Input
                        type="text"
                        name="orgNumber"
                        value={orgNumber}
                        handleChange={this.handleInputChange}
                        placeholder="Organization number"
                        required
                    />
                </div>

                <div className="grid-xs-6 u-mb-3">
                    <Input
                        type="text"
                        name="email"
                        value={email}
                        handleChange={this.handleInputChange}
                        placeholder="Email"
                        required
                    />
                </div>
                <div className="grid-xs-6 u-mb-3">
                    <Input
                        type="text"
                        name="contactPerson"
                        value={contactPerson}
                        handleChange={this.handleInputChange}
                        placeholder="Contact Person"
                    />
                </div>
                <div className="grid-xs-6 u-mb-3">
                    <Input
                        type="text"
                        name="phone"
                        value={phone}
                        handleChange={this.handleInputChange}
                        placeholder="Phone number"
                    />
                </div>
                <div className="grid-xs-6 u-mb-3">
                    <Input
                        type="text"
                        name="website"
                        value={website}
                        handleChange={this.handleInputChange}
                        placeholder="Website"
                    />
                </div>
                <div className="grid-xs-6 u-mb-3">
                    <Textarea
                        type="text"
                        name="billingAdress"
                        value={billingAdress}
                        handleChange={this.handleInputChange}
                        placeholder="Billing Address"
                    />
                </div>
                <div className="grid-xs-12">
                    <Button
                        color="primary"
                        title="Submit"
                        submit
                    />
                </div>
            </form>
        );
    }
}

export default RegistrationForm;
