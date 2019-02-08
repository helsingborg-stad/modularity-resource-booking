<?php

namespace ModularityResourceBooking\Helper;

/**
 * Class Validation
 *
 * @category Helpers
 * @package  ModularityResourceBooking\Helper
 * @author   Sebastian Thulin <sebastian.thulin@helsingborg.se>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://helsingborg.se
 */
class Validation
{

    /**
     * Validates password strength
     *
     * @return true if verified, array if not verified (with response message)
     */
    public static function passwordStrenght($password)
    {

        //Length
        if (strlen($password) < 6) {
            return new \WP_Error('password_short', __('Your password should be at least 6 characters long, containing both letters and numbers.', 'modularity-resource-booking'));
        }

        //Number
        if (!preg_match("#[0-9]+#", $password)) {
            return new \WP_Error('password_short', __('Your password should be at least 6 characters long, containing both letters and numbers.', 'modularity-resource-booking'));
        }

        //Letter
        if (!preg_match("#[a-zA-Z]+#", $password)) {
            return new \WP_Error('password_short', __('Your password should be at least 6 characters long, containing both letters and numbers.', 'modularity-resource-booking'));
        }

        return true;
    }

    /**
     * Vaidates company number
     *
     * @return true if verified, array if not verified (with response message)
     */
    public static function companyNumber($input)
    {
        if (!\Olssonm\IdentityNumber\Pin::isValid($input, 'identity')) {
            return new \WP_Error('invalid_pattern_company', __('Invalid format on provided organization number.', 'modularity-resource-booking'));
        }
        return true;
    }

    /**
     * Validates personal number
     *
     * @return true if verified, array if not verified (with response message)
     */
    public static function personalNumber($input)
    {
        if (!\Olssonm\IdentityNumber\Pin::isValid($input, 'organization')) {
            return new \WP_Error('invalid_pattern_person', __('Invalid format on provided personal number.', 'modularity-resource-booking'));
        }
        return true;
    }

    /**
     * Validates personal number & company number
     *
     * @return true if verified, array if not verified (with response message)
     */
    public static function coordinationNumber($input)
    {
        if (!\Olssonm\IdentityNumber\Pin::isValid($input, 'coordination')) {
            return new \WP_Error('invalid_pattern_coordination', __('Invalid format on provided personal or organization number.', 'modularity-resource-booking'));
        }
        return true;
    }
}
