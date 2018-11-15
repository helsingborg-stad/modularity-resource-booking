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
        new Packages(); //Packages of prodcts
        new TimePeriod(); // Time slots that packages can be reserved for

        //Ordering
        new Orders(); //Order data
        new Frontend\Orders();

        //Account stuff
        new Customer();


        /*
        //Users (ei. users & roles)
        new Customers(); //Manages system users that are considerd customers

        //Options
        new Slots(); //Time slots that are orderable
        new CustomerGroups(); //Dynamic creation of roles*/
    }
}
