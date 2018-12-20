import OrderItem from './OrderItem';

const OrderList = ({items, translation}) =>
    items.map(item => (
        <OrderItem
            key={item.id}
            item={item}
            translation={translation}
        />
    ));

export default OrderList;