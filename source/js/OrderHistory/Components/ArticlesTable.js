const ArticlesTable = ({ headings, articles }) => (
    <table className="table">
        <thead>
            <tr>
                {headings.map(heading => (
                    <th key={heading}>{heading}</th>
                ))}
            </tr>
        </thead>
        <tbody>
            {articles.map((article, i) => (
                <tr key={i}>
                    <td>{article.title}</td>
                    <td>{article.type}</td>
                    <td>
                        {article.start} - {article.stop}
                    </td>
                    <td>{article.price} SEK</td>
                </tr>
            ))}
        </tbody>
    </table>
);

export default ArticlesTable;
