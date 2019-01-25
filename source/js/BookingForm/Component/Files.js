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
                                {media['media_name'] +
                                    ' (' +
                                    media['image_width'] +
                                    'x' +
                                    media['image_height'] +
                                    ')'}
                            </label>
                            <input
                                className="form-input"
                                id={media['media_name'] + '-' + index}
                                name={media['media_name'] + '-' + index}
                                type="file"
                                accept={media['file_types'].length > 0 ? media['file_types'].join(', ') : null}
                                onChange={
                                    typeof onFileUpload === 'function'
                                        ? e => {
                                              onFileUpload(e.target.files, media);
                                          }
                                        : null
                                }
                                required
                            />
                        </div>
                    );
                })}
            </span>
        );
    }
}

export default Files;
