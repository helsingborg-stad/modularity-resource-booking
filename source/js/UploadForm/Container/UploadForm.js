import { Button, Notice } from 'hbg-react';
import Files from '../../BookingForm/Component/Files';
import { ValidateFileSize } from '../../BookingForm/Helper/hyperForm';
import { uploadFiles } from '../../Api/orders';

class UploadForm extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            // Files
            files: props.files,

            // Notice
            notice: '',
            noticeType: '',

            // Form Control
            lockForm: false,
            formIsLoading: false,
        };
    }

    componentDidMount() {
        // Enable Filesize client validation
        new ValidateFileSize();
    }

    handleSubmitForm = e => {
        e.preventDefault();
        const { orderId, restUrl, restNonce } = this.props;
        const { files, notice, lockForm } = this.state;

        // Bail
        if (lockForm) {
            return;
        }

        // Lock & load
        this.setState({ lockForm: true, formIsLoading: true });

        // Reset notice
        if (notice.length > 0) {
            this.setState({ notice: '' });
        }

        // POST
        uploadFiles(orderId, files, restUrl, restNonce).then(response => {
            // Reset loading
            this.setState({ formIsLoading: false });

            // Dimension error
            if (
                response.state === 'dimension-error' &&
                Object.keys(response.data.invalid_dimensions).length > 0
            ) {
                this.setState((state, props) => {
                    const { files } = state;
                    Object.keys(response.data.invalid_dimensions).forEach(fileIndex => {
                        files[fileIndex].error = response.data.invalid_dimensions[fileIndex];
                    });
                });
            }

            // Unlock form if not succesful
            if (response.state !== 'success') {
                this.setState({ lockForm: false });
            }

            this.setState((state, props) => {
                return {
                    notice: response.message,
                    noticeType: response.state === 'success' ? 'success' : 'warning',
                };
            });
        });
    };

    /**
     * Fires on file upload, saves the file in state
     * @param  {array} files Array of file objects uploaded through the file input
     * @param  {object} media The file object taken from files in states
     * @return {void}
     */
    handleFileUpload = (files, media) => {
        this.setState((state, props) => {
            const mediaRequirements = state.files;
            mediaRequirements[media.index].file = files.length > 0 ? files[0] : null;
            mediaRequirements[media.index].error = '';
            return { files: mediaRequirements };
        });
    };

    render() {
        const { translation } = this.props;
        const { files, notice, noticeType, lockForm, formIsLoading } = this.state;
        return (
            <form onSubmit={this.handleSubmitForm}>
                <div className="grid">
                    <div className="grid-xs-12 u-mb-3">
                        <Files
                            onFileUpload={this.handleFileUpload}
                            disabled={!!lockForm}
                            translation={translation}
                        >
                            {files}
                        </Files>
                    </div>

                    <div className="grid-xs-12">
                        <div className="grid grid-va-middle">
                            <div className="grid-fit-content">
                                <Button
                                    color="primary"
                                    submit
                                    disabled={!!lockForm}
                                    title={translation.uploadFiles}
                                />
                            </div>

                            {formIsLoading ? (
                                <div className="grid-fit-content u-pl-0">
                                    {' '}
                                    <div className="spinner spinner-dark" />
                                </div>
                            ) : null}
                        </div>
                    </div>

                    {notice.length > 0 && (
                        <div className="grid-xs-12 u-mt-2">
                            <Notice type={noticeType} icon>
                                <span dangerouslySetInnerHTML={{ __html: notice }} />
                            </Notice>
                        </div>
                    )}
                </div>
            </form>
        );
    }
}

export default UploadForm;
