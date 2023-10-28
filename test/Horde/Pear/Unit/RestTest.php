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
use Horde_Pear_TestCase;

/**
 * Test the REST connector.
 *
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @category   Horde
 * @copyright  2011-2017 Horde LLC
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package    Pear
 * @subpackage UnitTests
 */
class RestTest extends Horde_Pear_TestCase
{
    public function testFetchPackageList()
    {
        $this->assertInternalType(
            'resource',
            $this->_getRest()->fetchPackageList()
        );
    }

    public function testPackageListResponse()
    {
        $response = $this->_getRest()->fetchPackageList();
        rewind($response);
        $this->assertEquals(
            'RESPONSE',
            stream_get_contents($response)
        );
    }

    public function testPackageInformationResponse()
    {
        $response = $this->_getRest()->fetchPackageInformation('TEST');
        rewind($response);
        $this->assertEquals(
            'RESPONSE',
            stream_get_contents($response)
        );
    }

    public function testPackageReleasesResponse()
    {
        $response = $this->_getRest()->fetchPackageReleases('TEST');
        rewind($response);
        $this->assertEquals(
            'RESPONSE',
            stream_get_contents($response)
        );
    }

    public function testPackageLatest()
    {
        $this->assertInternalType(
            'array',
            $this->_getRest()->fetchLatestPackageReleases('TEST')
        );
    }

    public function testPackageLatestArray()
    {
        $result = $this->_getRest()->fetchLatestPackageReleases('TEST');
        $this->assertEquals(
            'RESPONSE',
            $result['stable']
        );
    }

    public function testReleaseInformationResponse()
    {
        $response = $this->_getRest()->fetchReleaseInformation('TEST', '1');
        rewind($response);
        $this->assertEquals(
            'RESPONSE',
            stream_get_contents($response)
        );
    }

    public function testReleasePackageXmlResponse()
    {
        $response = $this->_getRest()->fetchReleasePackageXml('TEST', '1');
        rewind($response);
        $this->assertEquals(
            'RESPONSE',
            stream_get_contents($response)
        );
    }

    public function testPackageDependenciesResponse()
    {
        $response = $this->_getRest()->fetchPackageDependencies('TEST', '1');
        $this->assertEquals(
            'RESPONSE',
            $response
        );
    }

    public function testChannelResponse()
    {
        $response = $this->_getRest()->fetchChannelXml();
        $this->assertEquals(
            'RESPONSE',
            $response
        );
    }

    public function testReleaseExists()
    {
        $this->assertTrue($this->_getRest()->releaseExists('TEST', '1'));
    }

    public function testReleaseDoesNotExists()
    {
        $this->assertFalse($this->_getRest(404)->releaseExists('TEST', '1'));
    }

    public function testFetchLatest()
    {
        $this->assertEquals(
            'RESPONSE',
            $this->_getRest()->fetchLatestRelease('TEST')
        );
    }

    public function testFetchNoLatest()
    {
        $this->assertFalse($this->_getRest(404)->fetchLatestRelease('TEST'));
    }

    private function _getRest($code = 200)
    {
        if (!class_exists('Horde_Http_Client')) {
            $this->markTestSkipped('Horde_Http is missing!');
        }
        $string = 'RESPONSE';
        $body = new Horde_Support_StringStream($string);
        $response = new Horde_Http_Response_Mock('', $body->fopen());
        $response->code = $code;
        $request = new Horde_Http_Request_Mock();
        $request->setResponse($response);
        return new Horde_Pear_Rest(
            new Horde_Http_Client(array('request' => $request)),
            ''
        );
    }
}
