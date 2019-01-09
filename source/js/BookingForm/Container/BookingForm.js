import { Button, Calendar } from 'hbg-react';

class BookingForm extends React.Component {
    constructor(props) {
        super(props);
        this.state = {};
    }

    render() {
        return (
            <div>
                <Calendar />
            </div>
        );
    }
}

export default BookingForm;
