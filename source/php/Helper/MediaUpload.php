<?php

namespace ModularityResourceBooking\Helper;

class MediaUpload
{
    public static function upload($uploads, $mediaIds = array())
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
                $dimensionsError = self::checkDimensions($fileData);
                if (count($dimensionsError) > 0) {

                    unlink($fileData['file']);

                    $error['error'] = ($dimensionsError['width'] != null)
                        ? $dimensionsError['width']
                        : null;
                    $error['error'] .= ($dimensionsError['width'] != null && $dimensionsError['height'] != null)
                        ? __(' and ', 'modularity-resource-booking') . $dimensionsError['height']
                        : $dimensionsError['height'];

                    return $error;
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

    public static function checkDimensions($file, $prodId)
    {
        $widthFromProduct = get_option('image_width', $prodId);
        $heightFromProduct = get_option('image_height', $prodId);
        $format = pathinfo($file['file']);

        switch ($format['extension']) {
            case "pdf":

                if (!extension_loaded('imagick')) {
                    return 'imagick not installed';
                }

                $imageMagick = new \Imagick($file['file']);
                $size = $imageMagick->getImageGeometry();
                $error['width'] = ($size['width'] > $widthFromProduct) ?
                    __('The width of file (' . $size['width'] . ' is to big', 'modularity-resource-booking') : null;
                $error['height'] = ($size['height'] > $heightFromProduct) ?
                    __('The height of file (' . $size['height'] . ' is to big', 'modularity-resource-booking') : null;

                return $error;
                break;

            case "mp4":
                return null;
                break;

            default:
                $size = getimagesize($file['file']);
                $error['width'] = ($size[0] > $widthFromProduct) ?
                    __('The width of file (' . $size[0] . ') is to big', 'modularity-resource-booking') : null;
                $error['height'] = ($size[1] > $heightFromProduct) ?
                    __('The height of file (' . $size[1] . ') is to big', 'modularity-resource-booking') : null;

                return $error;
                break;
        }
    }
}
