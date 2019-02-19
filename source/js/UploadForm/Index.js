import UploadForm from './Container/UploadForm';
import { getArticle } from '../Api/products';

const App = args => {
    /**
     *
     * @param {*} articleId
     * @param {*} articleType
     * @param {*} restUrl
     */
    const getMediaRequirements = async (articleId, articleType, restUrl) => {
        const getArticleData = await getArticle(articleId, articleType, restUrl).then(response => {
            if (
                typeof response[0] !== 'undefined' &&
                typeof response[0].media_requirements !== 'undefined' &&
                response[0].media_requirements.length > 0
            ) {
                return response[0].media_requirements.map(mediaObject => {
                    const media = mediaObject;
                    // We will store file blob (upon upload) within this propety
                    media.file = null;

                    return media;
                });
            }

            throw new Error(response);
        });

        return getArticleData;
    };

    /**
     *
     * @param {*} arrayOfRequiredKeys
     * @param {*} objectToValidate
     */
    const validateObjectKeys = (arrayOfRequiredKeys, objectToValidate) => {
        return arrayOfRequiredKeys.reduce((accumulator, key) => {
            if (typeof objectToValidate[key] === 'undefined') {
                return false;
            }

            return accumulator;
        }, true);
    };

    //  Validate args param keys
    if (validateObjectKeys(['translation'], args)) {
        const { translation } = args;

        //  Get Elements
        const domElements = document.getElementsByClassName('modularity-resource-upload-form');

        //  Foreach Element
        for (let i = 0; i < domElements.length; i++) {
            const element = domElements[i];

            //  Get data from attribute
            if (element.getAttribute('data-upload-form')) {
                const attributeData = JSON.parse(element.getAttribute('data-upload-form'));
                const requiredKeys = [
                    'orderId',
                    'articleId',
                    'articleType',
                    'restUrl',
                    'restNonce',
                ];

                //  Validate attributeData keys
                if (validateObjectKeys(requiredKeys, attributeData)) {
                    const { orderId, articleId, articleType, restUrl, restNonce } = attributeData;

                    // Get media requirements
                    getMediaRequirements(articleId, articleType, restUrl).then(response => {
                        // Render form
                        ReactDOM.render(
                            <UploadForm
                                orderId={orderId}
                                files={response}
                                translation={translation}
                                restUrl={restUrl}
                                restNonce={restNonce}
                            />,
                            element
                        );
                    });
                }
            }
        }
    }
};

if (typeof modResourceUploadForm !== 'undefined') {
    new App(modResourceUploadForm);
}
