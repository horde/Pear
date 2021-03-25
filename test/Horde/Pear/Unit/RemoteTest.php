<?php
/**
 * Copyright 2011-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @category   Horde
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package    Pear
 * @subpackage UnitTests
 */
namespace Horde\Pear\Unit;
use Horde\Pear\TestCase;
use \Horde_Pear_Stub_Request;
use \Horde_Support_StringStream;
use \Horde_Http_Response_Mock;
use \Horde_Http_Request_Mock;
/**
 * Test the remote server handler.
 *
 * Test the package contents.
 *
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @category   Horde
 * @copyright  2011-2017 Horde LLC
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package    Pear
 * @subpackage UnitTests
 */
class RemoteTest extends TestCase
{
    public function testListPackages()
    {
        $this->assertIsArray(
            $this->getRemoteList()->listPackages()
        );
    }

    public function testListPackagesContainsComponents()
    {
        $this->assertEquals(
            array('A', 'B'),
            $this->getRemoteList()->listPackages()
        );
    }

    public function testLatest()
    {
        $this->assertEquals(
            '1.0.0',
            $this->_getLatestRemote()->getLatestRelease('A')
        );
    }

    public function testLatestUri()
    {
        $this->assertEquals(
            'https://pear.horde.org/get/A-1.0.0.tgz',
            $this->_getLatestRemote()->getLatestDownloadUri('A')
        );
    }

    /**
     * @expectedException Horde_Pear_Exception
     */
    public function testLatestUriExceptionForNoRelease()
    {
        $this->expectException('Horde_Pear_Exception');
        $this->_getLatestRemote()->getLatestDownloadUri('A', 'dev');
    }

    public function testNoDetails()
    {
        $this->assertFalse(
            $this->_getNoLatest()->getLatestDetails('X', null)
        );
    }

    public function testLatestDetails()
    {
        $this->assertEquals(
            '1.0.0',
            $this->_getLatest()->getLatestDetails('A', null)->getVersion()
        );
    }

    public function testDependencies()
    {
        $this->assertEquals(
            array(array('name' => 'test', 'type' => 'pkg', 'optional' => 'no')),
            $this->_getRemoteDependencies()->getDependencies('A', '1.0.0')
        );
    }

    public function testChannel()
    {
        $this->assertEquals(
            'a:1:{s:8:"required";a:1:{s:7:"package";a:1:{s:4:"name";s:4:"test";}}}',
            $this->_getRemoteDependencies()->getChannel()
        );
    }

    public function testPackageXml()
    {
        $this->assertInstanceOf(
            'Horde_Pear_Package_Xml',
            $this->_getPackageXml()->getPackageXml('A', null)
        );
    }


    private function _getRemoteDependencies()
    {
        if (!class_exists('Horde_Http_Client')) {
            $this->markTestSkipped('Horde_Http is missing!');
        }
        $string = serialize(array('required' => array('package' => array('name' => 'test'))));
        $body = new Horde_Support_StringStream($string);
        $response = new Horde_Http_Response_Mock('', $body->fopen());
        $response->code = 200;
        $request = new Horde_Http_Request_Mock();
        $request->setResponse($response);
        return $this->createRemote($request);
    }

    private function _getLatestRemote()
    {
        if (!class_exists('Horde_Http_Client')) {
            $this->markTestSkipped('Horde_Http is missing!');
        }
        $request = new Horde_Pear_Stub_Request();
        $request->setResponses(
            array(
                array(
                    'body' => '1.0.0',
                    'code' => 200,
                ),
                array(
                    'body' => '',
                    'code' => 404,
                ),
                array(
                    'body' => '',
                    'code' => 404,
                ),
                array(
                    'body' => '',
                    'code' => 404,
                ),
                array(
                    'body' => $this->_getRelease(),
                    'code' => 200,
                ),
            )
        );
        return $this->createRemote($request);
    }

    private function _getNoLatest()
    {
        if (!class_exists('Horde_Http_Client')) {
            $this->markTestSkipped('Horde_Http is missing!');
        }
        $request = new Horde_Pear_Stub_Request();
        $request->setResponses(
            array(
                array(
                    'body' => '',
                    'code' => 404,
                ),
            )
        );
        return $this->createRemote($request);
    }

    private function _getLatest()
    {
        if (!class_exists('Horde_Http_Client')) {
            $this->markTestSkipped('Horde_Http is missing!');
        }
        $request = new Horde_Pear_Stub_Request();
        $request->setResponses(
            array(
                array(
                    'body' => '1.0.0',
                    'code' => 200,
                ),
                array(
                    'body' => $this->_getRelease(),
                    'code' => 200,
                ),
            )
        );
        return $this->createRemote($request);
    }

    private function _getPackageXml()
    {
        if (!class_exists('Horde_Http_Client')) {
            $this->markTestSkipped('Horde_Http is missing!');
        }
        $request = new Horde_Pear_Stub_Request();
        $request->setResponses(
            array(
                array(
                    'body' => file_get_contents(
                        __DIR__ . '/../fixture/rest/package.xml'
                    ),
                    'code' => 404,
                ),
            )
        );
        return $this->createRemote($request);
    }
}
