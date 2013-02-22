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
 * @category  Zend
 * @package   Zend_Config
 * @copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Config;

use Zend\Json\Json as JsonUtil;

/**
 * JSON Adapter for Zend_Config
 *
 * @category  Zend
 * @package   Zend_Config
 * @copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Json extends Config
{
    /**
     * Name of object key indicating section current section extends
     */
    const EXTENDS_NAME = "_extends";

    /**
     * Whether or not to ignore constants in the JSON string
     *
     * Note: if you do not have constant names in quotations in your JSON
     * string, they may lead to syntax errors when parsing.
     *
     * @var bool
     */
    protected $_ignoreConstants = false;

    /**
     * Whether to skip extends or not
     *
     * @var boolean
     */
    protected $_skipExtends = false;

    /**
     * Loads the section $section from the config file encoded as JSON
     *
     * Sections are defined as properties of the main object
     *
     * In order to extend another section, a section defines the "_extends"
     * property having a value of the section name from which the extending
     * section inherits values.
     *
     * Note that the keys in $section will override any keys of the same
     * name in the sections that have been included via "_extends".
     *
     * @param  string  $json     JSON file or string to process
     * @param  mixed   $section Section to process
     * @param  boolean $options Whether modifiacations are allowed at runtime
     * @throws Exception\InvalidArgumentException When JSON text is not set or cannot be loaded
     * @throws Exception\RuntimeException When section $sectionName cannot be found in $json
     */
    public function __construct($json, $section = null, $options = false)
    {
        if (empty($json)) {
            throw new Exception\InvalidArgumentException('Filename is not set');
        }

        $allowModifications = false;
        if (is_bool($options)) {
            $allowModifications = $options;
        } elseif (is_array($options)) {
            foreach ($options as $key => $value) {
                switch (strtolower($key)) {
                    case 'allow_modifications':
                    case 'allowmodifications':
                        $allowModifications = (bool) $value;
                        break;
                    case 'skip_extends':
                    case 'skipextends':
                        $this->_skipExtends = (bool) $value;
                        break;
                    case 'ignore_constants':
                    case 'ignoreconstants':
                        $this->_ignoreConstants = (bool) $value;
                        break;
                    default:
                        break;
                }
            }
        }

        if ($json[0] != '{') {
            // read json file
            $this->_setErrorHandler();
            $content = file_get_contents($json, true);
            $errorMessages = $this->_restoreErrorHandler();
            if ($content === false) {
                $e = null;
                foreach ($errorMessages as $errMsg) {
                    $e = new Exception\RuntimeException($errMsg, 0, $e);
                }
                $e = new Exception\RuntimeException("Can't read file '{$json}'", 0, $e);
                throw $e;
            }
            $json = $content;
        }

        // Replace constants
        if (!$this->_ignoreConstants) {
            $json = $this->_replaceConstants($json);
        }

        // Parse/decode
        $config = JsonUtil::decode($json);

        if (null === $config) {
            // decode failed
            throw new Exception\RuntimeException("Error parsing JSON data");
        }

        // Flatten object structure into array
        $config = $this->flattenObjects($config);

        if ($section === null) {
            $dataArray = array();
            foreach ($config as $sectionName => $sectionData) {
                $dataArray[$sectionName] = $this->_processExtends($config, $sectionName);
            }

            parent::__construct($dataArray, $allowModifications);
        } elseif (is_array($section)) {
            $dataArray = array();
            foreach ($section as $sectionName) {
                if (!isset($config[$sectionName])) {
                    throw new Exception\RuntimeException(sprintf('Section "%s" cannot be found', $sectionName));
                }

                $dataArray = array_merge($this->_processExtends($config, $sectionName), $dataArray);
            }

            parent::__construct($dataArray, $allowModifications);
        } else {
            if (!isset($config[$section])) {
                throw new Exception\RuntimeException(sprintf('Section "%s" cannot be found', $section));
            }

            $dataArray = $this->_processExtends($config, $section);
            if (!is_array($dataArray)) {
                // Section in the JSON data contains just one top level string
                $dataArray = array($section => $dataArray);
            }

            parent::__construct($dataArray, $allowModifications);
        }

        $this->_loadedSection = $section;
    }

    /**
     * Helper function to process each element in the section and handle
     * the "_extends" inheritance attribute.
     *
     * @param  array            $data Data array to process
     * @param  string           $section Section to process
     * @param  array            $config  Configuration which was parsed yet
     * @throws Exception\RuntimeException When $section cannot be found
     * @return array
     */
    protected function _processExtends(array $data, $section, array $config = array())
    {
        if (!isset($data[$section])) {
            throw new Exception\RuntimeException(sprintf('Section "%s" cannot be found', $section));
        }

        $thisSection  = $data[$section];

        if (is_array($thisSection) && isset($thisSection[self::EXTENDS_NAME])) {
            if (is_array($thisSection[self::EXTENDS_NAME])) {
                throw new Exception\RuntimeException('Invalid extends clause: must be a string; array received');
            }
            $this->_assertValidExtend($section, $thisSection[self::EXTENDS_NAME]);

            if (!$this->_skipExtends) {
                $config = $this->_processExtends($data, $thisSection[self::EXTENDS_NAME], $config);
            }
            unset($thisSection[self::EXTENDS_NAME]);
        }

        $config = $this->_arrayMergeRecursive($config, $thisSection);

        return $config;
    }

    /**
     * Replace any constants referenced in a string with their values
     *
     * @param  string $value
     * @return string
     */
    protected function _replaceConstants($value)
    {
        foreach ($this->_getConstants() as $constant) {
            if (strstr($value, $constant)) {
                $value = str_replace($constant, constant($constant), $value);
            }
        }
        return $value;
    }

    /**
     * Get (reverse) sorted list of defined constant names
     *
     * @return array
     */
    protected function _getConstants()
    {
        $constants = array_keys(get_defined_constants());
        rsort($constants, SORT_STRING);
        return $constants;
    }

    /**
     * Flatten JSON object structure to associative array
     *
     * @param  object|array $config
     * @return array
     */
    protected function flattenObjects($config)
    {
        $flattened = array();
        foreach ($config as $key => $value) {
            if (is_object($value)) {
                $value = $this->flattenObjects($value);
            }
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    if (is_object($v)) {
                        $value[$k] = $this->flattenObjects($v);
                    }
                }
            }
            $flattened[$key] = $value;
        }
        return $flattened;
    }
}
