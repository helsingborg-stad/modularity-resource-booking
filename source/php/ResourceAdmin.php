<?php

namespace ModularityResourceBooking;

/**
* Add user roles and capabilities
*/
class ResourceAdmin
{
    public function __construct()
    {
        add_filter('map_meta_cap', array($this, 'addAdminUserCap'), 10, 4);
        add_filter('enable_edit_any_user_configuration', '__return_true');
    }

    /**
     * Gives users with capability 'edit_users' to edit other users on multisite
     * @param array     $caps       Returns the user's actual capabilities
     * @param string    $cap        Capability name
     * @param int       $user_id    The user ID
     * @param array     $args       Adds the context to the cap. Typically the object ID
     */
    public function addAdminUserCap($caps, $cap, $user_id, $args)
    {
        foreach ($caps as $key => $capability) {
            if ($capability != 'do_not_allow') {
                continue;
            }

            switch ($cap) {
                case 'edit_user':
                case 'edit_users':
                    $caps[$key] = 'edit_users';
                    break;
                case 'delete_user':
                case 'delete_users':
                    $caps[$key] = 'delete_users';
                    break;
                case 'create_users':
                    $caps[$key] = $cap;
                    break;
            }
        }

        return $caps;
    }

    /**
     * Create custom user roles
     * @return void
     */
    public static function createUserRoles()
    {
        $adminCapabilities = get_role('administrator')->capabilities;
        $customCap = array(
            'edit_users' => true,
            'list_users' => true,
            'promote_users' => true,
            'create_users' => true,
            'add_users' => true,
            'delete_users' => true,
            'level_8' => true,
        );

        add_role('resource_admin', __("Resource Administrator", 'modularity-resource-booking'), array_merge($adminCapabilities, $customCap));
    }

    /**
     * Remove custom user roles
     * @return void
     */
    public static function removeCustomUserRoles()
    {
        $roles = array('resource_admin');
        foreach ($roles as $role) {
            if (get_role($role)) {
                remove_role($role);
            }
        }
    }
}