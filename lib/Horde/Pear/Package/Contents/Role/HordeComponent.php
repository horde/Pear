<?php
/**
 * Copyright 2011-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Pear
 */

/**
 * Horde_Pear_Package_Contents_Role_HordeComponent:: handles file roles for
 * Horde components.
 *
 * @author    Gunnar Wrobel <wrobel@pardus.de>
 * @category  Horde
 * @copyright 2011-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Pear
 */
class Horde_Pear_Package_Contents_Role_HordeComponent
implements Horde_Pear_Package_Contents_Role
{
    /**
     * Tell which role the specified file has.
     *
     * @param string $file The file name.
     *
     * @return string The role of the file.
     */
    public function getRole($file)
    {
        $elements = explode('/', substr($file, 1));
        $basedir = array_shift($elements);
        switch ($basedir) {
        case 'bin':
            return 'script';
        case 'COPYING':
        case 'README':
        case 'README.md':
        case 'README.rst':
        case 'doc':
        case 'examples':
            return 'doc';
        case 'data':
        case 'locale':
        case 'migration':
            return 'data';
        case 'js':
            return 'horde';
        case 'test':
            return 'test';
        default:
            return 'php';
        }
    }
}
