<?php

namespace ModularityResourceBooking\Helper;

/**
 * Class Mail
 *
 * @category Undefined
 * @package  ModularityResourceBooking
 * @author   Sebastian Thulin <sebastian.thulin@helsingborg.se>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://helsingborg.se
 */
class CustomerMail extends Mail
{
    /**
     * Send customer email
     *
     * @param string|integer $reciver Defines a email (may also be a user id)
     * @param string         $subject Defines a email subject
     * @param string         $content Defines email content
     *
     * @return bool true if sent, false if undefined or malformed email
     */
    public function __construct($reciver, $subject, $content, $data = array())
    {
        if(is_numeric($reciver) && $email = \ModularityResourceBooking\Helper\Customer::getEmail((int) $reciver)) {
            $reciver = $email;
        }

        if (!is_wp_error($this->setReciver($reciver))) {
            $this->setSubject($subject);
            $this->setContent($content);

            if (isset($data['table']) && !empty($data['table'])) {
                $this->setTable($data['table']);
                unset($data['table']);
            }

            if (isset($data['links']) && !empty($data['links'])) {
                $this->setLinks($data['links']);
                unset($data['links']);
            }

            $this->data = $data;
            $this->dispatch();
            return true;
        }
        return false;
    }
}
