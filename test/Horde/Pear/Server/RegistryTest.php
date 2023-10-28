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
namespace Horde\Pear\Server;
use Horde_Pear_TestCase;

/**
 * Test the registry wrapper.
 *
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @category   Horde
 * @copyright  2011-2017 Horde LLC
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package    Pear
 * @subpackage UnitTests
 */
class RegistryTest extends Horde_Pear_TestCase
{
    private $_config;

    public function setUp(): void
    {
        $config = self::getConfig('PEAR_TEST_CONFIG');
        if ($config && !empty($config['pear']['config'])) {
            $this->_config = $config['pear']['config'];
            putenv('PHP_PEAR_SYSCONF_DIR=' . $this->_config);
        } else {
            $this->markTestSkipped('Missing configuration!');
        }
    }

    public function testListPackages()
    {
        $this->assertInternalType(
            'array',
            $this->_getRegistry()->listPackages()
        );
    }

    private function _getRegistry()
    {
        return new Horde_Pear_Registry();
    }
}
