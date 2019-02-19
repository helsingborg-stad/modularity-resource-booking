<?php

namespace ModularityResourceBooking;

class SingleOrder
{
    public function __construct()
    {
        //Modify content area
        add_action('the_content', array($this, 'filterContent'));
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function filterContent($content)
    {
        if (!is_singular(\ModularityResourceBooking\Orders::$postTypeSlug)) {
            return $content;
        }

        //Restrict access
        if (!current_user_can('administrator') && get_field('customer_id', get_queried_object_id())->ID !== (int) get_current_user_id()) {
            return __("Sorry, you don't have permission to view this order.", 'modularity-resource-booking');
        }

        $postId = get_queried_object_id();
        $orderData = get_post_meta($postId, 'order_data', true)[0];

        $summaryItem = array(
            'name' => '',
            'price' => '',
            'content' => ''
        );

        //Create data
        $data = array(
            'orderDetails' => array(
                'orderId' => [
                    'label' => __('Order ID', 'modularity-resource-booking'),
                    'value' => $orderData['order_id']
                ],
                'orderDate' => [
                    'label' => __('Order Date', 'modularity-resource-booking'),
                    'value' => get_the_date('', $postId)
                ],
                'campaginName' => [
                    'label' => __('Campaign name', 'modularity-resource-booking'),
                    'value' => $orderData['name']
                ],
                'status' => [
                    'label' => __('Status', 'modularity-resource-booking'),
                    'value' => get_the_terms($postId, 'order-status')[0]->name
                ]
            ),
        );

        $data['summary'] = array();
        $data['summary']['items'] = array_map(function ($article) {
            return array(
                'name' => $article['title'],
                'price' => (string)$article['price'] . ' ' . RESOURCE_BOOKING_CURRENCY_SYMBOL,
                'start' => $article['start'],
                'stop' => $article['stop']
            );
        }, $orderData['articles']);
        $data['summary']['totalPrice'] = (string)\ModularityResourceBooking\Orders::getTotalPrice($orderData['articles']) . ' ' . RESOURCE_BOOKING_CURRENCY_SYMBOL;

        if (isset($orderData['articles'][0])) {
            $mediaRequirements = $orderData['articles'][0]['type'] === 'package'
                ? \ModularityResourceBooking\Helper\Product::getPackageMediaRequirements($orderData['articles'][0]['id'])
                : get_field('media_requirement', get_queried_object_id());
            $uploadedMedia = (array) get_field('media_items', get_queried_object_id());

            if (count($mediaRequirements) > 0 && count($mediaRequirements) !== count($uploadedMedia)) {
                $data['uploadFormDataAttribute'] = json_encode(array(
                    'orderId' => get_queried_object_id(),
                    'articleId' => $orderData['articles'][0]['id'],
                    'articleType' => $orderData['articles'][0]['type'],
                    'restUrl' => get_rest_url(),
                    'restNonce' => wp_create_nonce('wp_rest')
                ));
            }
        }

        //Ensure that cache folder exits
        wp_mkdir_p(trailingslashit(wp_upload_dir()['basedir']) . 'cache/modularity-resource-booking/');

        //Run blade template
        $blade = new \Philo\Blade\Blade(
            MODULARITYRESOURCEBOOKING_PATH . "/templates",
            trailingslashit(wp_upload_dir()['basedir']) . 'cache/modularity-resource-booking/'
        );

        
        return $blade->view()->make('single-order', $data)->render();
    }
}
