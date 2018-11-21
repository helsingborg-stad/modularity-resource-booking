<?php

namespace ModularityResourceBooking\Entity;

class Taxonomy
{
    public $namePlural;
    public $nameSingular;
    public $slug;
    public $args;
    public $postTypes;

    /**
     * Registers a taxonomy
     *
     * @param string $namePlural   Plural name of the post type
     * @param string $nameSingular Singular name of the post type
     * @param string $slug         The slug (id) of the post type
     * @param array  $args         Array of argument passed to the posttype
     *
     * @return void Init a new taxonomy
     */
    public function __construct($namePlural, $nameSingular, $slug, $postTypes, $args)
    {
        $this->namePlural = $namePlural;
        $this->nameSingular = $nameSingular;
        $this->slug = $slug;
        $this->args = $args;
        $this->postTypes = $postTypes;

        add_action('init', array($this, 'registerTaxonomy'));
    }
    /**
     * Register the actual taxonomy
     *
     * @return string Registered taxonomy slug
     */
    public function registerTaxonomy() : string
    {
        $labels = array(
            'name'              => $this->namePlural,
            'singular_name'     => $this->nameSingular,
            'search_items'      => sprintf(__('Search %s', 'todo'), $this->namePlural),
            'all_items'         => sprintf(__('All %s', 'todo'), $this->namePlural),
            'parent_item'       => sprintf(__('Parent %s:', 'todo'), $this->nameSingular),
            'parent_item_colon' => sprintf(__('Parent %s:', 'todo'), $this->nameSingular) . ':',
            'edit_item'         => sprintf(__('Edit %s', 'todo'), $this->nameSingular),
            'update_item'       => sprintf(__('Update %s', 'todo'), $this->nameSingular),
            'add_new_item'      => sprintf(__('Add New %s', 'todo'), $this->nameSingular),
            'new_item_name'     => sprintf(__('New %s Name', 'todo'), $this->nameSingular),
            'menu_name'         => $this->namePlural,
        );

        $this->args['labels'] = $labels;

        register_taxonomy($this->slug, $this->postTypes, $this->args);

        return $this->slug;
    }
}
