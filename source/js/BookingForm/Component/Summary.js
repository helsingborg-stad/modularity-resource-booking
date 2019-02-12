import { Button } from 'hbg-react';
import dateFns from 'date-fns';
import classNames from 'classnames';

class Summary extends React.Component {
    static propTypes = {};

    constructor(props) {
        super(props);
        this.state = {};
    }

    render() {
        const { children, onClickRemoveItem, translation, disabled } = this.props;

        const totalPrice =
            children.length > 0
                ? children.reduce((accumulator, slot) => accumulator + slot.articlePrice, 0)
                : 0;

        return (
            <div>
                <table className="table table--plain">
                    <thead>
                        <tr>
                            <th>{translation.product}</th>
                            <th>{translation.start}</th>
                            <th colSpan="2">{translation.end}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {children.map(slot => (
                            <tr key={slot.id}>
                                <td>{slot.articleName}</td>
                                <td>{dateFns.format(slot.start, 'DD-MM-YYYY HH:mm')}</td>
                                <td>{dateFns.format(slot.stop, 'DD-MM-YYYY HH:mm')}</td>
                                <td
                                    className={classNames({
                                        'text-right': true,
                                        disabled: !!(typeof disabled !== 'undefined' && disabled),
                                    })}
                                >
                                    <Button
                                        color="plain"
                                        onClick={
                                            typeof onClickRemoveItem === 'function'
                                                ? event => {
                                                      onClickRemoveItem(slot, event);
                                                  }
                                                : null
                                        }
                                    >
                                        <i className="pricon pricon-close-o" />
                                    </Button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colSpan="3" className="text-right" />
                            <td className="text-right">
                                <b>
                                    {translation.total}: {totalPrice} {translation.currency}{' '}
                                    <small>{translation.vat}</small>
                                </b>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        );
    }
}

export default Summary;
