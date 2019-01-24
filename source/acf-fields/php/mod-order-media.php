<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5bffbfe266f20',
    'title' => 'Attached order media',
    'fields' => array(
        0 => array(
            'key' => 'field_5bffbfed18455',
            'label' => __('Media items', 'modularity-resource-booking'),
            'name' => 'media_items',
            'type' => 'repeater',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'collapsed' => 'field_5bffc9b93070a',
            'min' => 0,
            'max' => 0,
            'layout' => 'row',
            'button_label' => __('Add media item', 'modularity-resource-booking'),
            'sub_fields' => array(
                0 => array(
                    'key' => 'field_5bffc9b93070a',
                    'label' => __('File', 'modularity-resource-booking'),
                    'name' => 'file',
                    'type' => 'file',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'return_format' => 'array',
                    'library' => 'all',
                    'min_size' => '',
                    'max_size' => '',
                    'mime_types' => '',
                ),
            ),
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