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
class EconomyMail extends Mail
{
    /**
     * Send manager email
     *
     * @param string $subject Defines a email subject
     * @param string $content Defines email content
     *
     * @return bool true if sent, false if undefined or malformed email
     */
    public function __construct($subject, $content, $table = array())
    {
        if (!is_wp_error($this->setReciver(get_field('mod_rb_economy_email', 'option')))) {
            $this->setSubject($subject);
            $this->setContent($content);
            $this->setTable($table);
            $this->dispatch();
            return true;
        }
        return false;
    }
}
