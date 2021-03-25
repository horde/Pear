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
namespace Horde\Pear\Unit\Package\Xml;
use Horde\Pear\TestCase;
use \Horde_Pear_Package_Xml;
use \Horde_Pear_Package_Xml_Element_Directory;
use \Horde_Pear_Package_Xml_Directory;

/**
 * Test the directory handler.
 *
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @category   Horde
 * @copyright  2011-2017 Horde LLC
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package    Pear
 * @subpackage UnitTests
 */
class DirectoryTest extends TestCase
{
    public function testGetFiles()
    {
        $this->assertEquals(
            array(
                '/lib/Old.php',
                '/lib/Stays.php',
                '/test.php'
            ),
            $this->_getList(__DIR__ . '/../../../fixture/horde/framework/directory')->getFiles()
        );
    }

    private function _getList($package)
    {
        $xml = new Horde_Pear_Package_Xml(
            fopen($package . '/package.xml', 'r')
        );
        $element = new Horde_Pear_Package_Xml_Element_Directory('/');
        $element->setDocument($xml);
        $element->setDirectoryNode(
            $xml->findNode('/p:package/p:contents/p:dir')
        );
        return new Horde_Pear_Package_Xml_Directory($element, $xml);
    }
}
