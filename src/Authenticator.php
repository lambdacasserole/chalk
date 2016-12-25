<?php

namespace Chalk;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

/**
 * Represents an authenticator against a Blackboard installation.
 *
 * @package Chalk
 * @author Saul Johnson
 * @since 25/12/2016
 */
class Authenticator {

    private $url;

    /**
     * Initialises a new instance of an authenticator against a Blackboard installation.
     *
     * @param string $url   the URL of the installation
     */
    public function __construct($url) {
        $this->url = trim($url);
        if (!self::endsWith($this->url, '/')) {
            $this->url .= '/';
        }
    }

    /**
     * Checks whether or not a string terminated with the specified character.
     *
     * @param string $haystack  the string to check
     * @param string $needle    the character to check for
     * @return bool             true if the haystack terminates with the needle, otherwise false
     */
    private static function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }
        return substr($haystack, -$length) === $needle;
    }

    /**
     * Pads each of a string's characters with a right-hand null byte and returns the result.
     *
     * @param string $str   the string to pad
     * @return string       the transformed string
     */
    private static function padWithNulls($str) {
        $chars = str_split($str);
        $output = '';
        foreach ($chars as $char) {
            $output .= $char . "\0";
        }
        return $output;
    }

    /**
     * Authenticates against the installation.
     *
     * @param string $username  the username credential
     * @param string $password  the password credential
     * @return bool             true if the credentials are valid, otherwise false
     */
    function authenticate($username, $password) {
        $fields = array('user_id' => $username,
            'password' => '',
            'login' => 'Login',
            'action' => 'login',
            'remote-user' => '',
            'new_loc' => '',
            'auth_type' => '',
            'one_time_token' => '',
            'encoded_pw' => base64_encode($password),
            'encoded_pw_unicode' => base64_encode(self::padWithNulls($password))
        );

        // Store cookies to carry through the session, don't verify SSL.
        $cookies = new CookieJar();
        $client = new Client(['verify' => false, 'cookies' => $cookies]);

        // Make an initial request, this will give us our session ID.
        $client->request('GET', $this->url);

        // This is the actual login request.
        $response = $client->request('POST', $this->url . 'webapps/login/', [
            'form_params' => $fields
        ]);

        // If this text is in the body, we're logged in.
        $body = $response->getBody()->getContents();
        if (strpos($body, 'Modules you are studying:') !== false) {
            return true;
        }

        // Otherwise we're not logged in.
        return false;
    }
}