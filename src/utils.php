<?php

/**
*   Utility functions used by many other classes and functions.
*
*   @author Albert Nel
*/

/**
*    Check if it's a valid IP address.
*
*   @param string $ip_address The IP address to check.
*   @return bool True if valid, false if invalid.
*/
function checkIpAddress($ip_address)
{
    $valid = false;

    if (!empty($ip_address)) {
        if (filter_var($ip_address, FILTER_VALIDATE_IP) !== false) {
            $valid = true;
        } else {
            $valid = false;
        }
    } else {
        $valid = false;
    }

    return $valid;
}

?>
