<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5bed4d621923e',
    'title' => __('Time Slots Management', 'modularity-resource-booking'),
    'fields' => array(
        0 => array(
            'key' => 'field_5bed94b619d84',
            'label' => __('Schedule setup', 'modularity-resource-booking'),
            'name' => 'mod_res_book_automatic_or_manual',
            'type' => 'radio',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'weekly' => __('Weekly', 'modularity-resource-booking'),
                'manual' => __('Manual', 'modularity-resource-booking'),
            ),
            'allow_null' => 0,
            'other_choice' => 0,
            'default_value' => 'automatic',
            'layout' => 'horizontal',
            'return_format' => 'value',
            'save_other_choice' => 0,
        ),
        1 => array(
            'key' => 'field_5bed4e08b48ec',
            'label' => __('Time slots', 'modularity-resource-booking'),
            'name' => 'mod_res_book_time_slots',
            'type' => 'repeater',
            'instructions' => __('This list will automatically be filtered for invalid slots, the following rules will apply: <br/>
<br/>
<span style="background-color: #ff0000; color: #fff;">- Negative timeslots cannot be stored.</span> <br/>
<span style="background-color: #ff0000; color: #fff;">- Passed time slots will be removed (taking account the offset time).</span> <br/>
<span style="background-color: #ff0000; color: #fff;">- Duplicate timeslots will be removed.</span><br/>
<br/>
If your value won\'t save, it\'s possible it dosen\'t meet the requirements.', 'modularity-resource-booking'),
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_5bed94b619d84',
                        'operator' => '==',
                        'value' => 'manual',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'collapsed' => '',
            'min' => 0,
            'max' => 0,
            'layout' => 'table',
            'button_label' => '',
            'sub_fields' => array(
                0 => array(
                    'key' => 'field_5bed4e13b48ed',
                    'label' => __('Start date', 'modularity-resource-booking'),
                    'name' => 'start_date',
                    'type' => 'date_picker',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'display_format' => 'Y-m-d',
                    'return_format' => 'Y-m-d',
                    'first_day' => 1,
                ),
                1 => array(
                    'key' => 'field_5bed4e38b48ee',
                    'label' => __('End date', 'modularity-resource-booking'),
                    'name' => 'end_date',
                    'type' => 'date_picker',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'display_format' => 'Y-m-d',
                    'return_format' => 'Y-m-d',
                    'first_day' => 1,
                ),
            ),
        ),
        2 => array(
            'key' => 'field_5bf2ad1d879c1',
            'label' => __('Pattern mode', 'modularity-resource-booking'),
            'name' => '',
            'type' => 'message',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_5bed94b619d84',
                        'operator' => '==',
                        'value' => 'weekly',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => __('You have selected a weekly pattern, the system will automatically create slots a year ahead.', 'modularity-resource-booking'),
            'new_lines' => 'wpautop',
            'esc_html' => 0,
        ),
        3 => array(
            'key' => 'field_5c4ece8324e31',
            'label' => __('Time slots offset', 'modularity-resource-booking'),
            'name' => 'mod_res_offset_bookable_weeks_by',
            'type' => 'number',
            'instructions' => __('You can offset the bookable time slots by \'n\' number of weeks. This will prevent users from booking the upcoming weeks in set period of time.', 'modularity-resource-booking'),
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_5bed94b619d84',
                        'operator' => '==',
                        'value' => 'weekly',
                    ),
                ),
                1 => array(
                    0 => array(
                        'field' => 'field_5bed94b619d84',
                        'operator' => '==',
                        'value' => 'manual',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => 0,
            'placeholder' => '',
            'prepend' => __('Offset bookable weeks by', 'modularity-resource-booking'),
            'append' => __('weeks', 'modularity-resource-booking'),
            'min' => 0,
            'max' => 52,
            'step' => 1,
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'resource-booking-options',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => 1,
    'description' => '',
));
}