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
        add_action('profile_update', array($this, 'sendActivationEmail'),5, 2); 
        add_filter('user_contactmethods', array($this, 'addUserContactFields'));
        
    }
    
    /**
     * Send a email when the user enteres a new user group
     *
     * @param int    $userId      The user id
     * @param object $oldUserData The old user data
     * 
     * @return void
     */
    public function sendActivationEmail($userId, $oldUserData) {
        if (empty(get_field('customer_group', 'user_' . $userId))) {
            if (isset($_POST['acf']['field_5bfe8eb5174c1']) && is_numeric($_POST['acf']['field_5bfe8eb5174c1'])) {
                new Helper\CustomerMail(
                    $userId,
                    __('Activated account', 'modularity-resource-booking'),
                    __('Your account has now been verified by one of our managers. You can now proceed to make a new reservation for a spot.', 'modularity-resource-booking'),
                    array(
                        array(
                            'heading' => __('Account owner:', 'modularity-resource-booking'),
                            'content' => Helper\Customer::getName($userId)
                        ),
                        array(
                            'heading' => __('Registered email:', 'modularity-resource-booking'),
                            'content' => Helper\Customer::getEmail($userId)
                        ),
                        array(
                            'heading' => __('Registered phone:', 'modularity-resource-booking'),
                            'content' => Helper\Customer::getPhone($userId)
                        ),
                        array(
                            'heading' => __('Registered company:', 'modularity-resource-booking'),
                            'content' => Helper\Customer::getCompany($userId)
                        ),
                        array(
                            'heading' => __('Registered company number:', 'modularity-resource-booking'),
                            'content' => Helper\Customer::getCompanyNumber($userId)
                        )
                    )
                );
            }
        }
    }

    /**
     * Customize the contact information fields available to WordPress user accounts
     * @param array $userContactFields an associative array keyed by form field ids with human-readable text as values.
     * @return array
     */
    public function addUserContactFields($userContactFields)
    {
        $userContactFields['phone'] = __('Phone number', 'modularity-resource-booking');
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
