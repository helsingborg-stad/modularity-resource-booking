import PropTypes from 'prop-types';
import classNames from 'classnames';

class Checkbox extends React.Component {
    static propTypes = {
        name: PropTypes.string.isRequired,

        id: PropTypes.string,

        checked: PropTypes.bool,

        onChange: PropTypes.func.isRequired,

        required: PropTypes.bool,

        disabled: PropTypes.bool,

        readonly: PropTypes.bool,

        explainer: PropTypes.string,

        label: PropTypes.string.isRequired,

        description: PropTypes.string,
    };

    render() {
        const { props } = this;

        return (
            <div className={classNames('form-group', { disabled: props.disabled })}>
                {props.label && (
                    <label htmlFor={props.id || props.name} className="checkbox">
                        <input
                            className="form-input"
                            id={props.id || props.name}
                            name={props.name}
                            type="checkbox"
                            checked={props.checked}
                            onChange={props.onChange}
                            disabled={props.disabled}
                            required={props.required}
                        />{' '}
                        {props.label}{' '}
                        {typeof props.explainer !== 'undefined' && props.explainer.length > 0 ? (
                            <span data-tooltip={props.explainer}>
                                <i className="fa fa-question-circle" />
                            </span>
                        ) : null}
                    </label>
                )}

                {typeof props.description !== 'undefined' && props.description.length > 0 ? (
                    <small>{props.description}</small>
                ) : null}
            </div>
        );
    }
}

export default Checkbox;
