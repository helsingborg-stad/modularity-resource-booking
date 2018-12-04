const validateConfirmationField = (fieldId, confirmationFieldId, notice = 'The password does not match the control field.') => {
    if (typeof(hyperform) == 'undefined') {
        return;
    }

    hyperform.addValidator(
        document.getElementById(confirmationFieldId),
        function(element) {
            var valid = element.value === document.getElementById(fieldId).value;
            element.setCustomValidity(
                valid ?
                '' :
                notice
            );
            return valid;
        }
    );

    document.getElementById(fieldId).addEventListener('keyup', function() {
        document.getElementById(confirmationFieldId).reportValidity();
    });
}

module.exports = {
    validateConfirmationField: validateConfirmationField
};
