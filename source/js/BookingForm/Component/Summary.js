import { Button, Calendar } from 'hbg-react';
import PropTypes from 'prop-types';
import dateFns from 'date-fns';

class Summary extends React.Component {
    static propTypes = {};

    constructor(props) {
        super(props);
        this.state = {};
    }

    render() {
        const { children, onClickRemoveItem } = this.props;

        const totalPrice =
            children.length > 0
                ? children.reduce((accumulator, slot) => accumulator + slot.articlePrice, 0)
                : 0;

        return (
            <div>
                <table className="table">
                    <thead>
                        <tr>
                            <th>Produkt</th>
                            <th>Startar</th>
                            <th colSpan="2">Slutar</th>
                        </tr>
                    </thead>
                    <tbody>
                        {children.map(slot => (
                            <tr key={slot.id}>
                                <td>{slot.articleName}</td>
                                <td>{dateFns.format(slot.start, 'DD-MM-YYYY')}</td>
                                <td>{dateFns.format(slot.stop, 'DD-MM-YYYY')}</td>
                                <td className="text-right">
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
                            <td colSpan="3" className="text-right">
                                Total:
                            </td>
                            <td className="text-right">{totalPrice}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        );
    }
}

export default Summary;
