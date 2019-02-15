<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5c5dc351c1bfc',
    'title' => __('Order - Resend mail', 'modularity-resource-booking'),
    'fields' => array(
        0 => array(
            'key' => 'field_5c5dc36510a8e',
            'label' => __('Resend Mail', 'modularity-resource-booking'),
            'name' => 'resend_email',
            'type' => 'true_false',
            'instructions' => '',
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
            'ui_on_text' => __('On', 'modularity-resource-booking'),
            'ui_off_text' => __('Off', 'modularity-resource-booking'),
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
    'menu_order' => -99,
    'position' => 'side',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => 1,
    'description' => '',
));
}