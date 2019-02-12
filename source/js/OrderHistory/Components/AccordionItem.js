import { Button } from 'hbg-react';
import ArticlesTable from './ArticlesTable';

const AccordionItem = ({
    id,
    index,
    headings,
    articles,
    articleHeadings,
    translation,
    cancelOrder,
    cancelable,
}) => (
    <section className="accordion-section">
        <label tabIndex="0" className="accordion-toggle" htmlFor="accordion-section-1">
            <span className="accordion-table">
                {headings.map((heading, i) => {
                    const canceled = heading === translation.canceled ? 'text-danger' : '';
                    return (
                        <span key={i} className={`column-header ${canceled}`}>
                            {heading}
                        </span>
                    );
                })}
            </span>
        </label>
        <div className="accordion-content">
            <div className="grid">
                <div className="grid-xs-12">
                    <ArticlesTable
                        headings={articleHeadings}
                        articles={articles}
                        translation={translation}
                    />
                </div>
                {cancelable && (
                    <div className="grid-xs-12 u-mt-1">
                        <Button
                            title={translation.cancelOrder}
                            onClick={e => cancelOrder(e, index, id)}
                            color="primary"
                            size="small"
                            outline={false}
                        />
                    </div>
                )}
            </div>
        </div>
    </section>
);

export default AccordionItem;
