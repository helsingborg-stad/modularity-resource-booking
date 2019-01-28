<?php

namespace ModularityResourceBooking\Module;

class PackageMap extends \Modularity\Module
{

    public $slug = 'rb-package-map';
    public $supports = array();

    public function init()
    {
        $this->nameSingular = __('Resource Booking Package Map', 'modularity-resource-booking');
        $this->namePlural = __('Resource Booking Package Map', 'modularity-resource-booking');
        $this->description = __('Outputs a google map over products booked in package.', 'modularity-resource-booking');
    }

    public function getPackageData($id)
    { 

        if ($term = get_term($id, 'product-package')) {

            if(!is_a($term, 'WP_Term')) {
                return false; 
            }

            $postData = get_posts(
                array(
                    'posts_per_page' => -1,
                    'post_type' => 'product',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'product-package',
                            'field' => 'term_id',
                            'terms' => $term->term_id,
                        )
                    )
                )
            );

            if (is_object($postData) && !is_array($postData)) {
                $postData = array($postData);
            }

            if (is_array($postData) && !empty($postData)) {
                foreach ($postData as $postitem) {
                    $result[] = array(
                        'id' => (int)$postitem->ID,
                        'title' => (string)$postitem->post_title,
                        'content' => (string)$postitem->post_content,
                        'location' => get_field_object('product_location', $postitem->ID),
                        'productSpec' => get_field_object('media_requirement', $postitem->ID),
                    );
                }
            }

            return $result;
        }
    }

    public function data(): array
    {
        $data = get_fields($this->ID);

        $package = get_field_object('resource_booking_map_package', $this->data['ID']);
        $location = get_field_object('resource_booking_map_location', $this->data['ID']);

        $data['data']['lat'] = $location['value']['lat'];
        $data['data']['lng'] = $location['value']['lng'];
        $data['data']['title'] = $location['value']['address'];
        $data['data']['url'] = MODULARITYRESOURCEBOOKING_URL;
        $data['data']['classes'] = implode(' ',
            apply_filters('Modularity/Module/Classes', array(), $this->post_type, $this->args));
        
        $data['data']['getPackageData'] = $this->getPackageData($package['value']);
        $data['data']['translation'] = array(
            'medianame' => __('Media name', 'modularity-resource-booking'),
            'mediatype' => __('Media type', 'modularity-resource-booking'),
            'maxfilesize' => __('Maxiumum filesize', 'modularity-resource-booking'),
            'size' => __('Image width', 'modularity-resource-booking'),
        );
        return $data;
    }

    public function style()
    {
        if (file_exists(MODULARITYRESOURCEBOOKING_PATH . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('css/modularity-resource-booking.css'))) {
            wp_enqueue_style('modularity-resource-booking-css',
                MODULARITYRESOURCEBOOKING_URL . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('css/modularity-resource-booking.css'));
        }
    }


    public function script()
    {
        $apiKey['value'] = get_field_object('resource_booking_google_api_key', $this->data['ID']);
        if ($apiKey['value'] || GOOGLE_API_KEY) {
            $apiKey['value'] = (GOOGLE_API_KEY) ? GOOGLE_API_KEY : $apiKey['value'];
            wp_register_script('google-maps-api',
                'https://maps.googleapis.com/maps/api/js?key=' . $apiKey['value'] . '&callback=initMap', false, null,
                true);
            wp_enqueue_script('google-maps-api',
                'https://maps.googleapis.com/maps/api/js?key=' . $apiKey['value'] . '&callback=initMap', false, null,
                true);
        }
    }


    public function template()
    {
        return 'package-map.blade.php';
    }
    /**
     * Available "magic" methods for modules:
     * init()            What to do on initialization (if you must, use __construct with care, this will probably break stuff!!)
     * data()            Use to send data to view (return array)
     * style()           Enqueue style only when module is used on page
     * script            Enqueue script only when module is used on page
     * adminEnqueue()    Enqueue scripts for the module edit/add page in admin
     * template()        Return the view template (blade) the module should use when displayed
     */
}
