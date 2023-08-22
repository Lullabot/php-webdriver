<?php

/**
 * @copyright 2004 Meta Platforms, Inc.
 * @license Apache-2.0
 *
 * @package WebDriver
 *
 * @author Justin Bishop <jubishop@gmail.com>
 */

namespace WebDriver;

/**
 * WebDriver class
 *
 * @package WebDriver
 *
 * @method array status() Returns information about whether a remote end is in a state in which it can create new sessions.
 */
class WebDriver extends AbstractWebDriver implements WebDriverInterface
{
    /**
     * @var array
     */
    private $capabilities;

    /**
     * {@inheritdoc}
     */
    protected function methods()
    {
        return array(
            'status' => 'GET',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function session($browserName = Browser::FIREFOX, $desiredCapabilities = null, $requiredCapabilities = null)
    {
        // default to W3C WebDriver API
        $firstMatch = $desiredCapabilities ?: array();
        $firstMatch[] = array('browserName' => Browser::CHROME);

        if ($browserName !== Browser::CHROME) {
            $firstMatch[] = array('browserName' => $browserName);
        }

        $parameters = array('capabilities' => array('firstMatch' => $firstMatch));

        if (is_array($requiredCapabilities) && count($requiredCapabilities)) {
            $parameters['capabilities']['alwaysMatch'] = $requiredCapabilities;
        }

        $result = $this->curl(
            'POST',
            '/session',
            $parameters,
            array(CURLOPT_FOLLOWLOCATION => true)
        );

        $this->capabilities = isset($result['value']['capabilities']) ? $result['value']['capabilities'] : null;

        $session = new Session($result['sessionUrl'], $this->capabilities);

        return $session;
    }
}
