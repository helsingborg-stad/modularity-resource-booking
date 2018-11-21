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
