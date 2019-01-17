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
                        <input
                            key={media['media_name'] + '-' + index}
                            id={media['media_name'] + '-' + index}
                            name={media['media_name'] + '-' + index}
                            type="file"
                            accept={media['media_type'] + '/*'}
                            onChange={
                                typeof onFileUpload === 'function'
                                    ? e => {
                                          onFileUpload(e.target.files, media);
                                      }
                                    : null
                            }
                        />
                    );
                })}
            </span>
        );
    }
}

export default Files;
