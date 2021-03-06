<?php

namespace ModularityResourceBooking\Module;

class BookingForm extends \Modularity\Module
{
    public $slug = 'rb-booking-form';
    public $supports = array();

    public function init()
    {
        $this->nameSingular = __('Resource Booking Form', 'modularity-resource-booking');
        $this->namePlural = __('Resource Booking Forms', 'modularity-resource-booking');
        $this->description = __('Outputs a form for booking resources.', 'modularity-resource-booking');
    }

    public function data() : array
    {
        $data = get_fields($this->ID);
        $data['classes'] = implode(' ', apply_filters('Modularity/Module/Classes', array(), $this->post_type, $this->args));
        $data['dataAttributes'] = array(
            'restUrl' => get_rest_url(),
            'restNonce' => wp_create_nonce('wp_rest'),
            'articleType' => get_field('article_type', $this->ID),
            'articleId' => get_field('article_type', $this->ID) == 'package' ? get_field('package_id', $this->ID) : 0,
            'userId' => get_current_user_id(),
            'orderHistoryPage' => get_field('order_history_page', 'options') ? get_permalink(get_field('order_history_page', 'options')) : '',
            'locale' => get_locale() === 'sv_SE' ? 'sv' : 'en',
            'headings' => array(
                'orderName' => get_field('campaign_heading', $this->ID) ? get_field('campaign_heading', $this->ID) : __('1. Enter campaign name', 'modularity-resource-booking'),
                'calendar' => get_field('calendar_heading', $this->ID) ? get_field('calendar_heading', $this->ID) : __('2. Select advertising period', 'modularity-resource-booking'),
                'files' => get_field('files_heading', $this->ID) ? get_field('files_heading', $this->ID) : __('3. Upload files', 'modularity-resource-booking'),
                'summary' => get_field('summary_heading', $this->ID) ? get_field('summary_heading', $this->ID) : __('Summary', 'modularity-resource-booking')
            )

        );
        return $data;
    }

    public function style()
    {
        if (file_exists(MODULARITYRESOURCEBOOKING_PATH . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('css/modularity-resource-booking.css'))) {
            // Enqueue module script
            wp_enqueue_style('modularity-resource-booking-css', MODULARITYRESOURCEBOOKING_URL . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('css/modularity-resource-booking.css'));
        }
    }

    public function script()
    {
        if (file_exists(MODULARITYRESOURCEBOOKING_PATH . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('js/BookingForm/Index.js'))) {
            // Enqueue react
            \Modularity\Helper\React::enqueue();

            // Enqueue module script
            wp_enqueue_script('modularity-' . $this->slug, MODULARITYRESOURCEBOOKING_URL . '/dist/' . \ModularityResourceBooking\Helper\CacheBust::name('js/BookingForm/Index.js'), array('jquery', 'react', 'react-dom'), false, true);

            //Localize
            wp_localize_script('modularity-' . $this->slug, 'modResourceBookingForm', array(
                'translation' => array(
                    'currency' => RESOURCE_BOOKING_CURRENCY_SYMBOL,
                    'vat' => \ModularityResourceBooking\Helper\Customer::getTaxIndicator(get_current_user_id()),
                    'start' => __('Start date', 'modularity-resource-booking'),
                    'end' => __('End date', 'modularity-resource-booking'),
                    'product' => __('Product', 'modularity-resource-booking'),
                    'total' => __('Total', 'modularity-resource-booking'),
                    'goback' => __('Go back', 'modularity-resource-booking'),
                    'order' => __('Order', 'modularity-resource-booking'),
                    'noSlots' => __('Could not find any slots, please contact an administrator.', 'modularity-resource-booking'),
                    'newOrder' => __('Make a new order', 'modularity-resource-booking'),
                    'add' => __('Add', 'modularity-resource-booking'),
                    'remove' => __('Remove', 'modularity-resource-booking'),
                    'week' => __('Week', 'modularity-resource-booking'),
                    'dimensions' => __('Dimensions', 'modularity-resource-booking'),
                    'maxFileSize' => __('Max Filesize', 'modularity-resource-booking'),
                    'allowedFileTypes' => __('Allowed Filetypes', 'modularity-resource-booking'),
                    'selectAtleastOneDate' => __('Please select atleast one date in the calendar', 'modularity-resource-booking'),
                    'campaignName' => __('Campaign name', 'modularity-resource-booking'),
                )
            ));
        }
    }


    public function template()
    {
        return 'booking-form.blade.php';
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
