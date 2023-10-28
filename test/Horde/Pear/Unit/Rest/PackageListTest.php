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

/**
 * Test the package list parser.
 *
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @category   Horde
 * @copyright  2011-2017 Horde LLC
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package    Pear
 * @subpackage UnitTests
 */
class PackageListTest extends Horde_Pear_TestCase
{
    public function testCount()
    {
        $pl = $this->_getPackageList();
        $this->assertEquals(2, count($pl));
    }

    public function testPackageName()
    {
        $pl = $this->_getPackageList();
        $this->assertEquals('Horde_ActiveSync', (string)$pl->p[0]);
    }

    public function testPackageLink()
    {
        $pl = $this->_getPackageList();
        $this->assertEquals('/rest/p/horde_activesync', $pl->p[0]['xlink:href']);
    }

    public function testGetPackages()
    {
        $this->assertEquals(
            array(
                'Horde_ActiveSync' => '/rest/p/horde_activesync',
                'Horde_Alarm' => '/rest/p/horde_alarm'
            ),
            $this->_getPackageList()->getPackages()
        );
    }

    public function testPackageNames()
    {
        $this->assertEquals(
            array('Horde_ActiveSync', 'Horde_Alarm'), 
            $this->_getPackageList()->listPackages()
        );
    }

    public function testGetPackageLink()
    {
        $this->assertEquals(
            '/rest/p/horde_alarm', 
            $this->_getPackageList()->getPackageLink('Horde_Alarm')
        );
    }

    /**
     * @expectedException Horde_Pear_Exception
     */
    public function testGetInvalidPackageLink()
    {
        $this->_getPackageList()->getPackageLink('Horde_NoSuchPackage');
    }

    private function _getPackageList()
    {
        return new Horde_Pear_Rest_PackageList(
            $this->_getList()
        );
    }

    private function _getList()
    {
        return '<?xml version="1.0" encoding="UTF-8" ?>
<l xmlns="http://pear.php.net/dtd/rest.categorypackages" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xlink="http://www.w3.org/1999/xlink" xsi:schemaLocation="http://pear.php.net/dtd/rest.categorypackages http://pear.php.net/dtd/rest.categorypackages.xsd">
  <p xlink:href="/rest/p/horde_activesync">Horde_ActiveSync</p>
  <p xlink:href="/rest/p/horde_alarm">Horde_Alarm</p>
</l>';
    }
}
