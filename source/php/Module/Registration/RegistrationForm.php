<?php

namespace ModularityResourceBooking\Module;

class RegistrationForm extends \Modularity\Module
{
    public $slug = 'registration-form';
    public $supports = array();

    public function init()
    {
        $this->nameSingular = __('Registration form', 'modularity-resource-booking');
        $this->namePlural = __('Registration forms', 'modularity-resource-booking');
        $this->description = __('Outputs a registration form for customers.', 'modularity-resource-booking');
    }

    public function data() : array
    {
        $data = get_fields($this->ID);
        $data['classes'] = implode(' ', apply_filters('Modularity/Module/Classes', array('box', 'box-panel'), $this->post_type, $this->args));
        return $data;
    }

    public function script()
    {
        if (file_exists(MODULARITYRESOURCEBOOKING_PATH . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('js/RegistrationForm/Index.js'))) {
            // Enqueue react
            \ModularityResourceBooking\Helper\React::enqueue();
            // Enqueue module script
            wp_enqueue_script('modularity-' . $this->slug, MODULARITYRESOURCEBOOKING_URL . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('js/RegistrationForm/Index.js'), array('jquery', 'react', 'react-dom'));
            wp_localize_script('modularity-' . $this->slug, 'modRegistrationForm', array(
                'translation' => array(),
                'restUrl' => get_rest_url()
            ));
        }
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
