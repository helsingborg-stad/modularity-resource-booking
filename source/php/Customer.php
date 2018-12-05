<?php

namespace ModularityResourceBooking;

class Customer
{
    public static $groupTaxonomySlug;

    public function __construct()
    {
        self::$groupTaxonomySlug = $this->taxonomyGroup();

        add_action('admin_menu', array($this, 'registerTaxonomyPage'));
        add_action('parent_file', array($this, 'highlightTaxonomyParentMenu'));
        add_filter('user_contactmethods', array($this, 'addUserContactFields'));
    }

    /**
     * Customize the contact information fields available to WordPress user accounts
     * @param array $userContactFields an associative array keyed by form field ids with human-readable text as values.
     * @return array
     */
    public function addUserContactFields($userContactFields)
    {
        $userContactFields['phone'] = __( 'Phone number', 'modularity-resource-booking' );
        return $userContactFields;
    }

    /**
     * Add customer groups page
     * @return void
     */
    public function registerTaxonomyPage()
    {
        $taxonomy = get_taxonomy(self::$groupTaxonomySlug);
        add_users_page(
            esc_attr($taxonomy->labels->menu_name),
            esc_attr($taxonomy->labels->menu_name),
            'add_users',
            'edit-tags.php?taxonomy=' . $taxonomy->name
        );
    }

    /**
     * Highlight the taxonomy page
     * @param $parentFile
     * @return string
     */
    public function highlightTaxonomyParentMenu($parentFile)
    {
        if (get_current_screen()->taxonomy == self::$groupTaxonomySlug) {
            $parentFile = 'users.php';
        }

        return $parentFile;
    }

    /**
     * Create custom user roles
     * @return void
     */
    public static function createUserRoles()
    {
        add_role('customer', __('Customer', 'modularity-resource-booking'), array(
            'read' => true,
            'level_0' => true,
            'upload_files' => true
        ));
    }

    /**
     * Remove custom user roles
     * @return void
     */
    public static function removeUserRoles()
    {
        if (get_role('customer')) {
            remove_role('customer');
        }
    }

    /**
     * Create customer group taxonomy
     * @return string
     */
    public function taxonomyGroup(): string
    {
        $groups = new Entity\Taxonomy(
            __('Customer groups', 'modularity-resource-booking'),
            __('Customer group', 'modularity-resource-booking'),
            'customer_group',
            'user',
            array(
                'hierarchical' => false,
            )
        );

        //Add filter
        new Entity\Filter(
            'customer_group',
            'users'
        );

        return $groups->slug;
    }
}
