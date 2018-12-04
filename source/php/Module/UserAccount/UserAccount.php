<?php

namespace ModularityResourceBooking\Module;

class UserAccount extends \Modularity\Module
{
    public $slug = 'user-account';
    public $supports = array();

    public function init()
    {
        $this->nameSingular = __('User account settings', 'modularity-resource-booking');
        $this->namePlural = __('User account settings', 'modularity-resource-booking');
        $this->description = __('Outputs a form for user account settings.', 'modularity-resource-booking');
    }

    public function data() : array
    {
        $data = get_fields($this->ID);
        $data['classes'] = implode(' ', apply_filters('Modularity/Module/Classes', array('box', 'box-panel'), $this->post_type, $this->args));
        return $data;
    }

    public function getUserData() : array
    {
        if (!is_user_logged_in()) {
            return array();
        }

        $data = array(
            'id'             => get_current_user_id(),
            'email'          => wp_get_current_user()->data->user_email,
            'firstName'      => get_user_meta(get_current_user_id())['first_name'][0],
            'lastName'       => get_user_meta(get_current_user_id())['last_name'][0],
            'company'        => '',
            'companyNumber'  => '',
            'phone'          => '',
            'website'        => '',
            'billingAddress' => '',
            'contactPerson'  => ''
        );

        return $data;
    }

    public function script()
    {
        if (file_exists(MODULARITYRESOURCEBOOKING_PATH . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('js/RegistrationForm/Index.js'))) {
            // Enqueue react
            \ModularityResourceBooking\Helper\React::enqueue();

            // Enqueue module script
            wp_enqueue_script('modularity-' . $this->slug, MODULARITYRESOURCEBOOKING_URL . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('js/UserAccount/Index.js'), array('jquery', 'react', 'react-dom'));

            //Localize
            wp_localize_script('modularity-' . $this->slug, 'modUserAccount', array(
                'translation' => array(),
                'restUrl' => get_rest_url(),
                'user' => $this->getUserData()
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
