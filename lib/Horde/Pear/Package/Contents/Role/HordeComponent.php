<?php
/**
 * Copyright 2011-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
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
        case 'config':
            return 'cfg';
        case 'bin':
            return 'script';
        case 'COPYING':
        case 'LICENSE':
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
        case 'themes':
            return 'horde';
        case 'test':
            return 'test';
        default:
            return 'php';
        }
    }

    /**
     * Tell which replacement tasks are required for the specified file.
     *
     * @param string $file The file path inside the component.
     * @param string $root The filesystem root of the component.
     *
     * @return array A list of <tasks:replace> attribute hashes.
     */
    public function getReplace($file, $root)
    {
        $elements = explode('/', substr($file, 1));
        $basedir = array_shift($elements);
        switch ($basedir) {
        case 'bin':
            $contents = file_get_contents($root . '/' . $file);
            $replace = array();
            if (strpos($contents, '#!/usr/bin/env php') === 0) {
                $replace[] = array(
                    'from' => '/usr/bin/env php',
                    'to' => 'php_bin',
                    'type' => 'pear-config'
                );
            }
            if (strpos($contents, '@php_dir@')) {
                $replace[] = array(
                    'from' => '@php_dir@',
                    'to' => 'php_dir',
                    'type' => 'pear-config'
                );
            }
            return $replace;
        }
        $basename = array_pop($elements);
        if ($basename == 'Translation.php') {
            if (strpos(file_get_contents($root . '/' . $file), '@data_dir@')) {
                return array(array(
                    'from' => '@data_dir@',
                    'to' => 'data_dir',
                    'type' => 'pear-config'
                ));
            }
        }
        return array();
    }
}
