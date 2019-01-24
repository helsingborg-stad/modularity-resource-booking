<?php

namespace ModularityResourceBooking\Helper;

/**
 * Class MediaUpload
 * @package ModularityResourceBooking\Helper
 */
class MediaUpload
{
    /**
     * Upload media
     * The current version of this plugin only handles orders with a single package/product.
     * The method below needs to be updated to support multiple packages/products.
     * @param $articleId
     * @param $articleType
     * @param $uploads
     * @return \WP_ERROR|array
     */
    public static function upload($articleId, $articleType, $uploads)
    {
        // Require resources
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        // Check for errors
        foreach ($uploads as $key => $upload) {
            // Not a valid upload for some reason, move to next
            if (self::checkUploadErrors($upload) === false) {
                return new \WP_ERROR(
                    'error',
                    sprintf(__('The file: "%s" could not be uploaded.', 'modularity-resource-booking'), $upload['name'])
                );
            }
            // Check if a valid mime type
            if (self::checkMimeType($upload) !== true) {
                return new \WP_ERROR(
                    'error',
                    sprintf(__('Invalid file type: "%s".', 'modularity-resource-booking'), $upload['name'])
                );
            }
        }

        // Get list of media requirements
        $requiredDimensions = array();
        if ($articleType === 'package') {
            $requiredDimensions = Product::getPackageMediaRequirements($articleId);
        } else {
            $productRequirements = get_field('media_requirement', $articleId);
            if (is_array($productRequirements) && !empty($productRequirements)) {
                $requiredDimensions = $productRequirements;
            }
        }

        // Validate dimensions if requirements is set
        if (count($requiredDimensions) > 0) {
            $checkDimensions = self::checkDimensions($requiredDimensions, $uploads);
            if (is_wp_error($checkDimensions)) {
                return $checkDimensions;
            }
        }

        // Do the upload
        $mediaIds = array();
        foreach ($uploads as $upload) {
            // Move to correct location
            $fileData = \wp_handle_upload($upload, array('test_form' => false));

            // Add to WordPress media library
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

        return $mediaIds;
    }

    /**
     * Check if uploaded files has correct dimensions
     * @param array  $requiredDimensions List with required file dimensions
     * @param object $files              Object with uploaded files
     * @return bool|\WP_ERROR
     */
    public static function checkDimensions($requiredDimensions, $files)
    {
        // Remap requirements array with single value string of dimensions
        $requiredDimensions = array_map(function ($requirement) {
            return "{$requirement['image_width']}x{$requirement['image_height']}";
        }, $requiredDimensions);

        // Get list of the uploaded files dimensions
        $uploadedDimensions = array();
        foreach ($files as $key => $file) {
            switch ($file['type']) {
                case "application/pdf":
                    if (!extension_loaded('imagick')) {
                        return new \WP_ERROR(
                            'error',
                            __('Imagick not installed.', 'modularity-resource-booking')
                        );
                    }
                    try {
                        $imageMagick = new \Imagick($file['tmp_name']);
                        $size = $imageMagick->getImageGeometry();
                        $width = $size['width'] ?? '';
                        $height = $size['height'] ?? '';
                    } catch (\ImagickException $e) {
                        return new \WP_ERROR(
                            'error',
                            __('Imagick failed to get file dimensions.', 'modularity-resource-booking')
                        );
                    }
                    break;
                case "video/mp4":
                    $dimensions = \wp_read_video_metadata($file['tmp_name']);
                    $width = $dimensions['width'] ?? '';
                    $height = $dimensions['height'] ?? '';
                    break;
                case "image/png":
                case "image/jpeg":
                    $fileInfo = getimagesize($file['tmp_name']);
                    $width = $fileInfo[0] ?? '';
                    $height = $fileInfo[1] ?? '';
                    break;
                default:
                    $width = '';
                    $height = '';
                    break;
            }

            $uploadedDimensions[] = "{$width}x{$height}";
        }

        // Check if correct amount of required files is uploaded
        if (count($uploadedDimensions) !== count($requiredDimensions)) {
            return new \WP_ERROR(
                'error',
                __('One or many required files is missing.', 'modularity-resource-booking')
            );
        }

        // Compare lists diff to validate dimensions
        $diff = array_diff($requiredDimensions, $uploadedDimensions);

        if (!empty($diff)) {
            foreach ($diff as $key => &$item) {
                $item = sprintf(__('Your file has wrong dimensions: %spx. Please upload a file with following dimensions: %spx.', 'modularity-resource-booking'), $uploadedDimensions[$key], $requiredDimensions[$key]);
            }
            // Return list of all files that failed validation
            return new \WP_ERROR(
                'dimension-error',
                __('Wrong file dimensions.', 'modularity-resource-booking'),
                array(
                    'invalid_dimensions' => $diff
                )
            );
        }

        return true;
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
}
