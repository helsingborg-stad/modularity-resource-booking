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
    private $_table = array();
    private $_links = array();
    private $_headers = array('Content-Type: text/html; charset=UTF-8');

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
        return new \WP_Error('malformed_email_adress', __("The email provided was not in a valid format.", 'modularity-resource-booking'));
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
        return new \WP_Error('empty_subject', __("The subject cannot be empty.", 'modularity-resource-booking'));
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
        return new \WP_Error('empty_content', __("The content cannot be empty.", 'modularity-resource-booking'));
    }

    /**
     * Set email table
     *
     * @param array   $table           Array containing table rows array(array('heading' => 'Title', 'content' => 'The content'))
     * @param boolean $removeEmptyRows Boolean to controll if empty rows should be stripped from mail.
     *
     * @return true if set, WP_Error if malformed.
     */
    public function setTable($table, $removeEmptyRows = true)
    {
        if (!empty($table) && is_array($table)) {
            foreach ($table as $rowKey => $row) {
                //Validate item object
                if (!isset($row['heading']) ||!isset($row['content'])) {
                    return new \WP_Error('malformed_table_content', __("Each table array item must contain 'content' and 'heading' sub keys.", 'modularity-resource-booking'));
                }

                //Remove empty row
                if ((boolean) $removeEmptyRows && empty($row['content'])) {
                    if (isset($table[$rowKey])) {
                        unset($table[$rowKey]);
                    }
                }

                //Remove rows containing WP_Error objects
                if (is_wp_error($row['content'])) {
                    if (isset($table[$rowKey])) {
                        unset($table[$rowKey]);
                    }
                }
            }
            $this->_table = $table;

            return true;
        }

        return new \WP_Error('malformed_table', __("The content cannot be empty.", 'modularity-resource-booking'));
    }

    /**
     * Convert content to HTML
     *
     * @return string Html formatted string
     */
    public function html($content)
    {
        //Create data
        $data = array(
            'title' => $this->_subject,
            'preheader' => wp_trim_words($content, 10, "..."),
            'content' => apply_filters('the_content', $content),
            'table' => $this->_table,
            'links' => $this->_links,
            'color' => get_field('mod_rb_email_brand_color', 'options'),
            'summary' => isset($this->data['summary']) ? $this->data['summary'] : false
        );

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
        if (empty($this->_reciver)) {
            return new \WP_Error('undefined_reciver', __("Undefined reciver(s).", 'modularity-resource-booking'));
        }

        if (is_null($this->_subject)) {
            return new \WP_Error('undefined_subject', __("Undefined subject line.", 'modularity-resource-booking'));
        }

        if (is_null($this->_content)) {
            return new \WP_Error('undefined_content', __("Undefined content data.", 'modularity-resource-booking'));
        }

        return wp_mail(
            implode(", ", $this->_reciver),
            $this->_subject . " - " . get_bloginfo('name'),
            $this->html($this->_content),
            $this->_headers
        );
    }

    /**
     * Set links
     *
     * @param array $buttons List with button text and link url
     *
     * @return void
     */
    public function setLinks($links)
    {
        if (is_array($links) && !empty($links)) {
            foreach ($links as $key => &$link) {
                //Validate item object
                if (!isset($link['url']) ||!isset($link['text'])) {
                    return new \WP_Error('malformed_link_array', __("Each button array item must contain 'url' and 'text' sub keys.", 'modularity-resource-booking'));
                }

                //Remove empty buttons
                if (empty($link['url']) || empty($link['text'])) {
                    unset($links[$key]);
                }
            }
        }

        $this->_links = empty($links) ? array() : $links;
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
