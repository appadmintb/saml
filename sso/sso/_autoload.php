<?php

/**
 * This file implements a autoloader for simpleSAMLphp. This autoloader
 * will search for files under the simpleSAMLphp directory.
 *
 * @author Olav Morken, UNINETT AS.
 * @package simpleSAMLphp
 * @version $Id$
 */

/**
 * Autoload function for simpleSAMLphp.
 *
 * It will autoload all classes stored in the lib-directory.
 *
 * @param $className  The name of the class.
 */
function SimpleSAML_autoload($className) {

    $libDir = dirname(__FILE__) . '/';

    /* Special handling for xmlseclibs.php. */
    if (in_array($className, array('XMLSecurityKey', 'XMLSecurityDSig', 'XMLSecEnc'), TRUE)) {
        require_once($libDir . 'xmlseclibs.php');
        return;
    }

    /* Handling of modules. */
    if (substr($className, 0, 7) === 'sspmod_') {
        $modNameEnd = strpos($className, '_', 7);
        $module = substr($className, 7, $modNameEnd - 7);
        $moduleClass = substr($className, $modNameEnd + 1);

        if (!SimpleSAML_Module::isModuleEnabled($module)) {
            return;
        }

        $file = SimpleSAML_Module::getModuleDir($module) . '/lib/' . str_replace('_', '/', $moduleClass) . '.php';
    } else {
        $file = $libDir . str_replace('_', '/', $className) . '.php';
    }

    if (file_exists($file)) {
        require_once($file);
    }
}

/* Register autoload function for simpleSAMLphp. */
spl_autoload_register('SimpleSAML_autoload');

?>
