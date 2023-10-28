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
namespace Horde\Pear\Unit\Access;
use Horde_Pear_TestCase;
use \Horde_Pear_Rest_Releases;

/**
 * Test the releases parser.
 *
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @category   Horde
 * @copyright  2011-2017 Horde LLC
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package    Pear
 * @subpackage UnitTests
 */
class ReleasesTest extends Horde_Pear_TestCase
{
    public function testCount()
    {
        $rl = $this->_getReleases();
        $this->assertEquals(5, count($rl));
    }

    public function testVersion()
    {
        $rl = $this->_getReleases();
        $this->assertEquals('1.2.0', (string)$rl->r[0]->v);
    }

    public function testStability()
    {
        $rl = $this->_getReleases();
        $this->assertEquals('stable', (string)$rl->r[0]->s);
    }

    public function testGetReleases()
    {
        $this->assertEquals(
            array(
                '1.0.0' => 'stable',
                '1.0.0alpha1' => 'alpha',
                '1.0.0beta1' => 'beta',
                '1.0.1' => 'stable',
                '1.2.0' => 'stable',
            ),
            $this->_getReleases()->getReleases()
        );
    }

    public function testVersions()
    {
        $this->assertEquals(
            array('1.2.0', '1.0.1', '1.0.0', '1.0.0beta1', '1.0.0alpha1'), 
            $this->_getReleases()->listReleases()
        );
    }

    public function testGetReleaseStability()
    {
        $this->assertEquals(
            'stable', 
            $this->_getReleases()->getReleaseStability('1.2.0')
        );
    }

    /**
     * @expectedException Horde_Pear_Exception
     */
    public function testGetInvalidReleasesStability()
    {
        $this->expectException('Horde_Pear_Exception');
        $this->_getReleases()->getReleaseStability('0.0.2');
    }

    public function testGetReleaseStabilityWithStream()
    {
        $this->assertEquals(
            'stable', 
            $this->_getStreamReleases()->getReleaseStability('1.2.0')
        );
    }

    private function _getReleases()
    {
        return new Horde_Pear_Rest_Releases(
            $this->_getInput()
        );
    }

    private function _getStreamReleases()
    {
        return new Horde_Pear_Rest_Releases(
            fopen(__DIR__ . '/../../fixture/rest/releases.xml', 'r')
        );
    }

    private function _getInput()
    {
        return file_get_contents(
            __DIR__ . '/../../fixture/rest/releases.xml'
        );
    }
}
