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

                // check size
                self::checkDimensions($upload);
                return $mediaIds["apa 1apa"];
                //Move to correct location
                $fileData = \wp_handle_upload($upload, array('test_form' => false));

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

    public static function checkUploadErrors($file) {
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

    public static function checkDimensions($file)
    {
        $info = new SplFileInfo($file);
        $fileFormat = pathinfo($info->getFilename(), PATHINFO_EXTENSION);
        $fileInformation = new \finfo(FILEINFO_MIME_TYPE);
        var_dump($fileInformation);
        switch($fileFormat){
            case "pdf":
                $pdfinfo = shell_exec("pdfinfo ".$file);

                // find height and width
                preg_match('/Page size:\s+([0-9]{0,5}\.?[0-9]{0,3}) x ([0-9]{0,5}\.?[0-9]{0,3})/', $pdfinfo,$size);
                $width = $size[1];
                $height = $size[2];

                echo $width . " : " . $height;

                break;
            case "png":
                echo "YO";
                break;
        }
    }
}
