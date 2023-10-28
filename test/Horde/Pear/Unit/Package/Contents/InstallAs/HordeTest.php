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
namespace Horde\Pear\Unit\Package\Contents\InstallAs;
use Horde\Pear\TestCase;
use \Horde_Pear_Package_Type_Horde;

/**
 * Test the install paths for base applications.
 *
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @category   Horde
 * @copyright  2011-2017 Horde LLC
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package    Pear
 * @subpackage UnitTests
 */
class HordeTest extends TestCase
{
    public function testInstallAsType()
    {
        $this->assertInstanceOf(
            'Horde_Pear_Package_Contents_InstallAs_Horde',
            $this->_getFixture()->getInstallAs()
        );
    }

    public function testInstallAsForScripts()
    {
        $this->assertEquals(
            'horde-bin',
            $this->_getFixture()->getInstallAs()->getInstallAs('/bin/horde-bin', 'horde')
        );
    }

    public function testInstallAsForDocs()
    {
        $this->assertEquals(
            'doc.txt',
            $this->_getFixture()->getInstallAs()->getInstallAs('/doc/doc.txt', 'horde')
        );
    }

    public function testInstallAsForTests()
    {
        $this->assertEquals(
            'test.php',
            $this->_getFixture()->getInstallAs()->getInstallAs('/test/test.php', 'horde')
        );
    }

    public function testInstallAsForPhp()
    {
        $this->assertEquals(
            'index.php',
            $this->_getFixture()->getInstallAs()->getInstallAs('/index.php', 'horde')
        );
    }

    private function _getFixture()
    {
        return new Horde_Pear_Package_Type_Horde(
            __DIR__ . '/../../../../fixture/horde/horde'
        );
    }
}
