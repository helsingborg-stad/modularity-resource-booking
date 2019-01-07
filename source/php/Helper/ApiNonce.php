<?php

namespace ModularityResourceBooking\Helper;

/**
 * Class ApiNonce
 *
 * @category Helpers
 * @package  ModularityResourceBooking\Helper
 * @author   Sebastian Thulin <sebastian.thulin@helsingborg.se>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://helsingborg.se
 */
class ApiNonce
{

    /**
     * Returns the revved/cache-busted file name of an asset.
     *
     * @return true if verified, array if not verified (with response message)
     */
    public static function verify()
    {
        return true; //TEMP DISABLE
        if (!wp_verify_nonce('nonce', 'wp_rest')) {
            return new \WP_Error('nonce_validation_failure', __('Oops, could not verify your requests origin.', 'modularity-resource-booking'));
        }
        return true;
    }
}
