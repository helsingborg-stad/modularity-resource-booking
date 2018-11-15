<?php

namespace ModularityResourceBooking;

class TimePeriod
{

    public function __construct()
    {
        add_action('init', array($this, 'registerOptionsPage'));
    }

    /**
     * Registers an options page
     *
     * @return void
     */
    public function registerOptionsPage()
    {
        if (function_exists('acf_add_options_page') ) {
            acf_add_options_page(
                array(
                    'page_title' => __('Time Slots', 'modularity-resource-booking')
                )
            );
        }
    }

    /**
     * Registers an options page
     *
     * @return array $slots Slots orderd by time
     */
    public function getSlots($onlyAvabile = true)
    {
        //if()
    }

}
