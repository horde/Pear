<?php

/**
 * Copyright 2011-2026 Horde LLC (http://www.horde.org/)
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

namespace Horde\Pear\Unit\Package;

use Horde\Pear\TestCase;
use Horde_Pear_Package_Dependencies;

/**
 * Test the dependency handling.
 *
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @category   Horde
 * @copyright  2011-2017 Horde LLC
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package    Pear
 * @subpackage UnitTests
 * @coversNothing
 */
class DependenciesTest extends TestCase
{
    public function testPhp()
    {
        $result = [];
        Horde_Pear_Package_Dependencies::addDependency(
            ['min' => '5.2.0'],
            'php',
            'yes',
            $result
        );
        $this->assertEquals(
            [
                [
                    'type' => 'php',
                    'optional' => 'no',
                    'rel' => 'ge',
                    'version' => '5.2.0',
                ],
            ],
            $result
        );
    }

    public function testPearinstaller()
    {
        $result = [];
        Horde_Pear_Package_Dependencies::addDependency(
            ['min' => '1.0.0', 'max' => '2.0.0'],
            'pearinstaller',
            'yes',
            $result
        );
        $this->assertEquals(
            [
                [
                    'type' => 'pkg',
                    'name' => 'PEAR',
                    'channel' => 'pear.php.net',
                    'optional' => 'no',
                    'rel' => 'ge',
                    'version' => '1.0.0',
                ],
                [
                    'type' => 'pkg',
                    'name' => 'PEAR',
                    'channel' => 'pear.php.net',
                    'optional' => 'no',
                    'rel' => 'le',
                    'version' => '2.0.0',
                ],
            ],
            $result
        );
    }

    public function testPackage()
    {
        $result = [];
        Horde_Pear_Package_Dependencies::addDependency(
            ['name' => 'test', 'channel' => 'x', 'min' => '1.0.0'],
            'package',
            'yes',
            $result
        );
        $this->assertEquals(
            [
                [
                    'name' => 'test',
                    'channel' => 'x',
                    'min' => '1.0.0',
                    'type' => 'pkg',
                    'optional' => 'yes',
                    'rel' => 'ge',
                    'version' => '1.0.0',
                ],
            ],
            $result
        );
    }

    public function testExtension()
    {
        $result = [];
        Horde_Pear_Package_Dependencies::addDependency(
            ['name' => 'Z'],
            'extension',
            'yes',
            $result
        );
        $this->assertEquals(
            [
                [
                    'name' => 'Z',
                    'type' => 'ext',
                    'optional' => 'yes',
                ],
            ],
            $result
        );
    }

    /**
     * @expectedException Horde_Pear_Exception
     */
    public function testUnsupported()
    {
        $this->expectException('Horde_Pear_Exception');
        $result = [];
        Horde_Pear_Package_Dependencies::addDependency(
            ['name' => 'Z'],
            'unsupported',
            'yes',
            $result
        );
    }
}
