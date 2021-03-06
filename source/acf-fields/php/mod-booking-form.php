<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5c35f66a679df',
    'title' => __('Modularity Resource Booking - Booking Form', 'modularity-resource-booking'),
    'fields' => array(
        0 => array(
            'key' => 'field_5c3731f33f75d',
            'label' => __('Select article type', 'modularity-resource-booking'),
            'name' => 'article_type',
            'type' => 'select',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
                'package' => __('Package', 'modularity-resource-booking'),
            ),
            'default_value' => array(
                0 => __('package', 'modularity-resource-booking'),
            ),
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 0,
            'return_format' => 'value',
            'ajax' => 0,
            'placeholder' => '',
        ),
        1 => array(
            'key' => 'field_5c35f67549fa5',
            'label' => __('Select package', 'modularity-resource-booking'),
            'name' => 'package_id',
            'type' => 'taxonomy',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => array(
                0 => array(
                    0 => array(
                        'field' => 'field_5c3731f33f75d',
                        'operator' => '==',
                        'value' => 'package',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'taxonomy' => 'product-package',
            'field_type' => 'select',
            'allow_null' => 0,
            'add_term' => 0,
            'save_terms' => 0,
            'load_terms' => 0,
            'return_format' => 'id',
            'multiple' => 0,
        ),
        2 => array(
            'key' => 'field_5c53f823fc41a',
            'label' => __('Campaign Name Heading', 'modularity-resource-booking'),
            'name' => 'campaign_heading',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'maxlength' => '',
        ),
        3 => array(
            'key' => 'field_5c53f858fc41b',
            'label' => __('Calendar Heading', 'modularity-resource-booking'),
            'name' => 'calendar_heading',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'maxlength' => '',
        ),
        4 => array(
            'key' => 'field_5c50181473158',
            'label' => __('Files Heading', 'modularity-resource-booking'),
            'name' => 'files_heading',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'maxlength' => '',
        ),
        5 => array(
            'key' => 'field_5c53f88ffc41c',
            'label' => __('Summary Heading', 'modularity-resource-booking'),
            'name' => 'summary_heading',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'maxlength' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'mod-rb-booking-form',
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