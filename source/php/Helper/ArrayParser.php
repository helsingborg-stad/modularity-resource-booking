<?php

namespace ModularityResourceBooking\Helper;

/**
 * Class ArrayParser
 *
 * @category Helpers
 * @package  ModularityResourceBooking\Helper
 * @author   Sebastian Thulin <sebastian.thulin@helsingborg.se>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://helsingborg.se
 */
class ArrayParser
{
    /**
     * Extracts values from a multidimensional (2level) array
     *
     * @param array  $array   The array to be parsed
     * @param string $findKey The key to look for
     *
     * @return array
     */
    public static function getSubKey($array, $findKey)
    {
        $result = array();

        if (is_array($array) && !empty($array)) {
            foreach ($array as $item) {
                if (is_array($item) && isset($item[$findKey])) {
                    $result[] = $item[$findKey];
                }
            }
        }

        return array_filter($result);
    }
}
