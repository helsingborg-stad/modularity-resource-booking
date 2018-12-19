<?php

namespace ModularityResourceBooking\Module;

class OrderHistory extends \Modularity\Module
{
    public $slug = 'order-history';
    public $supports = array();

    public function init()
    {
        $this->nameSingular = __('Order history', 'modularity-resource-booking');
        $this->namePlural = __('Order histories', 'modularity-resource-booking');
        $this->description = __('Outputs a list of previous orders.', 'modularity-resource-booking');
    }

    public function data() : array
    {
        $orders = $this->getOrders();
        $data = get_fields($this->ID);
        $data['classes'] = implode(' ', apply_filters('Modularity/Module/Classes', array('box', 'box-panel'), $this->post_type, $this->args));
        $data['orders'] = $orders;

        return $data;
    }

    public function getOrders()
    {
        $users = \ModularityResourceBooking\Api\TimeSlots::customerGroupMembers();
        $orders = \ModularityResourceBooking\Api\TimeSlots::getOrders(array('author__in' => $users));
        return $orders;
    }

    public function script()
    {
        if (file_exists(MODULARITYRESOURCEBOOKING_PATH . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('js/OrderHistory/Index.js'))) {
            // Enqueue react
            \Modularity\Helper\React::enqueue();
            // Enqueue module script
            wp_enqueue_script('modularity-' . $this->slug, MODULARITYRESOURCEBOOKING_URL . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('js/OrderHistory/Index.js'), array('jquery', 'react', 'react-dom'));
            wp_localize_script('modularity-' . $this->slug, 'modOrderHistory', array(
                'translation' => array(),
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