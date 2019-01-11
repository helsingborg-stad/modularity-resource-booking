import { Button, Calendar } from 'hbg-react';
import { getArticle } from '../../Api/products';
import PropTypes from 'prop-types';

class BookingForm extends React.Component {
    static propTypes = {
        price: PropTypes.oneOfType([PropTypes.string, PropTypes.number])
            .isRequired,
        avalibleSlots: PropTypes.array.isRequired,
        disabledSlots: PropTypes.array.isRequired,
        mediaRequirements: PropTypes.array
    };

    constructor(props) {
        super(props);
        this.state = {
            selectedDates: []
        };

        this.handleClickDate = this.handleClickDate.bind(this);
    }

    handleClickDate(date) {}

    render() {
        return (
            <div>
                <Calendar onClickDate={this.handleClickDate} />
            </div>
        );
    }
}

export default BookingForm;
