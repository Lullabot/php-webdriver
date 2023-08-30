<?php

namespace Test\WebDriver;

use WebDriver\Exception\CurlExec;
use WebDriver\Exception\NoSuchElement;
use WebDriver\Service\CurlService;
use WebDriver\ServiceFactory;
use WebDriver\Exception\UnknownCommand;
use WebDriver\WebDriver;
use WebDriver\Session;

/**
 * Selenium WebDriver
 *
 * @package WebDriver
 *
 * @group Functional
 */
class SeleniumWebDriverTest extends WebDriverTestBase
{
    protected $testWebDriverRootUrl = 'http://firefox:4444';
    protected $testWebDriverName = 'selenium';
    protected $status = null;

    /**
     * Run before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        try {
            $this->status = $this->driver->status();
            $this->session = $this->driver->session();
        }
        catch (\Exception $e) {
            if ($this->isWebDriverDown($e)) {
                $this->fail("{$this->testWebDriverName} server not running: {$e->getMessage()}");
            }
            throw $e;
        }
    }

   /**
    * Test driver status
    */
    public function testStatus()
    {
        $this->assertEquals(1, $this->status['ready'], 'Selenium is not ready');
        $this->assertEquals('Selenium Grid ready.', $this->status['message'], 'Selenium is not ready');
        $this->assertNotEmpty($this->status['nodes'][0]['osInfo'], 'OS info not detected');
    }
}
