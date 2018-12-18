<?php

namespace ModularityResourceBooking\Helper;

/**
 * Class Nonce
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
     * @return true if verified, array if not verofyed (with response message)
     */
    public static function verify()
    {
        //Verify nonce
        if (!wp_verify_nonce('nonce', 'wp_rest')) {
            return array(
                'message' => __('Oops, could not verify your requests origin.', 'modularity-resource-booking'),
                'state' => 'error'
            );
        }

        return true;
    }
}
