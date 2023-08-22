<?php

/**
 * Copyright 2021-2022 Anthon Pang. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package WebDriver
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */

namespace Test\WebDriver;

use WebDriver\Exception\CurlExec;
use WebDriver\Exception\NoSuchElement;
use WebDriver\Service\CurlService;
use WebDriver\ServiceFactory;
use WebDriver\Exception\UnknownCommand;
use WebDriver\WebDriver;

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
        } catch (\Exception $e) {
            if ($this->isWebDriverDown($e)) {
                $this->fail("{$this->testWebDriverName} server not running: {$e->getMessage()}");
            }
            throw $e;
        }
    }

    /**
     * Test driver sessions
     */
    public function testSessions()
    {
        $this->assertEquals($this->getTestWebDriverRootUrl(), $this->driver->getUrl());
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

   ///**
   // * Checks that an error connecting to WebDriver gives back the expected exception
   // */
   //public function testWebDriverError()
   //{
   //    $this->session->close();
   //    $this->session = null;
   //    try {
   //        $this->driver = new WebDriver($this->getTestWebDriverRootUrl() . '/../invalidurl');
   //        $this->fail('Exception not thrown while connecting to invalid WebDriver url');
   //    } catch (\Exception $e) {
   //        $this->assertEquals(UnknownCommand::class, get_class($e), $e->getMessage());
   //    }
   //}

    /**
     * Checks that a successful command to WebDriver which returns an http error response gives back the expected exception
     */
    public function testWebDriverErrorResponse()
    {
        try {
            $this->session->open($this->getTestDocumentRootUrl() . '/test/Assets/index.html');
            $this->session->element('css selector', '#a-quite-unlikely-html-element-id');
            $this->fail('Exception not thrown while looking for missing element in page');
        } catch (\Exception $e) {
            $this->assertEquals(NoSuchElement::class, get_class($e), $e->getMessage());
        }
    }

    ///**
    // * Checks that a successful command to WebDriver which returns 'nothing' according to spec does not raise an error
    // */
    //public function testWebDriverNoResponse()
    //{
    //    $timeouts = $this->session->timeouts();
    //    $out = $timeouts->async_script(array('type' => 'implicit', 'ms' => 1000));
    //    $this->assertEquals(null, $out);
    //}

    ///**
    // * Assert that empty response does not trigger exception, but invalid JSON does
    // */
    //public function testNonJsonResponse()
    //{
    //    $mockCurlService = $this->createMock(CurlService::class);
    //    $mockCurlService->expects($this->any())
    //        ->method('execute')
    //        ->will($this->returnCallback(function ($requestMethod, $url) {
    //            $info = array(
    //                'url' => $url,
    //                'request_method' => $requestMethod,
    //                'http_code' => 200,
    //            );

    //            $result = preg_match('#.*session$#', $url)
    //                ? $result = 'some invalid json'
    //                : $result = '';

    //            return array($result, $info);
    //        }));

    //    ServiceFactory::getInstance()->setService('service.curl', $mockCurlService);

    //    $this->driver  = new WebDriver($this->getTestWebDriverRootUrl());
    //    $result = $this->driver->status();

    //    $this->assertNull($result);

    //    // Test /session should error
    //    $this->expectException(\WebDriver\Exception\CurlExec::class);
    //    $this->expectExceptionMessage('Payload received from webdriver is not valid json: some invalid json');

    //    $result = $this->driver->session();

    //    $this->assertNull($result);
    //}
}
