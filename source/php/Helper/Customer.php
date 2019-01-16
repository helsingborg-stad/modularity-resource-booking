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
        $user = self::_transformToUserItem($user);

        if (is_a($user, "WP_User")) {
            return $user->first_name . " " . $user->last_name;
        }

        return new \WP_Error(
            'user_not_found',
            __('The user data you requested was not found.', 'modularity-resource-booking')
        );
    }

    /**
     * Get a customer email
     *
     * @param int|WP_User $user The user object or user id
     *
     * @return string with the user name or WP_Error if not found.
     */
    public static function getEmail($user)
    {
        $user = self::_transformToUserItem($user);

        if (is_a($user, "WP_User")) {
            return $user->user_email;
        }

        return new \WP_Error(
            'user_not_found',
            __('The user data you requested was not found.', 'modularity-resource-booking')
        );
    }

    /**
     * Get a customer phone
     *
     * @param int|WP_User $user The user object or user id
     *
     * @return string with the user phone or WP_Error if not found.
     */
    public static function getPhone($user)
    {
        $user = self::_transformToUserId($user);

        if ($phone = get_user_meta($user, 'phone', true)) {
            return $phone;
        }

        return new \WP_Error(
            'user_not_found',
            __('The user data you requested was not found.', 'modularity-resource-booking')
        );
    }

    /**
     * Get a customer company
     *
     * @param int|WP_User $user The user object or user id
     *
     * @return string with the user company or WP_Error if not found.
     */
    public static function getCompany($user)
    {
        $user = self::_transformToUserId($user);

        if ($company = get_user_meta($user, 'billing_company', true)) {
            return $company;
        }

        return new \WP_Error(
            'user_not_found',
            __('The user data you requested was not found.', 'modularity-resource-booking')
        );
    }

    /**
     * Get a customer company number
     *
     * @param int|WP_User $user The user object or user id
     *
     * @return string with the user company or WP_Error if not found.
     */
    public static function getCompanyNumber($user)
    {
        $user = self::_transformToUserId($user);

        if ($companyNumber = get_user_meta($user, 'billing_company_number', true)) {
            return $companyNumber;
        }

        return new \WP_Error(
            'user_not_found',
            __('The user data you requested was not found.', 'modularity-resource-booking')
        );
    }

    /**
     * Transform user id's to object
     *
     * @param int|WP_User $user Data to be streamlined
     *
     * @return WP_User object
     */
    private static function _transformToUserItem($user)
    {
        if (is_numeric($user)) {
            $user = get_userdata($user);
        }
        return $user;
    }

    /**
     * Transform user id's to object
     *
     * @param int|WP_User $user Data to be streamlined
     *
     * @return WP_User object
     */
    private static function _transformToUserId($user)
    {
        if (is_a($user, "WP_User")) {
            $user->ID;
        }

        if (is_numeric($user)) {
            return $user;
        }

        return new \WP_Error(
            'invalid_user_input',
            __('The user data you requested was not found.', 'modularity-resource-booking')
        );
    }
}
