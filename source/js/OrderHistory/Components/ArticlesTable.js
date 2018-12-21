const ArticlesTable = ({headings, articles}) =>
    <table className="table">
        {console.log(headings)}
        <thead>
        <tr>
            {headings.map(heading =>
                <th key={heading}>{heading}</th>
            )}
        </tr>
        </thead>
        <tbody>
        {articles.map((article, i) =>
            <tr key={i}>
                <td>{article.title}</td>
                <td>{article.type}</td>
                <td>{article.start} - {article.stop}</td>
                <td></td>
            </tr>
        )}
        </tbody>
    </table>;

export default ArticlesTable;