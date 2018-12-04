<?php

namespace ModularityResourceBooking\Helper;

class Hash
{

    /**
     * Decode assets json to array
     *
     * @return array containg assets filenames
     */
    public static function createHash()
    {
        if (is_array($var) || is_object($var)) {
            return md5(serialize($var));
        }
        return md5($var);
    }
}
