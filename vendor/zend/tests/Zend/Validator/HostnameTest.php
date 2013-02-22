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
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Validator;
use Zend\Validator\Hostname,
    ReflectionClass;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class HostnameTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Default instance created for all test methods
     *
     * @var Zend_Validator_Hostname
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validator_Hostname object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_origEncoding = iconv_get_encoding('internal_encoding');
        $this->_validator = new Hostname();
    }

    /**
     * Reset iconv
     */
    public function tearDown()
    {
        iconv_set_encoding('internal_encoding', $this->_origEncoding);
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            array(Hostname::ALLOW_IP, true, array('1.2.3.4', '10.0.0.1', '255.255.255.255')),
            array(Hostname::ALLOW_IP, false, array('1.2.3.4.5', '0.0.0.256')),
            array(Hostname::ALLOW_DNS, true, array('example.com', 'example.museum', 'd.hatena.ne.jp')),
            array(Hostname::ALLOW_DNS, false, array('localhost', 'localhost.localdomain', '1.2.3.4', 'domain.invalid')),
            array(Hostname::ALLOW_LOCAL, true, array('localhost', 'localhost.localdomain', 'example.com')),
            array(Hostname::ALLOW_ALL, true, array('localhost', 'example.com', '1.2.3.4')),
            array(Hostname::ALLOW_LOCAL, false, array('local host', 'example,com', 'exam_ple.com'))
        );
        foreach ($valuesExpected as $element) {
            $validator = new Hostname($element[0]);
            foreach ($element[2] as $input) {
                $this->assertEquals($element[1], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }
    }

    public function testCombination()
    {
        $valuesExpected = array(
            array(Hostname::ALLOW_DNS | Hostname::ALLOW_LOCAL, true, array('domain.com', 'localhost', 'local.localhost')),
            array(Hostname::ALLOW_DNS | Hostname::ALLOW_LOCAL, false, array('1.2.3.4', '255.255.255.255')),
            array(Hostname::ALLOW_DNS | Hostname::ALLOW_IP, true, array('1.2.3.4', '255.255.255.255')),
            array(Hostname::ALLOW_DNS | Hostname::ALLOW_IP, false, array('localhost', 'local.localhost'))
            );
        foreach ($valuesExpected as $element) {
            $validator = new Hostname($element[0]);
            foreach ($element[2] as $input) {
                $this->assertEquals($element[1], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }
    }

    /**
     * Ensure the dash character tests work as expected
     *
     */
    public function testDashes()
    {
        $valuesExpected = array(
            array(Hostname::ALLOW_DNS, true, array('domain.com', 'doma-in.com')),
            array(Hostname::ALLOW_DNS, false, array('-domain.com', 'domain-.com', 'do--main.com'))
            );
        foreach ($valuesExpected as $element) {
            $validator = new Hostname($element[0]);
            foreach ($element[2] as $input) {
                $this->assertEquals($element[1], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals(array(), $this->_validator->getMessages());
    }

    /**
     * Ensure the IDN check works as expected
     *
     */
    public function testIDN()
    {
        $validator = new Hostname();

        // Check IDN matching
        $valuesExpected = array(
            array(true, array('bürger.de', 'hãllo.de', 'hållo.se')),
            array(true, array('bÜrger.de', 'hÃllo.de', 'hÅllo.se')),
            array(false, array('hãllo.se', 'bürger.lt', 'hãllo.uk'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }

        // Check no IDN matching
        $validator->useIdnCheck(false);
        $valuesExpected = array(
            array(false, array('bürger.de', 'hãllo.de', 'hållo.se'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }

        // Check setting no IDN matching via constructor
        unset($validator);
        $validator = new Hostname(Hostname::ALLOW_DNS, false);
        $valuesExpected = array(
            array(false, array('bürger.de', 'hãllo.de', 'hållo.se'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }
    }

    /**
     * Ensure the IDN check works on ressource files as expected
     *
     */
    public function testRessourceIDN()
    {
        $validator = new Hostname();

        // Check IDN matching
        $valuesExpected = array(
            array(true, array('bürger.com', 'hãllo.com', 'hållo.com')),
            array(true, array('bÜrger.com', 'hÃllo.com', 'hÅllo.com')),
            array(false, array('hãllo.lt', 'bürger.lt', 'hãllo.lt'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }

        // Check no IDN matching
        $validator->useIdnCheck(false);
        $valuesExpected = array(
            array(false, array('bürger.com', 'hãllo.com', 'hållo.com'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }

        // Check setting no IDN matching via constructor
        unset($validator);
        $validator = new Hostname(Hostname::ALLOW_DNS, false);
        $valuesExpected = array(
            array(false, array('bürger.com', 'hãllo.com', 'hållo.com'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }
    }

    /**
     * Ensure the TLD check works as expected
     *
     */
    public function testTLD()
    {
        $validator = new Hostname();

        // Check TLD matching
        $valuesExpected = array(
            array(true, array('domain.co.uk', 'domain.uk.com', 'domain.tl', 'domain.zw')),
            array(false, array('domain.xx', 'domain.zz', 'domain.madeup'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }

        // Check no TLD matching
        $validator->useTldCheck(false);
        $valuesExpected = array(
            array(true, array('domain.xx', 'domain.zz', 'domain.madeup'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }

        // Check setting no TLD matching via constructor
        unset($validator);
        $validator = new Hostname(Hostname::ALLOW_DNS, true, false);
        $valuesExpected = array(
            array(true, array('domain.xx', 'domain.zz', 'domain.madeup'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }
    }

    /**
     * Ensures that getAllow() returns expected default value
     *
     * @return void
     */
    public function testGetAllow()
    {
        $this->assertEquals(Hostname::ALLOW_DNS, $this->_validator->getAllow());
    }

    /**
     * Test changed with ZF-6676, as IP check is only involved when IP patterns match
     *
     * @group ZF-2861
     * @group ZF-6676
     */
    public function testValidatorMessagesShouldBeTranslated()
    {
        $translations = array(
            'hostnameInvalidLocalName' => 'this is the IP error message',
        );
        $translator = new \Zend\Translator\Translator('ArrayAdapter', $translations);
        $this->_validator->setTranslator($translator);

        $this->_validator->isValid('0.239,512.777');
        $messages = $this->_validator->getMessages();
        $found = false;
        foreach ($messages as $code => $message) {
            if (array_key_exists($code, $translations)) {
                $found = true;
                break;
            }
        }

        $this->assertTrue($found);
        $this->assertEquals($translations[$code], $message);
    }

    /**
     * @group ZF-6033
     */
    public function testNumberNames()
    {
        $validator = new Hostname();

        // Check TLD matching
        $valuesExpected = array(
            array(true, array('www.danger1.com', 'danger.com', 'www.danger.com')),
            array(false, array('www.danger1com', 'dangercom', 'www.dangercom'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }
    }

    /**
     * @group ZF-6133
     */
    public function testPunycodeDecoding()
    {
        $validator = new Hostname();

        // Check TLD matching
        $valuesExpected = array(
            array(true, array('xn--brger-kva.com')),
            array(false, array('xn--brger-x45d2va.com', 'xn--bürger.com', 'xn--'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->_validator->isValid(array(1 => 1)));
    }

    /**
     * @ZF-7323
     */
    public function testLatinSpecialChars()
    {
        $this->assertFalse($this->_validator->isValid('place@yah&oo.com'));
        $this->assertFalse($this->_validator->isValid('place@y*ahoo.com'));
        $this->assertFalse($this->_validator->isValid('ya#hoo'));
    }

    /**
     * @group ZF-7277
     */
    public function testDifferentIconvEncoding()
    {
        iconv_set_encoding('internal_encoding', 'ISO8859-1');
        $validator = new Hostname();

        $valuesExpected = array(
            array(true, array('bürger.com', 'hãllo.com', 'hållo.com')),
            array(true, array('bÜrger.com', 'hÃllo.com', 'hÅllo.com')),
            array(false, array('hãllo.lt', 'bürger.lt', 'hãllo.lt'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }
    }

    /**
     * @ZF-8312
     */
    public function testInvalidDoubledIdn()
    {
        $this->assertFalse($this->_validator->isValid('test.com / http://www.test.com'));
    }

    /**
     * @group ZF-10267
     */
    public function testURI()
    {
        $valuesExpected = array(
            array(Hostname::ALLOW_URI, true, array('localhost', 'example.com', '~ex%20ample')),
            array(Hostname::ALLOW_URI, false, array('§bad', 'don?t.know', 'thisisaverylonghostnamewhichextendstwohundredfiftysixcharactersandthereforshouldnotbeallowedbythisvalidatorbecauserfc3986limitstheallowedcharacterstoalimitoftwohunderedfiftysixcharactersinsumbutifthistestwouldfailthenitshouldreturntruewhichthrowsanexceptionbytheunittest')),
        );
        foreach ($valuesExpected as $element) {
            $validator = new Hostname($element[0]);
            foreach ($element[2] as $input) {
                $this->assertEquals($element[1], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }
    }

    /**
     * Ensure that a trailing "." in a local hostname is permitted
     *
     * @group ZF-6363
     */
    public function testTrailingDot()
    {
        $valuesExpected = array(
            array(Hostname::ALLOW_ALL, true, array('example.', 'example.com.', '~ex%20ample.')),
            array(Hostname::ALLOW_ALL, false, array('example..')),
            array(Hostname::ALLOW_ALL, true, array('1.2.3.4.')),
            array(Hostname::ALLOW_DNS, false, array('example..', '~ex%20ample..')),
            array(Hostname::ALLOW_LOCAL, true, array('example.', 'example.com.')),
        );

        foreach ($valuesExpected as $element) {
            $validator = new Hostname($element[0]);
            foreach ($element[2] as $input) {
                $this->assertEquals($element[1], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }
    }

    /**
     * @group ZF-11334
     */
    public function testSupportsIpv6AddressesWhichContainHexDigitF()
    {
        $validator = new Hostname(Hostname::ALLOW_ALL);

        $this->assertTrue($validator->isValid('FEDC:BA98:7654:3210:FEDC:BA98:7654:3210'));
        $this->assertTrue($validator->isValid('1080:0:0:0:8:800:200C:417A'));
        $this->assertTrue($validator->isValid('3ffe:2a00:100:7031::1'));
        $this->assertTrue($validator->isValid('1080::8:800:200C:417A'));
        $this->assertTrue($validator->isValid('::192.9.5.5'));
        $this->assertTrue($validator->isValid('::FFFF:129.144.52.38'));
        $this->assertTrue($validator->isValid('2010:836B:4179::836B:4179'));
    }

    /**
     * Test extended greek charset
     *
     * @group ZF-11751
     */
    public function testExtendedGreek()
    {
        $validator = new Hostname(Hostname::ALLOW_ALL);
        $this->assertEquals(true, $validator->isValid('ῆὧὰῧῲ.com'));
    }

    /**
     * @group ZF-11796
     */
    public function testIDNSI()
    {
        $validator = new Hostname(Hostname::ALLOW_ALL);

        $this->assertTrue($validator->isValid('Test123.si'));
        $this->assertTrue($validator->isValid('țest123.si'));
        $this->assertTrue($validator->isValid('tĕst123.si'));
        $this->assertTrue($validator->isValid('tàrø.si'));
        $this->assertFalse($validator->isValid('رات.si'));
    }
    
    public function testEqualsMessageTemplates()
    {
        $validator = $this->_validator;
        $reflection = new ReflectionClass($validator);
        
        if(!$reflection->hasProperty('_messageTemplates')) {
            return;
        }
        
        $property = $reflection->getProperty('_messageTemplates');
        $property->setAccessible(true);

        $this->assertEquals(
            $property->getValue($validator),
            $validator->getOption('messageTemplates')
        );
    }
    
    public function testEqualsMessageVariables()
    {
        $validator = $this->_validator;
        $reflection = new ReflectionClass($validator);
        
        if(!$reflection->hasProperty('_messageVariables')) {
            return;
        }
        
        $property = $reflection->getProperty('_messageVariables');
        $property->setAccessible(true);

        $this->assertEquals(
            $property->getValue($validator),
            $validator->getOption('messageVariables')
        );
    }
}
