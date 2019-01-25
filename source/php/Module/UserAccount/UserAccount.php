<?php

namespace ModularityResourceBooking\Module;

class UserAccount extends \Modularity\Module
{
    public $slug = 'rb-user-account';
    public $supports = array();

    public function init()
    {
        $this->nameSingular = __('User account settings', 'modularity-resource-booking');
        $this->namePlural = __('User account settings', 'modularity-resource-booking');
        $this->description = __('Outputs a form for user account settings.', 'modularity-resource-booking');
    }

    public function data(): array
    {
        $data = get_fields($this->ID);
        $data['classes'] = implode(' ', apply_filters('Modularity/Module/Classes', array('box', 'box-panel'), $this->post_type, $this->args));
        $data['user'] = json_encode($this->getUserData());
        $data['rest_url'] = get_rest_url();
        $data['nonce'] = wp_create_nonce('wp_rest');

        return $data;
    }

    public function getUserData(): array
    {
        if (!is_user_logged_in()) {
            return array();
        }

        $data = array(
            'id' => get_current_user_id(),
            'email' => wp_get_current_user()->data->user_email,
            'firstName' => get_user_meta(get_current_user_id(), 'first_name', true),
            'lastName' => get_user_meta(get_current_user_id(), 'last_name', true),
            'phone' => get_user_meta(get_current_user_id(), 'phone', true),
            'website' => get_userdata(get_current_user_id())->user_url,
            'company' => get_user_meta(get_current_user_id(), 'billing_company', true),
            'companyNumber' => get_user_meta(get_current_user_id(), 'billing_company_number', true),
            'billingAddress' => get_user_meta(get_current_user_id(), 'billing_address', true),
            'contactPerson' => get_user_meta(get_current_user_id(), 'billing_contact_person', true),
            'glnrNumber' => get_user_meta(get_current_user_id(), 'billing_glnr_number', true),
            'vatNumber' => get_user_meta(get_current_user_id(), 'billing_vat_number', true)
        );

        return $data;
    }

    public function script()
    {
        if (file_exists(MODULARITYRESOURCEBOOKING_PATH . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('js/RegistrationForm/Index.js'))) {
            // Enqueue react
            \Modularity\Helper\React::enqueue();

            // Enqueue module script
            wp_enqueue_script('modularity-' . $this->slug, MODULARITYRESOURCEBOOKING_URL . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('js/UserAccount/Index.js'), array('jquery', 'react', 'react-dom'));

            //Localize
            wp_localize_script('modularity-' . $this->slug, 'modUserAccount', array(
                'email' => __('Email', 'modularity-resource-booking'),
                'password' => __('Password', 'modularity-resource-booking'),
                'confirmPassword' => __('Confirm password', 'modularity-resource-booking'),
                'passwordMisMatch' => __('The password does not match.', 'modularity-resource-booking'),
                'firstName' => __('First name', 'modularity-resource-booking'),
                'lastName' => __('Last name', 'modularity-resource-booking'),
                'company' => __('Company', 'modularity-resource-booking'),
                'organizationNumber' => __('Organization number', 'modularity-resource-booking'),
                'glnrNumber' => __('Glnr number', 'modularity-resource-booking'),
                'vatNumber' => __('VAT number', 'modularity-resource-booking'),
                'contactPerson' => __('Contact person', 'modularity-resource-booking'),
                'phoneNumber' => __('Phone number', 'modularity-resource-booking'),
                'website' => __('Website', 'modularity-resource-booking'),
                'billingAddress' => __('Billing address', 'modularity-resource-booking'),
                'register' => __('Register', 'modularity-resource-booking'),
                'optional' => __('Optional setting', 'modularity-resource-booking'),
                'explanation' => array(
                    'vat' => __('Identifier for value added tax purposes.', 'modularity-resource-booking'),
                    'glnr' => __('Electronic invoice number', 'modularity-resource-booking')
                ),
                'headers' => array(
                    'billing' => __('Billing', 'modularity-resource-booking'),
                    'password' => __('Change password', 'modularity-resource-booking')
                ),
                'save' => __('Save', 'modularity-resource-booking')
            ));
        }
    }

    public function template()
    {
        return 'user-account.blade.php';
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
