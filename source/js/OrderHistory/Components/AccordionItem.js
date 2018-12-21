import ArticlesTable from './ArticlesTable';

const AccordionItem = ({headings, content}) =>
    <section className="accordion-section">
        <label tabIndex="0" className="accordion-toggle" htmlFor="accordion-section-1">
                <span className="accordion-table">
                {headings.map((heading, i) => (
                    <span key={i} className="column-header">{heading}</span>
                ))}
                </span>
        </label>
        <div className="accordion-content">
            <ArticlesTable />
        </div>
    </section>;

export default AccordionItem;