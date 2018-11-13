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
        add_action('plugins_loaded', array($this, 'init'));
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
    }
}
