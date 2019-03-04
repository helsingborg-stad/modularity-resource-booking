<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5c7931e1d5479',
    'title' => __('Order notice', 'modularity-resource-booking'),
    'fields' => array(
        0 => array(
            'key' => 'field_5c7931f7f634d',
            'label' => __('Display notice', 'modularity-resource-booking'),
            'name' => 'display_notice',
            'type' => 'true_false',
            'instructions' => __('Enable to display a message to the customer on the order page', 'modularity-resource-booking'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => '',
            'default_value' => 0,
            'ui' => 1,
            'ui_on_text' => __('Yes', 'modularity-resource-booking'),
            'ui_off_text' => __('No', 'modularity-resource-booking'),
        ),
        1 => array(
            'key' => 'field_5c7932e6ec77d',
            'label' => __('Hide notice on fileupload', 'modularity-resource-booking'),
            'name' => 'hide_notice_on_fileupload',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_5c7931f7f634d',
                        'operator' => '==',
                        'value' => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => '',
            'default_value' => 1,
            'ui' => 1,
            'ui_on_text' => '',
            'ui_off_text' => '',
        ),
        2 => array(
            'key' => 'field_5c7932363e75e',
            'label' => __('Notice', 'modularity-resource-booking'),
            'name' => 'notice',
            'type' => 'textarea',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_5c7931f7f634d',
                        'operator' => '==',
                        'value' => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'maxlength' => '',
            'rows' => 4,
            'new_lines' => 'br',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'purchase',
            ),
        ),
    ),
    'menu_order' => 99,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => 1,
    'description' => '',
));
}