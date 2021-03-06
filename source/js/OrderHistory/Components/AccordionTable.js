import AccordionItem from './AccordionItem';

const AccordionTable = ({ headings, items, articleHeadings, translation, cancelOrder }) => (
    <div>
        <header className="accordion-table accordion-table-head">
            {headings.map((heading, i) => (
                <span key={i} className="column-header">
                    {heading}
                </span>
            ))}
        </header>
        <div className="accordion accordion-icon accordion-list">
            {Object.keys(items).length === 0 && (
                <div className="gutter">
                    <p>{translation.noOrdersFound}</p>
                </div>
            )}
            {items.map((item, i) => (
                <AccordionItem
                    key={item.id}
                    id={item.id}
                    index={i}
                    headings={item.headings}
                    cancelable={item.cancelable}
                    articleHeadings={articleHeadings}
                    articles={item.articles}
                    translation={translation}
                    cancelOrder={cancelOrder}
                    permalink={item.permalink}
                />
            ))}
        </div>
    </div>
);

export default AccordionTable;
