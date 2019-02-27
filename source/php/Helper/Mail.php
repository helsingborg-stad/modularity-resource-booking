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
class Mail extends \ModularityResourceBooking\Helper\ErrorHandler
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $recipients = array();

    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $headers = array('Content-Type: text/html; charset=UTF-8');

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $subject = '';

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $content = '';

    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $sections = array();

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $color = 'black';

    /**
     * Set email reciver
     *
     * @param string $reciver Appends a new reciver to the recivers array
     *
     * @return true if set, WP_Error if malformed email.
     */
    public function addRecipient($recipient)
    {
        if (!empty($recipient) && is_array($recipient)) {
            $error = false;
            foreach ($recipient as $email) {
                if (is_WP_Error($this->addRecipient($email))) {
                    $error = true;
                }
            }

            return $error ? false : true;
        }

        if (is_email($recipient) && !stripos($recipient, "+")) {
            $this->recipients[] = $recipient;
            return true;
        }
        
        return $this->addError(__FUNCTION__, __("The email provided " . $recipient . " was not in a valid format.", 'modularity-resource-booking'));
    }

    /**
     * Set email subject
     *
     * @param string $subject Overwrites the subject, if not empty
     *
     * @return true if set, WP_Error if empty subject.
     */
    public function setSubject(string $subject)
    {
        if (!empty($subject)) {
            $this->subject = $subject;
            return true;
        }
        return $this->addError(__FUNCTION__, __("The subject cannot be empty.", 'modularity-resource-booking'));
    }

    /**
     * Set email content
     *
     * @param string $content Appends a new reciver to the recivers array
     *
     * @return true if set, WP_Error if malformed email.
     */
    public function setContent(string $content)
    {
        if (!empty($content)) {
            $this->content = apply_filters('the_content', $content);
            return true;
        }
        return $this->addError(__FUNCTION__, __("The content cannot be empty.", 'modularity-resource-booking'));
    }

    /**
     * Undocumented function
     *
     * @param string $color
     * @return void
     */
    public function setColor(string $color)
    {
        if (!empty($color)) {
            $this->color = $color;
        }
    }

    /**
     * Adds email section
     *
     * @param string  $sectionTitle
     * @param array   $table           Array containing table rows array(array('heading' => 'Title', 'content' => 'The content'))
     * @param boolean $removeEmptyRows Boolean to controll if empty rows should be stripped from mail.
     *
     * @return true if set, WP_Error if malformed.
     */
    public function addSection(string $sectionTitle, array $table, $removeEmptyRows = true)
    {
        if (!empty($table) && !empty($sectionTitle)) {
            foreach ($table as $rowKey => $row) {
                //Validate item object
                if (!isset($row['heading']) || !isset($row['content'])) {
                    return $this->addError('malformed_section_table', __("Each table array item must contain 'content' and 'heading' sub keys.", 'modularity-resource-booking'));
                }

                //Remove empty row
                if ((boolean)$removeEmptyRows && empty($row['content'])) {
                    if (isset($table[$rowKey])) {
                        unset($table[$rowKey]);
                    }
                }

                //Remove rows containing WP_Error objects
                if (is_WP_Error($row['content'])) {
                    if (isset($table[$rowKey])) {
                        unset($table[$rowKey]);
                    }
                }
            }
            
            $this->sections[] = array(
                'title' => $sectionTitle,
                'table' => $table
            );

            return true;
        }

        return $this->addError(__FUNCTION__, __("Section title and table content is required", 'modularity-resource-booking'));
    }

    /**
     * Convert content to HTML
     *
     * @return string Html formatted string
     */
    public function html()
    {
        //Create data
        $data = get_object_vars($this);

        //Ensure that cache folder exits
        wp_mkdir_p(trailingslashit(wp_upload_dir()['basedir']) . 'cache/modularity-resource-booking/');

        //Run blade template
        $blade = new \Philo\Blade\Blade(
            MODULARITYRESOURCEBOOKING_PATH . "/templates",
            trailingslashit(wp_upload_dir()['basedir']) . 'cache/modularity-resource-booking/'
        );
        return $blade->view()->make('email', $data)->render();
    }

    /**
     * Validate input data and send the actual mail.
     *
     * @return true if sent, WP_Error if malformed input, false if unknown failure.
     */
    public function dispatch()
    {
        if (empty($this->recipients)) {
            return $this->addError(__FUNCTION__, __("Undefined recipient(s). Cannot send mail without any recipient", 'modularity-resource-booking'));
        }

        return wp_mail(
            implode(", ", $this->recipients),
            $this->subject . " - " . get_bloginfo('name'),
            $this->html(),
            $this->headers
        );
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
