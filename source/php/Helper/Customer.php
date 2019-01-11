<?php

namespace ModularityResourceBooking\Helper;

class Customer
{
    /**
     * Get a customers first and lastname as string
     *
     * @param int|WP_User The user object or user id
     *
     * @return string with the user name or WP_Error if not found.
     */
    public static function getName($user)
    {
        $user = self::transformUserItem($user);

        if (get_class($user) == "WP_User") {
            return $user->first_name . " " . $user->last_name;
        }

        return new \WP_Error(
            'user_not_found',
            __('The user you requested was not found.', 'modularity-resource-booking')
        );
    }

    /**
     * Get a customer email
     *
     * @param int|WP_User The user object or user id
     *
     * @return string with the user name or WP_Error if not found.
     */
    public static function getEmail($user)
    {
        $user = self::transformUserItem($user);

        if (get_class($user) == "WP_User") {
            return $user->user_email;
        }

        return new \WP_Error(
            'user_not_found',
            __('The user you requested was not found.', 'modularity-resource-booking')
        );
    }

    /**
     * Transform user id's to object
     *
     * @param int|WP_User $user Data to be streamlined
     *
     * @return WP_User object
     */
    private static function transformUserItem($user)
    {
        if (is_numeric($user)) {
            $user = get_userdata($user);
        }
        return $user;
    }
}
