<?php

namespace ModularityResourceBooking\Helper;

/**
 * Class Mail
 *
 * @category Helpers
 * @package  ModularityResourceBooking\Helper
 * @author   Sebastian Thulin <sebastian.thulin@helsingborg.se>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://helsingborg.se
 */
class Mail
{

    private $_reciver = array();
    private $_subject = null;
    private $_content = null;

    /**
     * Set email reciver
     *
     * @param string $reciver Appends a new reciver to the recivers array
     *
     * @return true if set, WP_Error if malformed email.
     */
    public function setReciver($reciver)
    {
        if (is_email($reciver) && !stripos($reciver, "+")) {
            $this->_reciver[] = $reciver;
            return true;
        }
        return new WP_Error('malformed_email_adress', __("The email provided was not in a valid format.", 'modularity-resource-booking'));
    }

    /**
     * Set email subject
     *
     * @param string $subject Overwrites the subject, if not empty
     *
     * @return true if set, WP_Error if empty subject.
     */
    public function setSubject($subject)
    {
        if (!empty($subject)) {
            $this->_subject = $subject;
            return true;
        }
        return new WP_Error('empty_subject', __("The subject cannot be empty.", 'modularity-resource-booking'));
    }

    /**
     * Set email content
     *
     * @param string $content Appends a new reciver to the recivers array
     *
     * @return true if set, WP_Error if malformed email.
     */
    public function setContent($content)
    {
        if (!empty($content)) {
            $this->_content = $content;
            return true;
        }
        return new WP_Error('empty_content', __("The content cannot be empty.", 'modularity-resource-booking'));
    }

    /**
     * Validate input data and send the actual mail.
     *
     * @return true if sent, WP_Error if malformed input, false if unknown failure.
     */
    public function dispatch()
    {
        if (empty($this->_reciver)) {
            return new WP_Error('undefined_reciver', __("Undefined reciver(s).", 'modularity-resource-booking'));
        }

        if (is_null($this->_subject)) {
            return new WP_Error('undefined_subject', __("Undefined subject line.", 'modularity-resource-booking'));
        }

        if (is_null($this->_content)) {
            return new WP_Error('undefined_content', __("Undefined content data.", 'modularity-resource-booking'));
        }

        return wp_mail(implode(", ", $this->_reciver), $this->_subject, $this->_content);
    }

    /**
     * Send email, wrapping function for easier naming.
     *
     * @return true if set, WP_Error if malformed email.
     */
    public function send()
    {
        return $this->dispatch();
    }
}
