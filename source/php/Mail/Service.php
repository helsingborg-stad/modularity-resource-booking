<?php

namespace ModularityResourceBooking\Mail;

/**
 * Undocumented class
 * @author Nikolas Ramstedt <nikolas.ramstedt@helsingborg.se>
 */
class Service extends \ModularityResourceBooking\Helper\ErrorHandler
{
    /**
     * Undocumented variable
     *
     * @var integer
     */
    protected $templateId = 0;

    /**
     * Undocumented variable
     *
     * @var integer
     */
    protected $orderId = 0;

    /**
     * Undocumented variable
     *
     * @var integer
     */
    protected $userId = 0;

    /**
     * Undocumented variable
     *
     * @var boolean
     */
    protected $composed = false;

    /**
     * Undocumented variable
     *
     * @var [type]
     */
    protected $mail;

    /**
     * Undocumented function
     *
     * @param [type] $template
     */
    public function __construct($template)
    {
        $this->error = new \WP_Error();
        $this->templateId = $this->_getTemplateId($template);
    }

    /**
     * Undocumented function
     *
     * @param [type] $order
     * @return void
     */
    public function setOrder($order)
    {
        if (get_post_type($order) !== 'purchase') {
            return $this->addError(__FUNCTION__, __('Cannot find order. Param $order must be a post id (numeric value) or WP_Post object and the post type should be "purchase".', 'modularity-resource-booking'));
        }

        $this->orderId = is_numeric($order) ? $order : $order->ID;
        return true;
    }

    /**
     * Undocumented function
     *
     * @param [type] $user
     * @return void
     */
    public function setUser($user)
    {
        if (!is_numeric($user) && !is_object($user)
            || !is_numeric($user) && get_class($user) !== 'WP_User'
            || is_numeric($user) && !get_user_by('ID', $user)) {
            return $this->addError(__FUNCTION__, __('Cannot find user. Param $user must be a user id (numeric value) or a WP_User object.', 'modularity-resource-booking'));
        }

        $this->userId = is_numeric($user) ? $user : $user->ID;
        return true;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function composeMail()
    {
        if ($this->composed) {
            return $this->addError(__FUNCTION__, __('composeMail() method can only be run once within a MailAgent instance.', 'modularity-resource-booking'));
        }

        if ($this->templateId) {
            $mail = new \ModularityResourceBooking\Helper\Mail();
            $mail->setErrorObject($this->error);
            $mail->addRecipient($this->_getRecipients());
            $mail->setContent(get_field('content', $this->templateId));
            $mail->setSubject(get_field('subject', $this->templateId));
            $mail->setColor(get_field('mod_rb_email_brand_color', 'options'));

            //Controller
            $this->mail = apply_filters('ModularityResourceBooking/Mail/Service/composeMail', $mail, $this->templateId, $this->orderId, $this->userId);

            if (is_object($this->mail) && get_class($this->mail) === 'ModularityResourceBooking\Entity\Mail') {
                return $this->composed = true;
            }

            return $this->addError(__FUNCTION__, __('Failed to compose mail because of invalid mail object.', 'modularity-resource-booking'));
        }

        return $this->addError(__FUNCTION__, __('Failed to compose mail because of invalid Template ID', 'modularity-resource-booking'));
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function sendMail()
    {
        if (!$this->composed) {
            return $this->addError(__FUNCTION__, __('Cannot send the mail without composing the mail object. Please use composeMail() before this method.', 'modularity-resource-booking'));
        }

        return $this->mail->send();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function previewMail()
    {
        if (!$this->composed) {
            return $this->addError(__FUNCTION__, __('Cannot preview the mail without composing the mail object. Please use composeMail() before this method.', 'modularity-resource-booking'));
        }

        return $this->mail->html();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function _getRecipients()
    {
        $recipients = apply_filters(
            'ModularityResourceBooking/Mail/Service/recipients',
            get_field('reciver', $this->templateId),
            $this->templateId,
            $this->orderId,
            $this->userId
        );

        if (!empty($recipients)) {
            //rm Whitespace
            $recipients = str_replace(' ', '', $recipients);

            return array_filter(explode(',', $recipients), function ($recipient) {
                //rm empty items
                return !empty($recipient);
            });
        }

        return '';
    }

    /**
     * Undocumented function
     *
     * @param [type] $template
     * @return void
     */
    protected function _getTemplateId($template)
    {
        if (get_post_type($template) !== \ModularityResourceBooking\Mail::$postTypeSlug) {
            $this->addError(__FUNCTION__, __('Template could not be found.', 'modularity-resource-booking'));
            return false;
        }

        return is_numeric($template) ? $template : $template->ID;
    }
}
