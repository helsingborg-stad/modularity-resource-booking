<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5bffbfe266f20',
    'title' => __('Attached order media', 'modularity-resource-booking'),
    'fields' => array(
        0 => array(
            'key' => 'field_5bffbfed18455',
            'label' => __('Media items', 'modularity-resource-booking'),
            'name' => 'media_items',
            'type' => 'dynamic_table',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'tableclass' => '',
            'maxrows' => '',
            'disable_sort' => 0,
            'fixed_columns' => 0,
            'default_headers' => 'Name
Download',
            'default_header' => '',
            'readonly' => 0,
            'disabled' => 0,
            'sub_fields' => false,
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
    'menu_order' => 10,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => 1,
    'description' => '',
));
}