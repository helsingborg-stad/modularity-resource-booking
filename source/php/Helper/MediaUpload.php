<?php

namespace ModularityResourceBooking\Helper;

/**
 * Class MediaUpload
 * @package ModularityResourceBooking\Helper
 */
class MediaUpload
{
    /**
     * @param $ProdId
     * @param $uploads
     * @param array $mediaIds
     * @return array|object|void|null
     * @throws \ImagickException
     */
    public static function upload($ProdId, $uploads, $mediaIds = array())
    {

        //Require resources
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        //Check if have file uploads
        if (!is_array($uploads) || empty($uploads)) {
            return;
        }

        if (is_array($uploads) && !empty($uploads)) {

            foreach ($uploads as $upload) {

                //Not a valid upload for some reason, move to next
                if (self::checkUploadErrors($upload) === false) {
                    continue;
                }

                //Check if a valid mime type
                if (self::checkMimeType($upload) !== true) {
                    continue;
                }

                //Move to correct location
                $fileData = \wp_handle_upload($upload, array('test_form' => false));

                // check dimension of file upload ( Unlink the image if it contains size errors)
                $dimensionErrors = self::checkDimensions($ProdId, $fileData);

                if ($dimensionErrors->error != null) {
                    unlink($fileData['file']);
                    return $dimensionErrors;
                }

                //Enter to WordPress media library
                if (is_array($fileData) && !empty($fileData) && isset($fileData['file'])) {
                    $mediaIds[] = \wp_insert_attachment(
                        array(
                            'post_title' => "",
                            'post_content' => ""
                        ),
                        $fileData['file']
                    );
                }
            }
        }

        //Return id's of uploaded media files
        return array_filter($mediaIds);
    }


    /**
     * @param $file
     * @return bool
     */
    public static function checkUploadErrors($file)
    {
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                return true;
                break;
            case UPLOAD_ERR_NO_FILE:
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return false;
                break;
        }
    }

    /**
     * @param $file
     * @return bool
     */
    public static function checkMimeType($file)
    {
        $fileInformation = new \finfo(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
                $fileInformation->file($file['tmp_name']),
                array(
                    'jpg' => 'image/jpeg',
                    'png' => 'image/png',
                    'mp4' => 'video/mp4',
                    'pdf' => 'application/pdf'
                ),
                true
            )) {
            return false;
        }
        return true;
    }


    /**
     * @param $prodId int
     * @param $fileData array
     * @return object|null
     * @throws \ImagickException
     */
    public static function checkDimensions($prodId, $fileData)
    {
        $rows = get_field('media_requirement', $prodId);
        $error = (object) array('error' => null);

        if ($rows) {
            foreach ($rows as $row) {

                $widthFromProduct = $row['image_width'];
                $heightFromProduct = $row['image_height'];

                $format = pathinfo($fileData['file']);

                $dimensions = $widthFromProduct . 'px x ' . $widthFromProduct . 'px';
                $errorStr = sprintf(__('Your image: %s, size: ({width}px x {height}px), has wrong dimensions.', 'modularity-resource-booking'), basename($fileData['url']));
                $errorStr .= sprintf(__(' Please upload image with following dimensions: %s' , 'modularity-resource-booking'), $dimensions);

                switch ($format['extension']) {

                    case "pdf":

                        if (!extension_loaded('imagick')) {
                            $error->error = 'imagick not installed';
                            return $error;
                        }

                        $imageMagick = new \Imagick($fileData['file']);
                        $size = $imageMagick->getImageGeometry();
                        $error->size = ($size['width'] == $widthFromProduct && $size['height'] == $heightFromProduct) ? false : true;
                        $message = str_replace('{width}', $size['width'], str_replace('{height}', $size['height'], $errorStr));
                        $error->error = ($error->size) ? sprintf(__('Error! %s', 'modularity-resource-booking'), $message) : null;

                        return $error;
                        break;

                    case "mp4":
                        return null;
                        break;

                    default:

                        $size = getimagesize($fileData['file']);
                        $error->size = ($size[0] == $widthFromProduct && $size[1] == $heightFromProduct) ? false : true;
                        $message = str_replace('{width}', $size[0], str_replace('{height}', $size[1], $errorStr));
                        $error->error = ($error->size) ? sprintf(__('Error! %s', 'modularity-resource-booking'), $message) : null;

                        return $error;
                        break;
                }
            }
        } else {
            return $error;
        }
    }
}
