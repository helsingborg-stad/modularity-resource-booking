/**
 * Fetches article (product or package) data from rest api
 * @param  {string/int} articleId   Product or Term ID
 * @param  {string} articleType Defines the article type, can be either "package" or "product"
 * @return {array}  Article data
 */
const getArticle = (articleId, articleType, restUrl) => {
    const capitalizeFirstLetter = string => {
        return string.charAt(0).toUpperCase() + string.slice(1);
    };

    const url = `${restUrl}ModularityResourceBooking/v1/${capitalizeFirstLetter(
        articleType
    )}/${articleId}`;

    return fetch(url)
        .then(response => {
            return response.json();
        })
        .then(response => {
            if (response.state === 'error') {
                throw new Error(response.message);
            }

            return response;
        });
};

/**
 * Fetches article (product or package) data from rest api
 * @param  {string/int} articleId   Product or Term ID
 * @param  {string} articleType Defines the article type, can be either "package" or "product"
 * @param  {[type]} userId      Wordpress user ID
 * @return {array}             Array containing slots
 */
const getSlots = (articleId, articleType, userId, restUrl) => {
    let url = `${restUrl}ModularityResourceBooking/v1/Slots?`;

    const params = {
        articleType: `type=${articleType}`,
        articleId: `&article_id=${articleId}`,
        userId: `&user_id=${userId}`,
    };

    url = url.concat(params.articleType, params.articleId, params.userId);

    return fetch(url)
        .then(response => {
            return response.json();
        })
        .then(response => {
            if (response.state === 'error') {
                throw new Error(response.message);
            }

            return response;
        });
};

export { getArticle, getSlots };
