import InputFields from './InputFields';

class RegistrationForm extends React.Component {
    handleSubmit = (e) => {
        e.preventDefault();
        console.log("Submit");
    };

    render() {
        const {translation} = this.props;

        return (
            <InputFields
                translation={translation}
                createAccount={this.handleSubmit}
            />
        );
    }
}

export default RegistrationForm;