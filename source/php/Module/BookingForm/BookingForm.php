<?php

namespace ModularityResourceBooking\Module;

class BookingForm extends \Modularity\Module
{
    public $slug = 'resource-booking-form';
    public $supports = array();

    public function init()
    {
        $this->nameSingular = __('Resource Booking Form', 'modularity-resource-booking');
        $this->namePlural = __('Resource Booking Forms', 'modularity-resource-booking');
        $this->description = __('Outputs a form for booking resources.', 'modularity-resource-booking');
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
        if (file_exists(MODULARITYRESOURCEBOOKING_PATH . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('js/BookingForm/Index.js'))) {
            // Enqueue react
            \Modularity\Helper\React::enqueue();

            // Enqueue module script
            wp_enqueue_script('modularity-' . $this->slug, MODULARITYRESOURCEBOOKING_URL . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('js/BookingForm/Index.js'), array('jquery', 'react', 'react-dom'));

            //Localize
            wp_localize_script('modularity-' . $this->slug, 'modResourceBookingForm', array(
                'translation' => array(),
                'restUrl' => get_rest_url()
            ));
        }
    }


    public function template()
    {
        return 'booking-form.blade.php';
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
