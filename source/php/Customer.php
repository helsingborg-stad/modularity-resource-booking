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
        add_action('profile_update', array($this, 'sendActivationEmail'), 5, 2);
        add_filter('user_contactmethods', array($this, 'addUserContactFields'));
        add_filter('authenticate', array($this, 'checkCustomerGroup'), 99, 3);
        add_action('after_setup_theme', array($this, 'hideAdminBar'));
        add_action('init', array($this, 'restrictAdminPanel'));
        add_filter('modularityLoginForm/AbortLogin', array($this, 'prohibitGrouplessLogins'), 10, 2);
    }

    /**
     * Prohibit logins when there's no user group.
     *
     * @param bool|string $message  A string witrh a previous message or false if not set
     * @param WP_User     $user     The user object
     *
     * @return false|string
     */
    public function prohibitGrouplessLogins($message, $user)
    {

        //Not a valid user
        if (is_a($user, "WP_User")) {
            return $message;
        }

        if (isset($user->errors) && isset($user->errors['invalid_username'])) {
            return $user->errors['invalid_username'][0];
        }

        if (isset($user->errors) && isset($user->errors['incorrect_password'])) {
            return $user->errors['incorrect_password'][0];
        }

        if ($message === false) {
            if (!is_numeric(get_user_meta($user->ID, 'customer_group', true)) || !get_field('customer_account_active', 'user_' . $user->ID)) {
                $message = __("Your account has not been enabled yet, please wait for a activation notice in your email inbox.", 'modularity-resource-booking');
            }
        }
        return $message;
    }
    
    /**
     * Send a email when the user enteres a new user group
     *
     * @param int    $userId      The user id
     * @param object $oldUserData The old user data
     *
     * @return void
     */
    public function sendActivationEmail($userId, $oldUserData)
    {
        if (in_array('customer', get_userdata($userId)->roles)
            && !get_field('customer_account_active', 'user_' . $userId)
            && $_POST['acf']['field_5c73361b0f27d'] === '1') {
            // Get Actions
            if (!empty(get_field('actions_customer_account_approved', 'options')) && is_array(get_field('actions_customer_account_approved', 'options'))) {
                foreach (get_field('actions_customer_account_approved', 'options') as $mailTemplate) {
                    $mailService = new \ModularityResourceBooking\Mail\Service($mailTemplate);
                    $mailService->setUser($userId);
                    $mailService->composeMail();
                    $mailService->sendMail();

                    $errors = $mailService->getErrors();
                    if (!empty($errors->get_error_messages())) {
                        error_log(print_r($errors, true));
                    }
                }
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
        $role = get_role('customer');
        $role->add_cap('order');
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

    /**
     * Check if customer have a group
     * @param object $user User object or error
     * @param string $username
     * @param string $password
     * @return object|null
     */
    public function checkCustomerGroup($user, $username, $password)
    {
        if (isset($user->roles) && in_array('customer', (array)$user->roles)) {
            if (empty(get_field('customer_group', 'user_' . $user->ID)) || !get_field('customer_account_active', 'user_' . $user->ID)) {
                return new \WP_Error(
                    'not-verified',
                    __('Your account is not verified yet.', 'modularity-resource-booking')
                );
            }
        }

        return $user;
    }

    /**
     * Hide admin bar for customers
     */
    public function hideAdminBar()
    {
        //Prevent unintentional lockout
        if (is_super_admin()||current_user_can('administrator')) {
            return;
        }

        if (current_user_can('customer')) {
            show_admin_bar(false);
        }
    }

    /**
     * Redirects customers from admin panel
     */
    public function restrictAdminPanel()
    {

        //Prevent unintentional lockout
        if (is_super_admin()||current_user_can('administrator')) {
            return;
        }

        //Lockout customers
        if (is_admin() && current_user_can('customer') &&
            !(defined('DOING_AJAX') && DOING_AJAX)) {
            wp_safe_redirect(home_url());
            exit;
        }
    }
}
