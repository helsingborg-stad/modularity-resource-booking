import BookingForm from './Container/BookingForm';

const domElements = document.getElementsByClassName("modularity-resource-booking-form");
const {translation} = modResourceBookingForm;

for (let i = 0; i < domElements.length; i++) {
    const element = domElements[i];
    ReactDOM.render(
        <BookingForm
            translation={translation}
        />,
        element
    );
}
