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
    public function __construct($reciver, $subject, $content, $table = array(), $links = array())
    {
        if(is_integer($reciver) && $email = \ModularityResourceBooking\Helper\Customer::getEmail($reciver)) {
            $reciver = $email;
        }

        if (!is_wp_error($this->setReciver($reciver))) {
            $this->setSubject($subject);
            $this->setContent($content);
            $this->setTable($table);
            $this->setLinks($links);
            $this->dispatch();
            return true;
        }
        return false;
    }
}
