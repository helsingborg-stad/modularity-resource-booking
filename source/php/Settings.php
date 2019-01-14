<?php

namespace ModularityResourceBooking;

class Settings
{

    public function __construct()
    {
        add_action('init', array($this, 'registerOptionsPage'));

        //Add nonce message for su admins
        add_action('admin_notices', array($this, 'nonceKeyMessage'));

        //Remove passed timeslots
        add_filter('acf/load_value/key=field_5bed4e08b48ec', array($this, 'removeObsoleteTimeSlots'), 10, 3);

        //Validate for duplicate timeslots
        add_filter('acf/validate_value/key=field_5bed4e08b48ec', array($this, 'preventDuplicateTimeSlots'), 15, 4);

        //Validate for overlapping timeslots
        //add_filter('acf/validate_value/key=field_5bed4e08b48ec', array($this, 'preventOverlappingTimeSlots'), 20, 4);

        //Validate for negative timeslots
        add_filter('acf/validate_value/key=field_5bed4e08b48ec', array($this, 'preventNegativeTimeSlots'), 25, 4);
    }

    /**
     * Registers an options page
     *
     * @return void
     */
    public function registerOptionsPage()
    {
        if (function_exists('acf_add_options_page') ) {
            acf_add_options_page(
                array(
                    'icon_url' => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/PjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHg9IjBweCIgeT0iMHB4IiB3aWR0aD0iNTEycHgiIGhlaWdodD0iNTEycHgiIHZpZXdCb3g9IjAgMCA0ODguMTUyIDQ4OC4xNTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDQ4OC4xNTIgNDg4LjE1MjsiIHhtbDpzcGFjZT0icHJlc2VydmUiPjxnPjxnPjxwYXRoIGQ9Ik0xNzcuODU0LDI2OS4zMTFjMC02LjExNS00Ljk2LTExLjA2OS0xMS4wOC0xMS4wNjloLTM4LjY2NWMtNi4xMTMsMC0xMS4wNzQsNC45NTQtMTEuMDc0LDExLjA2OXYzOC42NiAgICBjMCw2LjEyMyw0Ljk2MSwxMS4wNzksMTEuMDc0LDExLjA3OWgzOC42NjVjNi4xMiwwLDExLjA4LTQuOTU2LDExLjA4LTExLjA3OVYyNjkuMzExTDE3Ny44NTQsMjY5LjMxMXoiIGZpbGw9IiNGRkZGRkYiLz48cGF0aCBkPSJNMjc0LjQ4MywyNjkuMzExYzAtNi4xMTUtNC45NjEtMTEuMDY5LTExLjA2OS0xMS4wNjloLTM4LjY3Yy02LjExMywwLTExLjA3NCw0Ljk1NC0xMS4wNzQsMTEuMDY5djM4LjY2ICAgIGMwLDYuMTIzLDQuOTYxLDExLjA3OSwxMS4wNzQsMTEuMDc5aDM4LjY3YzYuMTA4LDAsMTEuMDY5LTQuOTU2LDExLjA2OS0xMS4wNzlWMjY5LjMxMXoiIGZpbGw9IiNGRkZGRkYiLz48cGF0aCBkPSJNMzcxLjExNywyNjkuMzExYzAtNi4xMTUtNC45NjEtMTEuMDY5LTExLjA3NC0xMS4wNjloLTM4LjY2NWMtNi4xMiwwLTExLjA4LDQuOTU0LTExLjA4LDExLjA2OXYzOC42NiAgICBjMCw2LjEyMyw0Ljk2LDExLjA3OSwxMS4wOCwxMS4wNzloMzguNjY1YzYuMTEzLDAsMTEuMDc0LTQuOTU2LDExLjA3NC0xMS4wNzlWMjY5LjMxMXoiIGZpbGw9IiNGRkZGRkYiLz48cGF0aCBkPSJNMTc3Ljg1NCwzNjUuOTVjMC02LjEyNS00Ljk2LTExLjA3NS0xMS4wOC0xMS4wNzVoLTM4LjY2NWMtNi4xMTMsMC0xMS4wNzQsNC45NS0xMS4wNzQsMTEuMDc1djM4LjY1MyAgICBjMCw2LjExOSw0Ljk2MSwxMS4wNzQsMTEuMDc0LDExLjA3NGgzOC42NjVjNi4xMiwwLDExLjA4LTQuOTU2LDExLjA4LTExLjA3NFYzNjUuOTVMMTc3Ljg1NCwzNjUuOTV6IiBmaWxsPSIjRkZGRkZGIi8+PHBhdGggZD0iTTI3NC40ODMsMzY1Ljk1YzAtNi4xMjUtNC45NjEtMTEuMDc1LTExLjA2OS0xMS4wNzVoLTM4LjY3Yy02LjExMywwLTExLjA3NCw0Ljk1LTExLjA3NCwxMS4wNzV2MzguNjUzICAgIGMwLDYuMTE5LDQuOTYxLDExLjA3NCwxMS4wNzQsMTEuMDc0aDM4LjY3YzYuMTA4LDAsMTEuMDY5LTQuOTU2LDExLjA2OS0xMS4wNzRWMzY1Ljk1eiIgZmlsbD0iI0ZGRkZGRiIvPjxwYXRoIGQ9Ik0zNzEuMTE3LDM2NS45NWMwLTYuMTI1LTQuOTYxLTExLjA3NS0xMS4wNjktMTEuMDc1aC0zOC42N2MtNi4xMiwwLTExLjA4LDQuOTUtMTEuMDgsMTEuMDc1djM4LjY1MyAgICBjMCw2LjExOSw0Ljk2LDExLjA3NCwxMS4wOCwxMS4wNzRoMzguNjdjNi4xMDgsMCwxMS4wNjktNC45NTYsMTEuMDY5LTExLjA3NFYzNjUuOTVMMzcxLjExNywzNjUuOTV6IiBmaWxsPSIjRkZGRkZGIi8+PHBhdGggZD0iTTQ0MC4yNTQsNTQuMzU0djU5LjA1YzAsMjYuNjktMjEuNjUyLDQ4LjE5OC00OC4zMzgsNDguMTk4aC0zMC40OTNjLTI2LjY4OCwwLTQ4LjYyNy0yMS41MDgtNDguNjI3LTQ4LjE5OFY1NC4xNDIgICAgaC0xMzcuNDR2NTkuMjYyYzAsMjYuNjktMjEuOTM4LDQ4LjE5OC00OC42MjIsNDguMTk4SDk2LjIzNWMtMjYuNjg1LDAtNDguMzM2LTIxLjUwOC00OC4zMzYtNDguMTk4di01OS4wNSAgICBDMjQuNTc2LDU1LjA1Nyw1LjQxMSw3NC4zNTYsNS40MTEsOTguMDc3djM0Ni4wNjFjMCwyNC4xNjcsMTkuNTg4LDQ0LjAxNSw0My43NTUsNDQuMDE1aDM4OS44MiAgICBjMjQuMTMxLDAsNDMuNzU1LTE5Ljg4OSw0My43NTUtNDQuMDE1Vjk4LjA3N0M0ODIuNzQxLDc0LjM1Niw0NjMuNTc3LDU1LjA1Nyw0NDAuMjU0LDU0LjM1NHogTTQyNi4wOTEsNDIyLjU4OCAgICBjMCwxMC40NDQtOC40NjgsMTguOTE3LTE4LjkxNiwxOC45MTdIODAuMTQ0Yy0xMC40NDgsMC0xOC45MTYtOC40NzMtMTguOTE2LTE4LjkxN1YyNDMuODM1YzAtMTAuNDQ4LDguNDY3LTE4LjkyMSwxOC45MTYtMTguOTIxICAgIGgzMjcuMDNjMTAuNDQ4LDAsMTguOTE2LDguNDczLDE4LjkxNiwxOC45MjFMNDI2LjA5MSw0MjIuNTg4TDQyNi4wOTEsNDIyLjU4OHoiIGZpbGw9IiNGRkZGRkYiLz48cGF0aCBkPSJNOTYuMTI4LDEyOS45NDVoMzAuMTYyYzkuMTU1LDAsMTYuNTc4LTcuNDEyLDE2LjU3OC0xNi41NjdWMTYuNTczQzE0Mi44NjgsNy40MTcsMTM1LjQ0NSwwLDEyNi4yOSwwSDk2LjEyOCAgICBDODYuOTcyLDAsNzkuNTUsNy40MTcsNzkuNTUsMTYuNTczdjk2LjgwNUM3OS41NSwxMjIuNTMzLDg2Ljk3MiwxMjkuOTQ1LDk2LjEyOCwxMjkuOTQ1eiIgZmlsbD0iI0ZGRkZGRiIvPjxwYXRoIGQ9Ik0zNjEuMDM1LDEyOS45NDVoMzAuMTYyYzkuMTQ5LDAsMTYuNTcyLTcuNDEyLDE2LjU3Mi0xNi41NjdWMTYuNTczQzQwNy43Nyw3LjQxNyw0MDAuMzQ3LDAsMzkxLjE5NywwaC0zMC4xNjIgICAgYy05LjE1NCwwLTE2LjU3Nyw3LjQxNy0xNi41NzcsMTYuNTczdjk2LjgwNUMzNDQuNDU4LDEyMi41MzMsMzUxLjg4MSwxMjkuOTQ1LDM2MS4wMzUsMTI5Ljk0NXoiIGZpbGw9IiNGRkZGRkYiLz48L2c+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjwvc3ZnPg==',
                    'page_title' => __('Resource Booking', 'modularity-resource-booking'),
                    'parent_slug' => 'options-general.php',
                    'menu_slug'  => 'resource-booking-options',
                )
            );
        }
    }

    /**
     * Prints the nonce key
     *
     * @return void
     */
    public function nonceKeyMessage()
    {
        if ((isset($_GET['page']) && $_GET['page'] == "resource-booking-options") && (is_super_admin() || current_user_can('administrator'))) {
            printf('<div class="updated notice"><p>%s: %s</p></div>', __('Current nonce key is: ', 'modularity-resource-booking'), wp_create_nonce('wp_rest'));
        }
    }

    /**
     * Remove passed timestamps from render & get
     *
     * @param array  $value  The old value
     * @param string $postId The identification
     * @param array  $field  The field configuration
     *
     * @return array The new santitized value
     */
    public function removeObsoleteTimeSlots($value, $postId, $field)
    {
        if (is_array($value) && !empty($value)) {
            foreach ($value as $rowKey => $row) {
                //Check if start is passed
                if (date("Ymd") >= $row['field_5bed4e13b48ed']) {
                    unset($value[$rowKey]);
                }
            }
        }
        return $value;
    }

    /**
     * Prevent identical slots
     *
     * @param boolean $valid Previous validation
     * @param array   $value The old value
     * @param array   $field The field configuration
     * @param string  $input Dom element
     *
     * @return array The new santitized value
     */
    public function preventDuplicateTimeSlots($valid, $value, $field, $input)
    {

        if (!$valid) {
            return $valid;
        }

        if (is_array($value) && !empty($value)) {
            if ($value != array_unique($value, SORT_REGULAR)) {
                return __('You cannot have indentical time slots configured.', 'modularity-resource-booking');
            }
        }

        return $valid;
    }

    /**
     * Prevent overlapping slots
     *
     * @param boolean $valid Previous validation
     * @param array   $value The old value
     * @param array   $field The field configuration
     * @param string  $input Dom element
     *
     * @return array The new santitized value
     */
    public function preventOverlappingTimeSlots($valid, $value, $field, $input)
    {
        if (!$valid) {
            return $valid;
        }

        if (is_array($value) && !empty($value)) {

            $oldValues = (array) get_field('mod_res_book_time_slots', 'options');

            $newValues = array_udiff(
                $value,
                $oldValues,
                function ($a, $b) {
                    var_dump($a);
                    return serialize($a) == serialize($b) ? false : true;
                }
            );

            return json_encode($newValues);

            if ($value != array_unique($value, SORT_REGULAR)) {
                return __('You cannot have overlapping time slots configured.', 'modularity-resource-booking');
            }
        }

        return $valid;
    }


    /**
     * Prevent overlapping slots
     *
     * @param boolean $valid Previous validation
     * @param array   $value The old value
     * @param array   $field The field configuration
     * @param string  $input Dom element
     *
     * @return array The new santitized value
     */
    public function preventNegativeTimeSlots($valid, $value, $field, $input)
    {
        if (!$valid) {
            return $valid;
        }

        if (is_array($value) && !empty($value)) {
            $filtered = array_filter(
                $value,
                function ($subArray) {
                    if (reset($subArray) <= end($subArray)) {
                        return true;
                    }
                    return false;
                }
            );

            if ($value != $filtered) {
                return __('You cannot have negative time slots configured.', 'modularity-resource-booking');
            }
        }

        return $valid;
    }
}
