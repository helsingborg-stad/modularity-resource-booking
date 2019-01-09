<?php

namespace ModularityResourceBooking\;

/**
 * Class Mail
 *
 * @category Undefined
 * @package  ModularityResourceBooking
 * @author   Sebastian Thulin <sebastian.thulin@helsingborg.se>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://helsingborg.se
 */
class EconomyMail extends \Helper\Mail
{
    /**
     * Send manager email
     *
     * @param string $subject Defines a email subject
     * @param string $content Defines email content
     *
     * @return bool true if sent, false if undefined or malformed email
     */
    public static function __construct($subject, $content)
    {
        if (!is_wp_error($this->setReciver(get_option('mod_rb_manager_email')))) {
            $this->setSubject($subject);
            $this->setContent($content);
            $this->dispatch();
            return true;
        }
        return false;
    }
}
