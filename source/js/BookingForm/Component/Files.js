import { Button, Calendar } from 'hbg-react';
import PropTypes from 'prop-types';

class Files extends React.Component {
    static propTypes = {};

    constructor(props) {
        super(props);
        this.state = {};
    }

    render() {
        const { children, onFileUpload } = this.props;
        return (
            <span>
                {children.map((media, index) => {
                    media.index = index;
                    return (
                        <div className="form-group" key={media['media_name'] + '-' + index}>
                            <label htmlFor={media['media_name'] + '-' + index}>
                                {media['media_name']}
                            </label>
                            <input
                                className="form-input"
                                id={media['media_name'] + '-' + index}
                                name={media['media_name'] + '-' + index}
                                type="file"
                                accept={
                                    media['file_types'].length > 0
                                        ? media['file_types'].join(', ')
                                        : null
                                }
                                onChange={
                                    typeof onFileUpload === 'function'
                                        ? e => {
                                              onFileUpload(e.target.files, media);
                                          }
                                        : null
                                }
                                data-max-filesize={media['maxiumum_filesize']}
                                required
                            />
                            {typeof media.error !== 'undefined' && media.error.length > 0}{' '}
                            {<div className="form-notice text-danger text-sm">{media.error}</div>}
                            <ul className="unlist">
                                <li>
                                    <small>
                                        <b>Dimensions:</b>{' '}
                                        {media['image_width'] + 'x' + media['image_height']}
                                    </small>
                                </li>
                                <li>
                                    <small>
                                        <b>Max Filesize:</b> {media['maxiumum_filesize'] + 'MB'}
                                    </small>
                                </li>
                                <li>
                                    <small>
                                        <b>Allowed Filetypes:</b> {media['file_types'].join(', ')}
                                    </small>
                                </li>
                            </ul>
                        </div>
                    );
                })}
            </span>
        );
    }
}

export default Files;
