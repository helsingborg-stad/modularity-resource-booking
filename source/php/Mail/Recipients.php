<?php

namespace ModularityResourceBooking\Mail;

class Recipients
{
    public function __construct()
    {
        add_action('ModularityResourceBooking/Mail/Service/recipients', array($this, 'parseCustomerRecipient'), 99, 4);
        add_action('ModularityResourceBooking/Mail/Service/recipients', array($this, 'parseDynamicRecipients'), 99, 1);
    }

    /**
     * Undocumented function
     *
     * @param [type] $recpientString
     * @param [type] $templateId
     * @param [type] $orderId
     * @param [type] $userId
     * @return void
     */
    public function parseCustomerRecipient($recpientString, $templateId, $orderId, $userId)
    {
        if ($userId) {
            $customerShortCode = '[customer-mail]';
            if (strpos($recpientString, $customerShortCode) !== false) {
                $recpientString = str_replace($customerShortCode, \ModularityResourceBooking\Helper\Customer::getEmail($userId), $recpientString);
            }
        }

        return $recpientString;
    }

    /**
     * Undocumented function
     *
     * @param [type] $recpientString
     * @return void
     */
    public function parseDynamicRecipients($recpientString)
    {
        $dynamicRecipients = array(
            [
                'shortcode' => '[manager-mail]',
                'optionKey' => 'mod_rb_manager_email'
            ],
            [
                'shortcode' => '[economy-mail]',
                'optionKey' => 'mod_rb_economy_email'
            ]
        );

        foreach ($dynamicRecipients as $dynamicRecpient) {
            if (strpos($recpientString, $dynamicRecpient['shortcode']) !== false) {
                $recpientString = str_replace($dynamicRecpient['shortcode'], get_field($dynamicRecpient['optionKey'], 'option'), $recpientString);
            }
        }

        return $recpientString;
    }
}
