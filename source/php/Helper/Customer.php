<?php

namespace ModularityResourceBooking\Helper;

class Customer
{
    /**
     * Get customer data
     *
     * @param int|WP_User The user object or user id
     *
     * @return array with user data
     */
    public static function getCustomerData($user, $headingsOnly = false)
    {
        return array(
            'name' => $headingsOnly ?           __('Name', 'modularity-resource-booking')
            : self::getName($user),

            'company' => $headingsOnly ?        __('Company', 'modularity-resource-booking')
            : self::getCompany($user),

            'companyNumber' => $headingsOnly ?  __('Company number', 'modularity-resource-booking')
            : self::getCompanyNumber($user),

            'contactPerson' => $headingsOnly ?  __('Contact person', 'modularity-resource-booking')
            : self::getContactPerson($user),

            'email' => $headingsOnly ?          __('Email', 'modularity-resource-booking')
            : self::getEmail($user),

            'phone' => $headingsOnly ?          __('Phone', 'modularity-resource-booking')
            : self::getPhone($user),

            'glnr' => $headingsOnly ?           __('GLNR number', 'modularity-resource-booking')
            : self::getGlnr($user),

            'vat' => $headingsOnly ?            __('VAT-number', 'modularity-resource-booking')
            : self::getVat($user),
            
            'billingAddress' => $headingsOnly ? __('Billing address', 'modularity-resource-booking')
            : self::getBillingAddress($user)
        );
    }

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
     * Get a customer glnr
     *
     * @param int|WP_User $user The user object or user id
     *
     * @return string with the user phone or WP_Error if not found.
     */
    public static function getGlnr($user)
    {
        $user = self::_transformToUserId($user);

        if ($glnr = get_user_meta($user, 'billing_glnr_number', true)) {
            return $glnr;
        }

        return new \WP_Error(
            'user_not_found',
            __('The user data you requested was not found.', 'modularity-resource-booking')
        );
    }

    /**
     * Get a customer vat
     *
     * @param int|WP_User $user The user object or user id
     *
     * @return string with the user phone or WP_Error if not found.
     */
    public static function getVat($user)
    {
        $user = self::_transformToUserId($user);

        if ($vat = get_user_meta($user, 'billing_vat_number', true)) {
            return $vat;
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
     * Get a customer's contact person
     *
     * @param int|WP_User $user The user object or user id
     *
     * @return string with the user phone or WP_Error if not found.
     */
    public static function getContactPerson($user)
    {
        $user = self::_transformToUserId($user);
        if ($phone = get_user_meta($user, 'billing_contact_person', true)) {
            return $phone;
        }

        return new \WP_Error(
            'user_not_found',
            __('The user data you requested was not found.', 'modularity-resource-booking')
        );
    }

    /**
     * Get a customer billing address
     *
     * @param int|WP_User $user The user object or user id
     *
     * @return string with the user phone or WP_Error if not found.
     */
    public static function getBillingAddress($user)
    {
        $user = self::_transformToUserId($user);
        if ($phone = get_user_meta($user, 'billing_address', true)) {
            return $phone;
        }

        return new \WP_Error(
            'user_not_found',
            __('The user data you requested was not found.', 'modularity-resource-booking')
        );
    }

    /**
     * Get tax indicator
     *
     * @param int|WP_User $user The user object or user id
     *
     * @return string with excl. tax or incl. tax
     */
    public static function getTaxIndicator($user, $yesNo = false)
    {
        $user = self::_transformToUserId($user);

        if ($userGroup = get_field('customer_group', 'user_' . $user)) {
            if ($taxSetting = get_field('mod_rb_include_tax_in_price', 'customer_group_' . $userGroup)) {
                if ($taxSetting) {
                    if ($yesNo === true) {
                        return __('Yes', 'modularity-resource-booking');
                    }
                    return __('incl. vat', 'modularity-resource-booking');
                } else {
                    if ($yesNo === true) {
                        return __('No', 'modularity-resource-booking');
                    }
                    return __('excl. vat', 'modularity-resource-booking');
                }
            }
        }
        return "";
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
