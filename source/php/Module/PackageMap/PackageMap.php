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

    public function data() : array
    {
        $data = get_fields($this->ID);
        $data['classes'] = implode(' ', apply_filters('Modularity/Module/Classes', array(), $this->post_type, $this->args));
        return $data;
    }

    public function style()
    {
        if (file_exists(MODULARITYRESOURCEBOOKING_PATH . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('css/modularity-resource-booking.css'))) {
            // Enqueue module script
            wp_enqueue_style('modularity-resource-booking-css', MODULARITYRESOURCEBOOKING_URL . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('css/modularity-resource-booking.css'));
        }
    }

    public function script()
    {
        if (file_exists(MODULARITYRESOURCEBOOKING_PATH . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('js/PackageMap/Index.js'))) {
            // Enqueue react
            \Modularity\Helper\React::enqueue();

            // Enqueue module script
            wp_enqueue_script('modularity-' . $this->slug, MODULARITYRESOURCEBOOKING_URL . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('js/PackageMap/Index.js'), array('jquery', 'react', 'react-dom'));

            //Localize
            wp_localize_script('modularity-' . $this->slug, 'modPackageMap', array(
                'translation' => array(),
                'restUrl' => get_rest_url(),
                'package_id' => get_field('package_id', $this->ID),
                'packageId' => get_current_user_id(),
                'order_nonce' => wp_create_nonce('wp_rest')
            ));
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
