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
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Barcode;
use Zend\Barcode,
    Zend\Barcode\Renderer,
    Zend\Barcode\Object,
    Zend\Config\Config,
    Zend\Loader\PrefixPathLoader,
    Zend\Pdf;

/**
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage UnitTests
 * @group      Zend_Barcode
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Stores the original set timezone
     * @var string
     */
    private $_originaltimezone;

    public function setUp()
    {
        $this->originaltimezone = date_default_timezone_get();

        // Set timezone to avoid "It is not safe to rely on the system's timezone settings."
        // message if timezone is not set within php.ini
        date_default_timezone_set('GMT');
    }

    public function tearDown()
    {
        Barcode\Barcode::getObjectBroker()->setClassLoader(new Barcode\ObjectLoader);
        Barcode\Barcode::getObjectBroker()->unregister('code25');
        Barcode\Barcode::getObjectBroker()->unregister('code39');
        Barcode\Barcode::getObjectBroker()->unregister('error');
        Barcode\Barcode::getRendererBroker()->setClassLoader(new Barcode\RendererLoader);
        Barcode\Barcode::getRendererBroker()->unregister('image');
        Barcode\Barcode::getRendererBroker()->unregister('pdf');
        date_default_timezone_set($this->originaltimezone);
    }

    public function testMinimalFactory()
    {
        $this->checkGDRequirement();
        $renderer = Barcode\Barcode::factory('code39');
        $this->assertTrue($renderer instanceof Renderer\Image);
        $this->assertTrue($renderer->getBarcode() instanceof Object\Code39);
    }

    public function testMinimalFactoryWithRenderer()
    {
        $renderer = Barcode\Barcode::factory('code39', 'pdf');
        $this->assertTrue($renderer instanceof Renderer\Pdf);
        $this->assertTrue($renderer->getBarcode() instanceof Object\Code39);
    }

    public function testFactoryWithOptions()
    {
        $this->checkGDRequirement();
        $options = array('barHeight' => 123);
        $renderer = Barcode\Barcode::factory('code25', 'image', $options);
        $this->assertEquals(123, $renderer->getBarcode()->getBarHeight());
    }

    public function testFactoryWithAutomaticExceptionRendering()
    {
        $this->checkGDRequirement();
        $options = array('barHeight' => - 1);
        $renderer = Barcode\Barcode::factory('code39', 'image', $options);
        $this->assertTrue($renderer instanceof Renderer\Image);
        $this->assertTrue($renderer->getBarcode() instanceof Object\Error);
    }

    public function testFactoryWithoutAutomaticObjectExceptionRendering()
    {
        $this->setExpectedException('\Zend\Barcode\Object\Exception');
        $options = array('barHeight' => - 1);
        $renderer = Barcode\Barcode::factory('code39', 'image', $options, array(), false);
    }

    public function testFactoryWithoutAutomaticRendererExceptionRendering()
    {
        $this->setExpectedException('\Zend\Barcode\Renderer\Exception');
        $this->checkGDRequirement();
        $options = array('imageType' => 'my');
        $renderer = Barcode\Barcode::factory('code39', 'image', array(), $options, false);
        $this->markTestIncomplete('Need to throw a configuration exception in renderer');
    }

    public function testFactoryWithZendConfig()
    {
        $this->checkGDRequirement();
        $config = new Config(array('barcode'  => 'code39',
                                   'renderer' => 'image'));
        $renderer = Barcode\Barcode::factory($config);
        $this->assertTrue($renderer instanceof Renderer\Image);
        $this->assertTrue($renderer->getBarcode() instanceof Object\Code39);

    }

    public function testFactoryWithZendConfigAndObjectOptions()
    {
        $this->checkGDRequirement();
        $config = new Config(array('barcode'       => 'code25' ,
                                   'barcodeParams' => array(
                                   'barHeight'     => 123)));
        $renderer = Barcode\Barcode::factory($config);
        $this->assertTrue($renderer instanceof Renderer\Image);
        $this->assertTrue($renderer->getBarcode() instanceof Object\Code25);
        $this->assertEquals(123, $renderer->getBarcode()->getBarHeight());
    }

    public function testFactoryWithZendConfigAndRendererOptions()
    {
        $this->checkGDRequirement();
        $config = new Config(array('barcode'        => 'code25' ,
                                   'rendererParams' => array(
                                   'imageType'      => 'gif')));
        $renderer = Barcode\Barcode::factory($config);
        $this->assertTrue($renderer instanceof Renderer\Image);
        $this->assertTrue($renderer->getBarcode() instanceof Object\Code25);
        $this->assertSame('gif', $renderer->getImageType());
    }

    public function testFactoryWithoutBarcodeWithAutomaticExceptionRender()
    {
        $this->checkGDRequirement();
        $renderer = Barcode\Barcode::factory(null);
        $this->assertTrue($renderer instanceof Renderer\Image);
        $this->assertTrue($renderer->getBarcode() instanceof Object\Error);
    }

    public function testFactoryWithoutBarcodeWithAutomaticExceptionRenderWithZendConfig()
    {
        $this->checkGDRequirement();
        $config = new Config(array('barcode' => null));
        $renderer = Barcode\Barcode::factory($config);
        $this->assertTrue($renderer instanceof Renderer\Image);
        $this->assertTrue($renderer->getBarcode() instanceof Object\Error);
    }

    public function testFactoryWithExistingBarcodeObject()
    {
        $this->checkGDRequirement();
        $barcode = new Object\Code25();
        $renderer = Barcode\Barcode::factory($barcode);
        $this->assertSame($barcode, $renderer->getBarcode());
    }

    public function testBarcodeObjectFactoryWithExistingBarcodeObject()
    {
        $barcode = new Object\Code25();
        $generatedBarcode = Barcode\Barcode::makeBarcode($barcode);
        $this->assertSame($barcode, $generatedBarcode);
    }

    public function testBarcodeObjectFactoryWithBarcodeAsString()
    {
        $barcode = Barcode\Barcode::makeBarcode('code25');
        $this->assertTrue($barcode instanceof Object\Code25);
    }

    public function testBarcodeObjectFactoryWithBarcodeAsStringAndConfigAsArray()
    {
        $barcode = Barcode\Barcode::makeBarcode('code25', array('barHeight' => 123));
        $this->assertTrue($barcode instanceof Object\Code25);
        $this->assertSame(123, $barcode->getBarHeight());
    }

    public function testBarcodeObjectFactoryWithBarcodeAsStringAndConfigAsZendConfig()
    {
        $config = new Config(array('barHeight' => 123));
        $barcode = Barcode\Barcode::makeBarcode('code25', $config);
        $this->assertTrue($barcode instanceof Object\Code25);
        $this->assertSame(123, $barcode->getBarHeight());
    }

    public function testBarcodeObjectFactoryWithBarcodeAsZendConfig()
    {
        $config = new Config(array('barcode' => 'code25' ,
                                   'barcodeParams' => array(
                                   'barHeight' => 123)));
        $barcode = Barcode\Barcode::makeBarcode($config);
        $this->assertTrue($barcode instanceof Object\Code25);
        $this->assertSame(123, $barcode->getBarHeight());
    }

    public function testBarcodeObjectFactoryWithBarcodeAsZendConfigButNoBarcodeParameter()
    {
        $this->setExpectedException('\Zend\Barcode\Exception');
        $config = new Config(array('barcodeParams' => array('barHeight' => 123) ));
        $barcode = Barcode\Barcode::makeBarcode($config);
    }

    public function testBarcodeObjectFactoryWithBarcodeAsZendConfigAndBadBarcodeParameters()
    {
        $this->setExpectedException('\Zend\Barcode\Exception');
        $barcode = Barcode\Barcode::makeBarcode('code25', null);
    }

    public function testBarcodeObjectFactoryWithNamespace()
    {
        $loader = new PrefixPathLoader(array('ZendTest\Barcode\Object\TestAsset' => __DIR__ . '/Object/TestAsset'));
        Barcode\Barcode::getObjectBroker()->setClassLoader($loader);
        $barcode = Barcode\Barcode::makeBarcode('barcodeNamespace');
        $this->assertTrue($barcode instanceof \ZendTest\Barcode\Object\TestAsset\BarcodeNamespace);
    }

    public function testBarcodeObjectFactoryWithNamespaceExtendStandardLibray()
    {
        $loader = new PrefixPathLoader(array('Zend\Barcode\Object' => 'Zend/Barcode/Object',
                                             'ZendTest\Barcode\Object\TestAsset' => __DIR__ . '/Object/TestAsset'));
        Barcode\Barcode::getObjectBroker()->setClassLoader($loader);
        $barcode = Barcode\Barcode::makeBarcode('error');
        $this->assertTrue($barcode instanceof \ZendTest\Barcode\Object\TestAsset\Error);
    }

    public function testBarcodeObjectFactoryWithNamespaceButWithoutExtendingObjectAbstract()
    {
        $this->setExpectedException('\Zend\Barcode\Exception');
        $loader = new PrefixPathLoader(array('ZendTest\Barcode\Object\TestAsset' => __DIR__ . '/Object/TestAsset'));
        Barcode\Barcode::getObjectBroker()->setClassLoader($loader);
        $barcode = Barcode\Barcode::makeBarcode('barcodeNamespaceWithoutExtendingObjectAbstract');
    }

    public function testBarcodeObjectFactoryWithUnexistantBarcode()
    {
        $this->setExpectedException('\Zend\Loader\Exception\RuntimeException');
        $barcode = Barcode\Barcode::makeBarcode('zf123', array());
    }

    public function testBarcodeRendererFactoryWithExistingBarcodeRenderer()
    {
        $this->checkGDRequirement();
        $renderer = new Renderer\Image();
        $generatedBarcode = Barcode\Barcode::makeRenderer($renderer);
        $this->assertSame($renderer, $generatedBarcode);
    }

    public function testBarcodeRendererFactoryWithBarcodeAsString()
    {
        $this->checkGDRequirement();
        $renderer = Barcode\Barcode::makeRenderer('image');
        $this->assertTrue($renderer instanceof Renderer\Image);
    }

    public function testBarcodeRendererFactoryWithBarcodeAsStringAndConfigAsArray()
    {
        $this->checkGDRequirement();

        $renderer = Barcode\Barcode::makeRenderer('image', array('imageType' => 'gif'));
        $this->assertTrue($renderer instanceof Renderer\Image);
        $this->assertSame('gif', $renderer->getImageType());
    }

    public function testBarcodeRendererFactoryWithBarcodeAsStringAndConfigAsZendConfig()
    {
        $this->checkGDRequirement();
        $config = new Config(array('imageType' => 'gif'));
        $renderer = Barcode\Barcode::makeRenderer('image', $config);
        $this->assertTrue($renderer instanceof Renderer\Image);
        $this->assertSame('gif', $renderer->getimageType());
    }

    public function testBarcodeRendererFactoryWithBarcodeAsZendConfig()
    {
        $this->checkGDRequirement();
        $config = new Config(array('renderer'       => 'image' ,
                                   'rendererParams' => array('imageType' => 'gif')));
        $renderer = Barcode\Barcode::makeRenderer($config);
        $this->assertTrue($renderer instanceof Renderer\Image);
        $this->assertSame('gif', $renderer->getimageType());
    }

    public function testBarcodeRendererFactoryWithBarcodeAsZendConfigButNoBarcodeParameter()
    {
        $this->setExpectedException('\Zend\Barcode\Exception');
        $config = new Config(array('rendererParams' => array('imageType' => 'gif') ));
        $renderer = Barcode\Barcode::makeRenderer($config);
    }

    public function testBarcodeRendererFactoryWithBarcodeAsZendConfigAndBadBarcodeParameters()
    {
        $this->setExpectedException('\Zend\Barcode\Exception');
        $renderer = Barcode\Barcode::makeRenderer('image', null);
    }

    public function testBarcodeRendererFactoryWithNamespace()
    {
        $this->checkGDRequirement();
        $loader = new PrefixPathLoader(array('ZendTest\Barcode\Renderer\TestAsset' => __DIR__ . '/Renderer/TestAsset'));
        Barcode\Barcode::getRendererBroker()->setClassLoader($loader);
        $renderer = Barcode\Barcode::makeRenderer('rendererNamespace');
        $this->assertTrue($renderer instanceof \Zend\Barcode\Renderer);
    }

    public function testBarcodeFactoryWithNamespaceButWithoutExtendingRendererAbstract()
    {
        $this->setExpectedException('\Zend\Barcode\Exception');
        $loader = new PrefixPathLoader(array('ZendTest\Barcode\Renderer\TestAsset' => __DIR__ . '/Renderer/TestAsset'));
        Barcode\Barcode::getRendererBroker()->setClassLoader($loader);
        $renderer = Barcode\Barcode::makeRenderer('rendererNamespaceWithoutExtendingRendererAbstract');
    }

    public function testBarcodeRendererFactoryWithUnexistantRenderer()
    {
        $this->setExpectedException('\Zend\Loader\Exception\RuntimeException');
        $renderer = Barcode\Barcode::makeRenderer('zend', array());
    }

    public function testProxyBarcodeRendererDrawAsImage()
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped('GD extension is required to run this test');
        }
        $resource = Barcode\Barcode::draw('code25', 'image', array('text' => '012345'));
        $this->assertTrue(gettype($resource) == 'resource', 'Image must be a resource');
        $this->assertTrue(get_resource_type($resource) == 'gd', 'Image must be a GD resource');
    }

    public function testProxyBarcodeRendererDrawAsImageAutomaticallyRenderImageIfException()
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped('GD extension is required to run this test');
        }
        $resource = Barcode\Barcode::draw('code25', 'image');
        $this->assertTrue(gettype($resource) == 'resource', 'Image must be a resource');
        $this->assertTrue(get_resource_type($resource) == 'gd', 'Image must be a GD resource');
    }

    public function testProxyBarcodeRendererDrawAsPdf()
    {
        Barcode\Barcode::setBarcodeFont(__DIR__ . '/Object/_fonts/Vera.ttf');
        $resource = Barcode\Barcode::draw('code25', 'pdf', array('text' => '012345'));
        $this->assertTrue($resource instanceof Pdf\PdfDocument);
        Barcode\Barcode::setBarcodeFont('');
    }

    public function testProxyBarcodeRendererDrawAsPdfAutomaticallyRenderPdfIfException()
    {
        Barcode\Barcode::setBarcodeFont(__DIR__ . '/Object/_fonts/Vera.ttf');
        $resource = Barcode\Barcode::draw('code25', 'pdf');
        $this->assertTrue($resource instanceof Pdf\PdfDocument);
        Barcode\Barcode::setBarcodeFont('');
    }

    public function testProxyBarcodeRendererDrawAsSvg()
    {
        Barcode\Barcode::setBarcodeFont(__DIR__ . '/Object/_fonts/Vera.ttf');
        $resource = Barcode\Barcode::draw('code25', 'svg', array('text' => '012345'));
        $this->assertTrue($resource instanceof \DOMDocument);
        Barcode\Barcode::setBarcodeFont('');
    }

    public function testProxyBarcodeRendererDrawAsSvgAutomaticallyRenderSvgIfException()
    {
        Barcode\Barcode::setBarcodeFont(__DIR__ . '/Object/_fonts/Vera.ttf');
        $resource = Barcode\Barcode::draw('code25', 'svg');
        $this->assertTrue($resource instanceof \DOMDocument);
        Barcode\Barcode::setBarcodeFont('');
    }

    public function testProxyBarcodeObjectFont()
    {
        Barcode\Barcode::setBarcodeFont('my_font.ttf');
        $barcode = new Object\Code25();
        $this->assertSame('my_font.ttf', $barcode->getFont());
        Barcode\Barcode::setBarcodeFont('');
    }

    protected function checkGDRequirement()
    {
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('This test requires the GD extension');
        }
    }
}
