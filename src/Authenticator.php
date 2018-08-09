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

    /**
     * The default string to search for to determine whether or not the user has logged in successfully.
     */
    const DEFAULT_FLAG = 'Modules you are studying:';

    /**
     * The URL of the installation.
     *
     * @var string
     */
    private $url;

    /**
     * Whether or not to verify SSL certificates.
     *
     * @var bool
     */
    private $verify;

    /**
     * The string to search for to determine whether or not the user has logged in successfully.
     *
     * @var string
     */
    private $flag;

    /**
     * Initialises a new instance of an authenticator against a Blackboard installation.
     *
     * @param string $url   the URL of the installation
     * @param bool $verify  whether or not to verify SSL certificates
     * @param string $flag  the string to search for to determine whether or not the user has logged in successfully
     */
    public function __construct($url, $verify = true, $flag = self::DEFAULT_FLAG) {
        $this->url = trim($url);
        if (!self::endsWith($this->url, '/')) {
            $this->url .= '/';
        }
        $this->verify = $verify;
        $this->flag = $flag;
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
     * Authenticates against the installation.
     *
     * @param string $username  the username credential
     * @param string $password  the password credential
     * @return bool             true if the credentials are valid, otherwise false
     */
    function authenticate($username, $password) {
        $fields = array('user_id' => $username,
            'password' => $password,
            'login' => 'Login',
            'action' => 'login',
            'new_loc' => ''
        );

        // Store cookies to carry through the session.
        $cookies = new CookieJar();
        $client = new Client(['verify' => $this->verify, 'cookies' => $cookies]);

        // Make an initial request, this will give us our session ID.
        $client->request('GET', $this->url);

        // This is the actual login request.
        $response = $client->request('POST', $this->url . 'webapps/login/', [
            'form_params' => $fields
        ]);

        // If this text is in the body, we're logged in.
        $body = $response->getBody()->getContents();
        if (strpos($body, self::DEFAULT_FLAG) !== false) {
            return true;
        }

        // Otherwise we're not logged in.
        return false;
    }
}