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
use \Horde_Pear_Rest_Package;

/**
 * Test the package information parser.
 *
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @category   Horde
 * @copyright  2011-2017 Horde LLC
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package    Pear
 * @subpackage UnitTests
 */
class PackageTest extends Horde_Pear_TestCase
{
    public function testName()
    {
        $this->assertEquals('Horde_Core', $this->_getPackage()->getName());
    }

    public function testChannel()
    {
        $this->assertEquals('pear.horde.org', $this->_getPackage()->getChannel());
    }

    public function testLicense()
    {
        $this->assertEquals('LGPL-2.1', $this->_getPackage()->getLicense());
    }

    public function testSummary()
    {
        $this->assertEquals(
            'Horde Core Framework libraries',
            $this->_getPackage()->getSummary()
        );
    }

    public function testDescription()
    {
        $this->assertEquals(
            'These classes provide the core functionality of the Horde Application Framework.',
            $this->_getPackage()->getDescription()
        );
    }

    public function testDescriptionFromStream()
    {
        $this->assertEquals(
            'These classes provide the core functionality of the Horde Application Framework.',
            $this->_getStreamPackage()->getDescription()
        );
    }

    private function _getPackage()
    {
        return new Horde_Pear_Rest_Package(
            $this->_getInformation()
        );
    }

    private function _getStreamPackage()
    {
        return new Horde_Pear_Rest_Package(
            fopen(__DIR__ . '/../../fixture/rest/package.xml', 'r')
        );
    }

    private function _getInformation()
    {
        return file_get_contents(
            __DIR__ . '/../../fixture/rest/package.xml'
        );
    }
}
