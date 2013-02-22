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
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace Zend\Config\Writer;

use Zend\Config\Yaml as YamlConfig,
    Zend\Config\Exception;

/**
 * @category   Zend
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Yaml extends AbstractFileWriter
{
    /**
     * What to call when we need to decode some YAML?
     *
     * @var callable
     */
    protected $_yamlEncoder = array('Zend\Config\Writer\Yaml', 'encode');

    /**
     * Get callback for decoding YAML
     *
     * @return callable
     */
    public function getYamlEncoder()
    {
        return $this->_yamlEncoder;
    }

    /**
     * Set callback for decoding YAML
     *
     * @param  callable $yamlEncoder the decoder to set
     * @return Zend_Config_Yaml
     */
    public function setYamlEncoder($yamlEncoder)
    {
        if (!is_callable($yamlEncoder)) {
            throw new Exception\InvalidArgumentException('Invalid parameter to setYamlEncoder - must be callable');
        }

        $this->_yamlEncoder = $yamlEncoder;
        return $this;
    }

    /**
     * Render a Zend_Config into a YAML config string.
     *
     * @since 1.10
     * @return string
     */
    public function render()
    {
        $data        = $this->_config->toArray();
        $sectionName = $this->_config->getSectionName();
        $extends     = $this->_config->getExtends();

        if (is_string($sectionName)) {
            $data = array($sectionName => $data);
        }

        foreach ($extends as $section => $parentSection) {
            $data[$section][YamlConfig::EXTENDS_NAME] = $parentSection;
        }

        // Ensure that each "extends" section actually exists
        foreach ($data as $section => $sectionData) {
            if (is_array($sectionData) && isset($sectionData[YamlConfig::EXTENDS_NAME])) {
                $sectionExtends = $sectionData[YamlConfig::EXTENDS_NAME];
                if (!isset($data[$sectionExtends])) {
                    // Remove "extends" declaration if section does not exist
                    unset($data[$section][YamlConfig::EXTENDS_NAME]);
                }
            }
        }

        return call_user_func($this->getYamlEncoder(), $data);
    }

    /**
     * Very dumb YAML encoder
     *
     * Until we have Zend_Yaml...
     *
     * @param array $data YAML data
     * @return string
     */
    public static function encode($data)
    {
        return self::_encodeYaml(0, $data);
    }

    /**
     * Service function for encoding YAML
     *
     * @param int $indent Current indent level
     * @param array $data Data to encode
     * @return string
     */
    protected static function _encodeYaml($indent, $data)
    {
        reset($data);
        $result = "";
        $numeric = is_numeric(key($data));

        foreach($data as $key => $value) {
            if(is_array($value)) {
                $encoded = "\n".self::_encodeYaml($indent+1, $value);
            } else {
                $encoded = (string)$value."\n";
            }
            $result .= str_repeat("  ", $indent).($numeric?"- ":"$key: ").$encoded;
        }
        return $result;
    }
}
