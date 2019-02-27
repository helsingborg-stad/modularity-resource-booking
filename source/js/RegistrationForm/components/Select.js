import PropTypes from 'prop-types';
import classNames from 'classnames';

class Select extends React.Component {
    static propTypes = {
        name: PropTypes.string.isRequired,

        id: PropTypes.string,

        checked: PropTypes.bool,

        onChange: PropTypes.func.isRequired,

        required: PropTypes.bool,

        disabled: PropTypes.bool,

        readonly: PropTypes.bool,

        explainer: PropTypes.string,

        label: PropTypes.string,

        description: PropTypes.string,

        placeholder: PropTypes.string,
    };

    render() {
        const { props } = this;

        return (
            <div className={classNames('form-group', { disabled: props.disabled })}>
                {props.label && (
                    <label htmlFor={props.id || props.name} className="form-label">
                        {props.label}{' '}
                        {typeof props.explainer !== 'undefined' && props.explainer.length > 0 ? (
                            <span data-tooltip={props.explainer}>
                                <i className="fa fa-question-circle" />
                            </span>
                        ) : null}
                    </label>
                )}

                <select
                    className="form-input"
                    id={props.id || props.name}
                    name={props.name}
                    type={props.type}
                    value={props.value}
                    onChange={props.onChange}
                    required={props.required}
                >
                    {typeof props.placeholder !== 'undefined' && props.placeholder.length > 0 && (
                        <option value="" disabled>
                            {props.placeholder}
                        </option>
                    )}
                    {props.children}
                </select>

                {typeof props.description !== 'undefined' && props.description.length > 0 ? (
                    <small>{props.description}</small>
                ) : null}
            </div>
        );
    }
}

export default Select;
