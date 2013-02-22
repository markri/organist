<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Loader
 * @subpackage Exception
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Generate class maps for use with autoloading.
 *
 * Usage:
 * --help|-h                    Get usage message
 * --library|-l [ <string> ]    Library to parse; if none provided, assumes 
 *                              current directory
 * --output|-o [ <string> ]     Where to write autoload file; if not provided, 
 *                              assumes "autoload_classmap.php" in library directory
 * --append|-a                  Append to autoload file if it exists
 * --overwrite|-w               Whether or not to overwrite existing autoload 
 *                              file
 */

$libPath = __DIR__ . '/../library';
if (!is_dir($libPath)) {
    // Try to load StandardAutoloader from include_path
    if (false === include('Zend/Loader/StandardAutoloader.php')) {
        echo "Unable to locate autoloader via include_path; aborting" . PHP_EOL;
        exit(2);
    }
} else {
    // Try to load StandardAutoloader from library
    if (false === include(__DIR__ . '/../library/Zend/Loader/StandardAutoloader.php')) {
        echo "Unable to locate autoloader via library; aborting" . PHP_EOL;
        exit(2);
    }
}

// Setup autoloading
$loader = new Zend\Loader\StandardAutoloader();
$loader->register();

$rules = array(
    'help|h'        => 'Get usage message',
    'library|l-s'   => 'Library to parse; if none provided, assumes current directory',
    'output|o-s'    => 'Where to write plugin map file; if not provided, assumes "plugin_classmap.php" in library directory',
    'append|a'      => 'Append to plugin map file if it exists',
    'overwrite|w'   => 'Whether or not to overwrite existing autoload file',
);

try {
    $opts = new Zend\Console\Getopt($rules);
    $opts->parse();
} catch (Zend\Console\Getopt\Exception $e) {
    echo $e->getUsageMessage();
    exit(2);
}

if ($opts->getOption('h')) {
    echo $opts->getUsageMessage();
    exit();
}

$path = $libPath;
if (array_key_exists('PWD', $_SERVER)) {
    $path = $_SERVER['PWD'];
}

if (isset($opts->l)) {
    $libraryPath = $opts->l;
    $libraryPath = rtrim($libraryPath, '/\\') . DIRECTORY_SEPARATOR;
    if (!is_dir($libraryPath)) {
        echo "Invalid library directory provided" . PHP_EOL . PHP_EOL;
        echo $opts->getUsageMessage();
        exit(2);
    }
    $path = realpath($libraryPath);
}

$usingStdout = false;
$appending = $opts->getOption('a');
$output = $path . DIRECTORY_SEPARATOR . 'plugin_classmap.php';
if (isset($opts->o)) {
    $output = $opts->o;
    if ('-' == $output) {
        $output = STDOUT;
        $usingStdout = true;
    } elseif (!is_writeable(dirname($output))) {
        echo "Cannot write to '$output'; aborting." . PHP_EOL
            . PHP_EOL
            . $opts->getUsageMessage();
        exit(2);
    } elseif (file_exists($output)) {
        if (!$opts->getOption('w') && !$appending) {
            echo "Plugin map file already exists at '$output'," . PHP_EOL
                . "but 'overwrite' flag was not specified; aborting." . PHP_EOL 
                . PHP_EOL
                . $opts->getUsageMessage();
            exit(2);
        }
    }
}

if (!$usingStdout) {
    if ($appending) {
        echo "Appending to plugin class map '$output' for classes in '$path'..." . PHP_EOL;
    } else {
        echo "Creating plugin class map for classes in '$path'..." . PHP_EOL;
    }
}

// Get the ClassFileLocator, and pass it the library path
$l = new \Zend\File\ClassFileLocator($path);

// Iterate over each element in the path, and create a map of pluginname => classname 
$map    = new \stdClass;
foreach($l as $file) {
    $namespace = empty($file->namespace) ? '' : $file->namespace . '\\';
    $plugin    = strtolower($file->classname);
    $class     = $namespace . $file->classname;

    $map->{$plugin} = $class;
}

if ($appending) {

    $content = var_export((array) $map, true) . ';';

    // Fix \' strings from injected DIRECTORY_SEPARATOR usage in iterator_apply op
    $content = str_replace("\\'", "'", $content);

    // Convert to an array and remove the first "array ("
    $content = explode(PHP_EOL, $content);
    array_shift($content);

    // Load existing class map file and remove the closing "bracket ");" from it
    $existing = file($output, FILE_IGNORE_NEW_LINES);
    array_pop($existing); 

    // Merge
    $content = implode(PHP_EOL, $existing + $content);
} else {
    // Create a file with the class/file map.
    // Stupid syntax highlighters make separating < from PHP declaration necessary
    $content = '<' . "?php\n\n"
             . "// plugin class map\n" 
             . "// auto-generated using "
             . basename($_SERVER['argv'][0]) . ', ' . date('Y-m-d H:i:s') . "\n\n"
             . 'return ' . var_export((array) $map, true) . ';';

    // Fix \' strings from injected DIRECTORY_SEPARATOR usage in iterator_apply op
    $content = str_replace("\\'", "'", $content);
}

// Write the contents to disk
file_put_contents($output, $content);

if (!$usingStdout) {
    echo "Wrote plugin classmap file to '" . realpath($output) . "'" . PHP_EOL;
}
