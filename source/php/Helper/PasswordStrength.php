<?php

namespace ModularityResourceBooking\Helper;

/**
 * Class PasswordStrength
 *
 * @category Helpers
 * @package  ModularityResourceBooking\Helper
 * @author   Sebastian Thulin <sebastian.thulin@helsingborg.se>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://helsingborg.se
 */
class PasswordStrength
{

    /**
     * Returns the revved/cache-busted file name of an asset.
     *
     * @return true if verified, array if not verified (with response message)
     */
    public static function check($password) {

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
}



