<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5c360bf77a6cf',
    'title' => __('Status Change Actions', 'modularity-resource-booking'),
    'fields' => array(
        0 => array(
            'key' => 'field_5c360bffe82b6',
            'label' => __('Do these actions when order aquire status', 'modularity-resource-booking'),
            'name' => 'do_action_on_aqusition',
            'type' => 'select',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'economy_mail' => __('Send a invoice request to economy', 'modularity-resource-booking'),
            ),
            'default_value' => array(
            ),
            'allow_null' => 1,
            'multiple' => 1,
            'ui' => 1,
            'ajax' => 0,
            'return_format' => 'value',
            'placeholder' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'taxonomy',
                'operator' => '==',
                'value' => 'order-status',
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