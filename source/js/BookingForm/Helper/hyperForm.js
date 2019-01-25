class ValidateFileSize {
    constructor() {
        this.validateFileSizeFields();
    }

    validateFileSizeFields() {
        if (typeof hyperform == 'undefined') {
            return;
        }

        var fileInputFields = document.querySelectorAll('input[type="file"][data-max-filesize]');
        if (fileInputFields.length == 0) {
            return;
        }

        fileInputFields.forEach(input => {
            this.validateFileSize(input);
        });
    }

    validateFileSize(element) {
        const maxFileSize = element.getAttribute('data-max-filesize');

        hyperform.addValidator(element, function(element) {
            let valid = true;

            if (typeof element.files[0] !== 'undefined') {
                const fileSize = element.files[0].size / 1024 / 1024;
                valid = fileSize > maxFileSize ? false : true;
            }

            element.setCustomValidity(
                valid
                    ? ''
                    : 'The uploaded file is larger then the maximum filesize of ' +
                          maxFileSize +
                          'MB.'
            );
            return valid;
        });

        element.addEventListener('change', () => {
            element.reportValidity();
        });
    }
}

export { ValidateFileSize };
