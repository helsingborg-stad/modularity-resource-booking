import { Button } from 'hbg-react';
import dateFns from 'date-fns';
import classNames from 'classnames';

const removeBg = {
    backgroundColor: 'initial',
};

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
                    <tbody style={removeBg}>
                        {children.map(slot => (
                            <tr key={slot.id}>
                                <td className="u-pb-3">
                                    <b>{slot.articleName}</b>
                                    <br />
                                    {slot.isoWeek && (
                                        <React.Fragment>
                                            <small>
                                                <b>{`Vecka ${slot.isoWeek}`}</b>
                                            </small>
                                            <br />
                                        </React.Fragment>
                                    )}

                                    <small>
                                        {`${translation.start}: ${dateFns.format(
                                            slot.start,
                                            'DD-MM-YYYY HH:mm'
                                        )}`}
                                    </small>
                                    <br />
                                    <small>
                                        {`${translation.end}: ${dateFns.format(
                                            slot.stop,
                                            'DD-MM-YYYY HH:mm'
                                        )}`}
                                    </small>
                                </td>
                                <td className="text-right">{`${slot.articlePrice} ${
                                    translation.currency
                                }`}</td>
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
                    <tfoot style={removeBg}>
                        <tr>
                            <td className="text-right" />
                            <td className="text-right u-pt-2">
                                <b>
                                    {translation.total}: {totalPrice} {translation.currency} <br />
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
