import {Button, Input, Textarea, Notice} from 'hbg-react';
import {createUser} from '../Api/users.js';

class RegistrationForm extends React.Component {
    constructor(props)
    {
        super(props);
        this.state = {
            //User input
            newUser: {
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

            //Account created
            accountCreated: false
        }

        this.handleInputChange = this.handleInputChange.bind(this);
        this.handleFormSubmit = this.handleFormSubmit.bind(this);
    }

    handleFormSubmit(e) {
        e.preventDefault();
        const {newUser} = this.state;

        createUser(newUser)
        .then((response) => {
            //Succesfully created user
            console.log(response);
        })
        .catch((error) => {
            //Failed to create user
            console.log(error);
        });
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
            companyNumber,
            billingAdress,
            website,
            contactPerson } = this.state.newUser;

        const {notice, noticeType, accountCreated} = this.state;

        return (
            <div>
                {!accountCreated &&
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
                                name="companyNumber"
                                value={companyNumber}
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
                }

                {notice.length > 0 &&
                    <div class="grid-xs-12">
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
