<?php

namespace ModularityResourceBooking\Mail;

class Controller
{
    public function __construct()
    {
        add_action('ModularityResourceBooking/Mail/Service/composeMail', array($this, 'composeCustomerDetails'), 99, 4);
        add_action('ModularityResourceBooking/Mail/Service/composeMail', array($this, 'composeOrderDetails'), 99, 4);
        add_action('ModularityResourceBooking/Mail/Service/composeMail', array($this, 'composeOrderSummary'), 99, 4);
        add_action('ModularityResourceBooking/Mail/Service/composeMail', array($this, 'composeLinks'), 99, 4);
    }

    /**
     * Undocumented function
     *
     * @param [type] $mail
     * @param [type] $templateId
     * @param [type] $orderId
     * @param [type] $userId
     * @return void
     */
    public function composeLinks($mail, $templateId, $orderId, $userId)
    {
        if (!is_array(get_field('links', $templateId)) || empty(get_field('links', $templateId))) {
            return $mail;
        }

        $dynamicPages = array(
            'customerOrderPage' => isset($orderId) && $orderId > 0
                                    ? get_permalink($orderId)
                                    : false,
            'adminOrderPage' => isset($orderId) && $orderId > 0
                                    ? get_admin_url() . 'post.php?post=' . $orderId . '&action=edit'
                                    : false,
            'adminCustomerPage' => isset($userId) && $userId > 0
                                    ? get_admin_url() . 'user-edit.php?user_id=' . $userId
                                    : false
        );

        $links = array();

        foreach (get_field('links', $templateId) as $linkType) {
            if (!isset($linkType['acf_fc_layout'])) {
                continue;
            }

            switch ($linkType['acf_fc_layout']) {
                // Custom link
                case 'custom_link':
                    if (isset($linkType['link'])
                    && is_array($linkType['link'])
                    && isset($linkType['link']['title'])
                    && isset($linkType['link']['url'])
                    && !empty($linkType['link']['title'])
                    && !empty($linkType['link']['url'])) {
                        $links[] = array(
                            'text' => $linkType['link']['title'],
                            'url' => $linkType['link']['url']
                        );
                    }
                    break;
                // Dynamic link
                case 'dynamic_link':
                    if (isset($linkType['dynamic_page'])
                    && isset($linkType['link_text'])
                    && !empty($linkType['link_text'])
                    && isset($dynamicPages[$linkType['dynamic_page']])
                    && $dynamicPages[$linkType['dynamic_page']]) {
                        $links[] = array(
                            'text' => $linkType['link_text'],
                            'url' => $dynamicPages[$linkType['dynamic_page']]
                        );
                    }
            }
        }

        if (is_array($links) && !empty($links)) {
            $mail->links = $links;
        }

        return $mail;
    }

    /**
     * Undocumented function
     *
     * @param [type] $mail
     * @param [type] $templateId
     * @param [type] $orderId
     * @param [type] $userId
     * @return void
     */
    public function composeOrderDetails($mail, $templateId, $orderId, $userId)
    {
        if (empty($templateId)
            || !is_array(get_field('additional_content', $templateId))
            || !in_array('orderDetails', get_field('additional_content', $templateId))
            || empty(get_field('order_details_fields', $templateId))
            || !$orderId) {
            return $mail;
        }

        $sectionTitle = !empty(get_field('order_details_heading', $templateId))
                        ? get_field('order_details_heading', $templateId)
                        : __('Order details', 'modularity-resource-booking');
        $sectionTable = array();

        $orderFields = array(
            'orderId' => [
                'heading' => __('Order ID', 'modularity-resource-booking'),
                'content' => get_post_meta($orderId, 'order_id', true)
            ],
            'orderDate' => [
                'heading' => __('Order Date', 'modularity-resource-booking'),
                'content' => get_the_date('', $orderId)
            ],
            'campaignName' => [
                'heading' => __('Campaign name', 'modularity-resource-booking'),
                'content' => get_the_title($orderId),
            ]
        );
        foreach (get_field('order_details_fields', $templateId) as $key) {
            if (isset($orderFields[$key])
                && isset($orderFields[$key]['heading'])
                && isset($orderFields[$key]['content'])) {
                $sectionTable[] = array(
                    'heading' => $orderFields[$key]['heading'],
                    'content' => $orderFields[$key]['content']
                );
            }
        }


        $mail->addSection($sectionTitle, $sectionTable);

        return $mail;
    }

    /**
     * Undocumented function
     *
     * @param [type] $mail
     * @param [type] $templateId
     * @param [type] $orderId
     * @param [type] $userId
     * @return void
     */
    public function composeCustomerDetails($mail, $templateId, $orderId, $userId)
    {
        if (empty($templateId)
            || !is_array(get_field('additional_content', $templateId))
            || !in_array('customerDetails', get_field('additional_content', $templateId))
            || empty(get_field('customer_details_fields', $templateId))
            || !$userId) {
            return $mail;
        }

        $sectionTitle = !empty(get_field('customer_details_heading', $templateId))
                        ? get_field('customer_details_heading', $templateId)
                        : __('Customer', 'modularity-resource-booking');
        $sectionTable = array();


        $customerFields = \ModularityResourceBooking\Helper\Customer::getCustomerData($userId);
        $fieldHeadings = \ModularityResourceBooking\Helper\Customer::getCustomerData($userId, true);
        foreach (get_field('customer_details_fields', $templateId) as $key) {
            if (isset($customerFields[$key]) && isset($fieldHeadings[$key])) {
                $sectionTable[] = array(
                    'heading' => $fieldHeadings[$key],
                    'content' => $customerFields[$key]
                );
            }
        }


        $mail->addSection($sectionTitle, $sectionTable);

        return $mail;
    }

    /**
     * Undocumented function
     *
     * @param [type] $mail
     * @param [type] $templateId
     * @param [type] $orderId
     * @param [type] $userId
     * @return void
     */
    public function composeOrderSummary($mail, $templateId, $orderId, $userId)
    {
        if (empty($templateId)
            || !is_array(get_field('additional_content', $templateId))
            || !in_array('orderSummary', get_field('additional_content', $templateId))
            || !$orderId) {
            return $mail;
        }

        $articles = get_post_meta($orderId, 'order_data', true)[0]['articles'];

        if (!empty($articles) && is_array($articles)) {
            $mail->summary = array(
                'title' => __('Summary', 'modularity-resource-booking'),
                'items' => array_map(function ($article) {
                    $startDate = new \DateTime($article['start']);
                    return array(
                        'title' => $article['title'],
                        'content' => [__('Start date', 'modularity-resource-booking') . ': ' . $article['start'], __('End date', 'modularity-resource-booking') . ': ' . $article['stop']],
                        'price' => (string)$article['price'] . ' ' . RESOURCE_BOOKING_CURRENCY_SYMBOL,
                        'week' => __('Week', 'modularity-resource-booking') . ' ' . $startDate->format('W')
                    );
                }, $articles),
                'totalPrice' => (string)\ModularityResourceBooking\Orders::getTotalPrice($articles) . ' ' . RESOURCE_BOOKING_CURRENCY_SYMBOL,
                'totalTitle' => __('Total', 'modularity-resource-booking'),
            );
        }
        

        $mail->vat = isset($userId) && (int) $userId > 0  ? \ModularityResourceBooking\Helper\Customer::getTaxIndicator($userId) : '';

        return $mail;
    }
}
