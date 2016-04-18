<?php

/**
 * Validates outgoing requests
 *
 * @license         Berkeley Software Distribution License (BSD-License 2) http://www.opensource.org/licenses/bsd-license.php
 * @author          Roemer Bakker
 * @copyright       Complexity Software
 *
 */
class Spryng_Api_Utilities_Validator
{

    /**
     * @var array
     */
    private static $routes = ['BUSINESS', 'ECONOMY'];

    /**
     * @param $recipient string
     * @param $body string
     * @param $options array
     * @return bool
     * @throws Spryng_Api_Exception_InvalidRequestException
     */
    static public function validateSendRequest($recipient, $body, $options)
    {
        // Validate recipient
        preg_match('/^[1-9]{1}[0-9]{3,14}$/', $recipient, $match);
        if ( count($match) === 0 )
        {
            throw new Spryng_Api_Exception_InvalidRequestException(
                "Destination is invalid.",
                304
            );
        }

        // Validate sender
        if ( intval($options['sender']) > 0 && strlen($options['sender']) > 14 )
        {
            throw new Spryng_Api_Exception_InvalidRequestException(
                "Numeric senders can not be longer than 14 characters long.",
                306
            );
        }
        else if ( intval($options['sender']) === 0 && strlen($options['sender']) > 11 )
        {
            throw new Spryng_Api_Exception_InvalidRequestException(
                "Alphanumeric senders can not be longer than 11 characters long.",
                305
            );
        }

        if ( isset($options['route']) )
        {
            if ( !in_array(strtoupper($options['route']), self::$routes) )
            {
                throw new Spryng_Api_Exception_InvalidRequestException(
                    "The client you're trying to use does not exist.",
                    301
                );
            }
        }

        // Validate reference
        if ( isset($options['reference']) )
        {
            $ref = $options['reference'];
            if ( strlen($ref) < 1 || strlen($ref) > 256)
            {
                throw new Spryng_Api_Exception_InvalidRequestException(
                    "Reference must be between 1 and 256 characters long.",
                    307
                );
            }
        }

        // Validate length
        if ( isset($options['allowlong']) )
        {
            if ($options['allowlong'])
            {
                // Check for length requirement
                if (strlen($body) > 612)
                {
                    throw new Spryng_Api_Exception_InvalidRequestException(
                        "Maximum length for body is 612 characters.",
                        303
                    );
                }
            }
            else {
                // Check for length requirement if allowlong is dissabled
                if (strlen($body) > 160 && !$options['allowlong']) {
                    throw new Spryng_Api_Exception_InvalidRequestException(
                        "Body can't be longer than 160 characters without 'allowlong' enabled.",
                        302
                    );
                }
            }
        }

        return true;
    }
}