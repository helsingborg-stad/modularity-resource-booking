import AccordionItem from './AccordionItem';
import SearchField from './SearchField';

const AccordionTable = ({headings, items, showSearch, doSearch, langFilterOn, langNoResults}) =>
    <div>
        <header className="accordion-table accordion-table-head">
            {headings.map((heading, i) => (
                <span key={i} className="column-header">
                    {heading}
                </span>
            ))}
        </header>
        <div className="accordion accordion-icon accordion-list">
            {showSearch &&
                <SearchField
                    doSearch={doSearch}
                    langFilterOn={langFilterOn}
                />
            }
            {Object.keys(items).length === 0 &&
                <div className="gutter"><p>{langNoResults}</p></div>
            }
            {items.map(item => (
                <AccordionItem
                    key={item.id}
                    headings={item.headings}
                    content={item.content}
                />
            ))}
        </div>
    </div>;

export default AccordionTable;