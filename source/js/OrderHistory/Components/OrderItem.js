const OrderItem = ({item, translation}) =>
    <div className="grid-md-12 u-mb-2">
        <div className="c-card">
            <h4 className="c-card__header">
                #{item.order_id}
            </h4>
            <div className="c-card__body">
                <p className="c-card__text">{item.date}</p>
            </div>
        </div>
    </div>;

export default OrderItem;