<?php

namespace ModularityResourceBooking;

class App
{
    public function __construct()
    {
        //Enqueue styles / js
        add_action('admin_enqueue_scripts', array($this, 'enqueueStyles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));

        //Register plugin classes
        $this->init();

        // Register Modularity modules
        add_action('plugins_loaded', array($this, 'registerModules'));

        //Register currency symbol
        add_action('init', array($this, 'registerCurrencySymbol'), 1);

    }

    /**
     * Enqueue required style
     *
     * @return void
     */
    public function registerCurrencySymbol()
    {
        if (!defined('RESOURCE_BOOKING_CURRENCY_SYMBOL')) {
            define('RESOURCE_BOOKING_CURRENCY_SYMBOL', __('USD', 'modularity-resource-booking'));
        }
    }

    /**
     * Enqueue required style
     *
     * @return void
     */
    public function enqueueStyles()
    {
        wp_register_style('modularity-resource-booking-css', MODULARITYRESOURCEBOOKING_URL . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('css/modularity-resource-booking.css'));
    }

    /**
     * Enqueue required scripts
     *
     * @return void
     */
    public function enqueueScripts()
    {
        wp_register_script('modularity-resource-booking-js', MODULARITYRESOURCEBOOKING_URL . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('js/modularity-resource-booking.js'));
        wp_register_script('google-maps-api', 'https://maps.googleapis.com/maps/api/js?key='.GOOGLE_API_KEY.'&callback=initMap', false, NULL, true);

    }

    /**
     * Init plugin classes
     *
     * @return void
     */
    public function init()
    {
        //Product database
        new Products(); //Main product library
        new Settings(); // Time slots that packages can be reserved for

        //Ordering
        new Orders(); //Order data

        //Account stuff
        new Customer();

        //Fronted API
        new Api\Orders();
        new Api\TimeSlots();
        new Api\Products();
        new Api\Customer();
    }

    /**
     * Register Modularity v2 modules
     * @return void
     */
    public function registerModules()
    {
        if (function_exists('modularity_register_module')) {
            modularity_register_module(
                MODULARITYRESOURCEBOOKING_PATH . 'source/php/Module/Registration',
                'RegistrationForm'
            );
            modularity_register_module(
                MODULARITYRESOURCEBOOKING_PATH . 'source/php/Module/UserAccount',
                'UserAccount'
            );
            modularity_register_module(
                MODULARITYRESOURCEBOOKING_PATH . 'source/php/Module/OrderHistory',
                'OrderHistory'
            );
            modularity_register_module(
                MODULARITYRESOURCEBOOKING_PATH . 'source/php/Module/BookingForm',
                'BookingForm'
            );
            modularity_register_module(
                MODULARITYRESOURCEBOOKING_PATH . 'source/php/Module/PackageMap',
                'PackageMap'
            );
        }
    }


}
