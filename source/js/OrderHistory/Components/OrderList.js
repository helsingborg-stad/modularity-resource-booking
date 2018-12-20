import OrderItem from './OrderList';

const OrderList = ({items}) =>
    <ul>
        {items.map(item => (
            <OrderItem
                key={item.id}
                order={item}
            />
        ))}
    </ul>;

export default OrderList;