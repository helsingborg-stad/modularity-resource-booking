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
        $data = get_fields($this->ID);
        $data['classes'] = implode(' ', apply_filters('Modularity/Module/Classes', array('box', 'box-panel'), $this->post_type, $this->args));

        return $data;
    }

    public function script()
    {
        if (!is_user_logged_in()) {
            return;
        }

        if (file_exists(MODULARITYRESOURCEBOOKING_PATH . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('js/OrderHistory/Index.js'))) {
            // Enqueue react
            \Modularity\Helper\React::enqueue();
            // Enqueue module script
            wp_enqueue_script('modularity-' . $this->slug, MODULARITYRESOURCEBOOKING_URL . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('js/OrderHistory/Index.js'), array('jquery', 'react', 'react-dom'));
            wp_localize_script('modularity-' . $this->slug, 'modOrderHistory', array(
                'translation' => array(
                    'next' => __('Next', 'modularity-resource-booking'),
                    'prev' => __('Previous', 'modularity-resource-booking'),
                    'somethingWentWrong' => __('Something went wrong.', 'modularity-resource-booking'),
                    'noOrdersFound' => __('No orders found.', 'modularity-resource-booking'),
                    'orderNumber' => __('Order #', 'modularity-resource-booking'),
                    'date' => __('Date', 'modularity-resource-booking'),
                    'status' => __('Status', 'modularity-resource-booking'),
                    'article' => __('Article', 'modularity-resource-booking'),
                    'type' => __('Type', 'modularity-resource-booking'),
                    'price' => __('Price', 'modularity-resource-booking'),
                    'period' => __('Period', 'modularity-resource-booking'),
                    'product' => __('Product', 'modularity-resource-booking'),
                    'package' => __('Package', 'modularity-resource-booking'),
                    'canceled' => __('Canceled', 'modularity-resource-booking'),
                    'cancelOrder' => __('Cancel order', 'modularity-resource-booking'),
                    'cancelOrderConfirm' => __('Do you really want to cancel this order?', 'modularity-resource-booking'),
                ),
                'restUrl' => get_rest_url(),
                'nonce' => wp_create_nonce('wp_rest')
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
