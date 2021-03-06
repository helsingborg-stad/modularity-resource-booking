<?php

namespace ModularityResourceBooking\Module;

class RegistrationForm extends \Modularity\Module
{
    public $slug = 'rb-reg-form';
    public $supports = array();

    public function init()
    {
        $this->nameSingular = __('Registration form', 'modularity-resource-booking');
        $this->namePlural = __('Registration forms', 'modularity-resource-booking');
        $this->description = __('Outputs a registration form for customers.', 'modularity-resource-booking');
    }

    public function style()
    {
        if (file_exists(MODULARITYRESOURCEBOOKING_PATH . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('css/modularity-resource-booking.css'))) {
            // Enqueue module style
            wp_enqueue_style('modularity-resource-booking-css', MODULARITYRESOURCEBOOKING_URL . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('css/modularity-resource-booking.css'));
        }
    }

    public function data(): array
    {
        $data = get_fields($this->ID);
        $data['classes'] = implode(' ', apply_filters('Modularity/Module/Classes', array('box', 'box-panel'), $this->post_type, $this->args));
        $data['rest_url'] = get_rest_url();

        $customerGroups = get_terms('customer_group', array('hide_empty' => false));
        $data['customerGroups'] = !empty($customerGroups) ? array_map(function ($group) {
            return array(
                'id' => $group->term_id,
                'name' => $group->name,
                'slug' => $group->slug,
            );
        }, $customerGroups) : array();

        $data['customerGroups'] = json_encode($data['customerGroups']);

        if (!isset($data['google_recaptcha_site_key']) && defined('G_RECAPTCHA_KEY') && is_string(G_RECAPTCHA_KEY) && !empty(G_RECAPTCHA_KEY)) {
            $data['google_recaptcha_site_key'] = G_RECAPTCHA_KEY;
        }

        return $data;
    }

    
    public function script()
    {
        if (is_user_logged_in()) {
            return;
        }

        if (file_exists(MODULARITYRESOURCEBOOKING_PATH . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('js/RegistrationForm/Index.js'))) {
            // Enqueue react
            \Modularity\Helper\React::enqueue();
            // Enqueue module script
            wp_enqueue_script('modularity-' . $this->slug, MODULARITYRESOURCEBOOKING_URL . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('js/RegistrationForm/Index.js'), array('jquery', 'react', 'react-dom'), false, true);
            wp_localize_script(
                'modularity-' . $this->slug,
                'modRegistrationForm',
                array(
                    'email' => __('Email', 'modularity-resource-booking'),
                    'confirmEmail' => __('Confirm email', 'modularity-resource-booking'),
                    'emailMisMatch' => __('The email does not match.', 'modularity-resource-booking'),
                    'password' => __('Password', 'modularity-resource-booking'),
                    'confirmPassword' => __('Confirm password', 'modularity-resource-booking'),
                    'passwordMisMatch' => __('The password does not match.', 'modularity-resource-booking'),
                    'firstName' => __('First name', 'modularity-resource-booking'),
                    'lastName' => __('Last name', 'modularity-resource-booking'),
                    'company' => __('Company', 'modularity-resource-booking'),
                    'organizationNumber' => __('Organization number', 'modularity-resource-booking'),
                    'glnrNumber' => __('Glnr number', 'modularity-resource-booking') . ' (' . __('optional', 'modularity-resource-booking') . ')',
                    'vatNumber' => __('VAT number', 'modularity-resource-booking'),
                    'contactPerson' => __('Contact person', 'modularity-resource-booking'),
                    'phoneNumber' => __('Phone number', 'modularity-resource-booking'),
                    'website' => __('Website', 'modularity-resource-booking'),
                    'billingAddress' => __('Billing address', 'modularity-resource-booking'),
                    'register' => __('Register', 'modularity-resource-booking'),
                    'explanation' => array(
                        'vat' => __('Identifier for value added tax purposes.', 'modularity-resource-booking'),
                        'glnr' => __('Electronic invoice number', 'modularity-resource-booking')
                    ),
                    'headers' => array(
                        'billing' => __('Billing', 'modularity-resource-booking'),
                        'password' => __('Password', 'modularity-resource-booking')
                    ),
                    'selectCompanyType' => __('Select organisation type', 'modularity-resource-booking'),
                    'iAm' => __('I am', 'modularity-resource-booking')

                )
            );
        }
    }

    public function template()
    {
        return 'registration-form.blade.php';
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
