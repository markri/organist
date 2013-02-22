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
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Mail;

use stdClass,
    Zend\Mail\Address,
    Zend\Mail\AddressList,
    Zend\Mail\Header,
    Zend\Mail\Message,
    Zend\Mime\Message as MimeMessage,
    Zend\Mime\Mime,
    Zend\Mime\Part as MimePart;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Mail
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->message = new Message();
    }

    public function testInvalidByDefault()
    {
        $this->assertFalse($this->message->isValid());
    }

    public function testSetsOrigDateHeaderByDefault()
    {
        $headers = $this->message->headers();
        $this->assertInstanceOf('Zend\Mail\Headers', $headers);
        $this->assertTrue($headers->has('date'));
        $header  = $headers->get('date');
        $date    = date('r');
        $date    = substr($date, 0, 16);
        $test    = $header->getFieldValue();
        $test    = substr($test, 0, 16);
        $this->assertEquals($date, $test);
    }

    public function testAddingFromAddressMarksAsValid()
    {
        $this->message->addFrom('zf-devteam@zend.com');
        $this->assertTrue($this->message->isValid());
    }

    public function testHeadersMethodReturnsHeadersObject()
    {
        $headers = $this->message->headers();
        $this->assertInstanceOf('Zend\Mail\Headers', $headers);
    }

    public function testToMethodReturnsAddressListObject()
    {
        $this->message->addTo('zf-devteam@zend.com');
        $to = $this->message->to();
        $this->assertInstanceOf('Zend\Mail\AddressList', $to);
    }

    public function testToAddressListLivesInHeaders()
    {
        $this->message->addTo('zf-devteam@zend.com');
        $to      = $this->message->to();
        $headers = $this->message->headers();
        $this->assertInstanceOf('Zend\Mail\Headers', $headers);
        $this->assertTrue($headers->has('to'));
        $header  = $headers->get('to');
        $this->assertSame($header->getAddressList(), $to);
    }

    public function testFromMethodReturnsAddressListObject()
    {
        $this->message->addFrom('zf-devteam@zend.com');
        $from = $this->message->from();
        $this->assertInstanceOf('Zend\Mail\AddressList', $from);
    }

    public function testFromAddressListLivesInHeaders()
    {
        $this->message->addFrom('zf-devteam@zend.com');
        $from    = $this->message->from();
        $headers = $this->message->headers();
        $this->assertInstanceOf('Zend\Mail\Headers', $headers);
        $this->assertTrue($headers->has('from'));
        $header  = $headers->get('from');
        $this->assertSame($header->getAddressList(), $from);
    }

    public function testCcMethodReturnsAddressListObject()
    {
        $this->message->addCc('zf-devteam@zend.com');
        $cc = $this->message->cc();
        $this->assertInstanceOf('Zend\Mail\AddressList', $cc);
    }

    public function testCcAddressListLivesInHeaders()
    {
        $this->message->addCc('zf-devteam@zend.com');
        $cc      = $this->message->cc();
        $headers = $this->message->headers();
        $this->assertInstanceOf('Zend\Mail\Headers', $headers);
        $this->assertTrue($headers->has('cc'));
        $header  = $headers->get('cc');
        $this->assertSame($header->getAddressList(), $cc);
    }

    public function testBccMethodReturnsAddressListObject()
    {
        $this->message->addBcc('zf-devteam@zend.com');
        $bcc = $this->message->bcc();
        $this->assertInstanceOf('Zend\Mail\AddressList', $bcc);
    }

    public function testBccAddressListLivesInHeaders()
    {
        $this->message->addBcc('zf-devteam@zend.com');
        $bcc     = $this->message->bcc();
        $headers = $this->message->headers();
        $this->assertInstanceOf('Zend\Mail\Headers', $headers);
        $this->assertTrue($headers->has('bcc'));
        $header  = $headers->get('bcc');
        $this->assertSame($header->getAddressList(), $bcc);
    }

    public function testReplyToMethodReturnsAddressListObject()
    {
        $this->message->addReplyTo('zf-devteam@zend.com');
        $replyTo = $this->message->replyTo();
        $this->assertInstanceOf('Zend\Mail\AddressList', $replyTo);
    }

    public function testReplyToAddressListLivesInHeaders()
    {
        $this->message->addReplyTo('zf-devteam@zend.com');
        $replyTo = $this->message->replyTo();
        $headers = $this->message->headers();
        $this->assertInstanceOf('Zend\Mail\Headers', $headers);
        $this->assertTrue($headers->has('reply-to'));
        $header  = $headers->get('reply-to');
        $this->assertSame($header->getAddressList(), $replyTo);
    }

    public function testSenderIsNullByDefault()
    {
        $this->assertNull($this->message->getSender());
    }

    public function testSettingSenderCreatesAddressObject()
    {
        $this->message->setSender('zf-devteam@zend.com');
        $sender = $this->message->getSender();
        $this->assertInstanceOf('Zend\Mail\Address', $sender);
    }

    public function testCanSpecifyNameWhenSettingSender()
    {
        $this->message->setSender('zf-devteam@zend.com', 'ZF DevTeam');
        $sender = $this->message->getSender();
        $this->assertInstanceOf('Zend\Mail\Address', $sender);
        $this->assertEquals('ZF DevTeam', $sender->getName());
    }

    public function testCanProvideAddressObjectWhenSettingSender()
    {
        $sender = new Address('zf-devteam@zend.com');
        $this->message->setSender($sender);
        $test = $this->message->getSender();
        $this->assertSame($sender, $test);
    }

    public function testSenderAccessorsProxyToSenderHeader()
    {
        $header = new Header\Sender();
        $this->message->headers()->addHeader($header);
        $address = new Address('zf-devteam@zend.com', 'ZF DevTeam');
        $this->message->setSender($address);
        $this->assertSame($address, $header->getAddress());
    }

    public function testCanAddFromAddressUsingName()
    {
        $this->message->addFrom('zf-devteam@zend.com', 'ZF DevTeam');
        $addresses = $this->message->from();
        $this->assertEquals(1, count($addresses));
        $address = $addresses->current();
        $this->assertEquals('zf-devteam@zend.com', $address->getEmail());
        $this->assertEquals('ZF DevTeam', $address->getName());
    }

    public function testCanAddFromAddressUsingAddressObject()
    {
        $address = new Address('zf-devteam@zend.com', 'ZF DevTeam');
        $this->message->addFrom($address);

        $addresses = $this->message->from();
        $this->assertEquals(1, count($addresses));
        $test = $addresses->current();
        $this->assertSame($address, $test);
    }

    public function testCanAddManyFromAddressesUsingArray()
    {
        $addresses = array(
            'zf-devteam@zend.com',
            'zf-contributors@lists.zend.com' => 'ZF Contributors List',
            new Address('fw-announce@lists.zend.com', 'ZF Announce List'),
        );
        $this->message->addFrom($addresses);

        $from = $this->message->from();
        $this->assertEquals(3, count($from));

        $this->assertTrue($from->has('zf-devteam@zend.com'));
        $this->assertTrue($from->has('zf-contributors@lists.zend.com'));
        $this->assertTrue($from->has('fw-announce@lists.zend.com'));
    }

    public function testCanAddManyFromAddressesUsingAddressListObject()
    {
        $list = new AddressList();
        $list->add('zf-devteam@zend.com');

        $this->message->addFrom('fw-announce@lists.zend.com');
        $this->message->addFrom($list);
        $from = $this->message->from();
        $this->assertEquals(2, count($from));
        $this->assertTrue($from->has('fw-announce@lists.zend.com'));
        $this->assertTrue($from->has('zf-devteam@zend.com'));
    }

    public function testCanSetFromListFromAddressList()
    {
        $list = new AddressList();
        $list->add('zf-devteam@zend.com');

        $this->message->addFrom('fw-announce@lists.zend.com');
        $this->message->setFrom($list);
        $from = $this->message->from();
        $this->assertEquals(1, count($from));
        $this->assertFalse($from->has('fw-announce@lists.zend.com'));
        $this->assertTrue($from->has('zf-devteam@zend.com'));
    }

    public function testCanAddCcAddressUsingName()
    {
        $this->message->addCc('zf-devteam@zend.com', 'ZF DevTeam');
        $addresses = $this->message->cc();
        $this->assertEquals(1, count($addresses));
        $address = $addresses->current();
        $this->assertEquals('zf-devteam@zend.com', $address->getEmail());
        $this->assertEquals('ZF DevTeam', $address->getName());
    }

    public function testCanAddCcAddressUsingAddressObject()
    {
        $address = new Address('zf-devteam@zend.com', 'ZF DevTeam');
        $this->message->addCc($address);

        $addresses = $this->message->cc();
        $this->assertEquals(1, count($addresses));
        $test = $addresses->current();
        $this->assertSame($address, $test);
    }

    public function testCanAddManyCcAddressesUsingArray()
    {
        $addresses = array(
            'zf-devteam@zend.com',
            'zf-contributors@lists.zend.com' => 'ZF Contributors List',
            new Address('fw-announce@lists.zend.com', 'ZF Announce List'),
        );
        $this->message->addCc($addresses);

        $cc = $this->message->cc();
        $this->assertEquals(3, count($cc));

        $this->assertTrue($cc->has('zf-devteam@zend.com'));
        $this->assertTrue($cc->has('zf-contributors@lists.zend.com'));
        $this->assertTrue($cc->has('fw-announce@lists.zend.com'));
    }

    public function testCanAddManyCcAddressesUsingAddressListObject()
    {
        $list = new AddressList();
        $list->add('zf-devteam@zend.com');

        $this->message->addCc('fw-announce@lists.zend.com');
        $this->message->addCc($list);
        $cc = $this->message->cc();
        $this->assertEquals(2, count($cc));
        $this->assertTrue($cc->has('fw-announce@lists.zend.com'));
        $this->assertTrue($cc->has('zf-devteam@zend.com'));
    }

    public function testCanSetCcListFromAddressList()
    {
        $list = new AddressList();
        $list->add('zf-devteam@zend.com');

        $this->message->addCc('fw-announce@lists.zend.com');
        $this->message->setCc($list);
        $cc = $this->message->cc();
        $this->assertEquals(1, count($cc));
        $this->assertFalse($cc->has('fw-announce@lists.zend.com'));
        $this->assertTrue($cc->has('zf-devteam@zend.com'));
    }

    public function testCanAddBccAddressUsingName()
    {
        $this->message->addBcc('zf-devteam@zend.com', 'ZF DevTeam');
        $addresses = $this->message->bcc();
        $this->assertEquals(1, count($addresses));
        $address = $addresses->current();
        $this->assertEquals('zf-devteam@zend.com', $address->getEmail());
        $this->assertEquals('ZF DevTeam', $address->getName());
    }

    public function testCanAddBccAddressUsingAddressObject()
    {
        $address = new Address('zf-devteam@zend.com', 'ZF DevTeam');
        $this->message->addBcc($address);

        $addresses = $this->message->bcc();
        $this->assertEquals(1, count($addresses));
        $test = $addresses->current();
        $this->assertSame($address, $test);
    }

    public function testCanAddManyBccAddressesUsingArray()
    {
        $addresses = array(
            'zf-devteam@zend.com',
            'zf-contributors@lists.zend.com' => 'ZF Contributors List',
            new Address('fw-announce@lists.zend.com', 'ZF Announce List'),
        );
        $this->message->addBcc($addresses);

        $bcc = $this->message->bcc();
        $this->assertEquals(3, count($bcc));

        $this->assertTrue($bcc->has('zf-devteam@zend.com'));
        $this->assertTrue($bcc->has('zf-contributors@lists.zend.com'));
        $this->assertTrue($bcc->has('fw-announce@lists.zend.com'));
    }

    public function testCanAddManyBccAddressesUsingAddressListObject()
    {
        $list = new AddressList();
        $list->add('zf-devteam@zend.com');

        $this->message->addBcc('fw-announce@lists.zend.com');
        $this->message->addBcc($list);
        $bcc = $this->message->bcc();
        $this->assertEquals(2, count($bcc));
        $this->assertTrue($bcc->has('fw-announce@lists.zend.com'));
        $this->assertTrue($bcc->has('zf-devteam@zend.com'));
    }

    public function testCanSetBccListFromAddressList()
    {
        $list = new AddressList();
        $list->add('zf-devteam@zend.com');

        $this->message->addBcc('fw-announce@lists.zend.com');
        $this->message->setBcc($list);
        $bcc = $this->message->bcc();
        $this->assertEquals(1, count($bcc));
        $this->assertFalse($bcc->has('fw-announce@lists.zend.com'));
        $this->assertTrue($bcc->has('zf-devteam@zend.com'));
    }

    public function testCanAddReplyToAddressUsingName()
    {
        $this->message->addReplyTo('zf-devteam@zend.com', 'ZF DevTeam');
        $addresses = $this->message->replyTo();
        $this->assertEquals(1, count($addresses));
        $address = $addresses->current();
        $this->assertEquals('zf-devteam@zend.com', $address->getEmail());
        $this->assertEquals('ZF DevTeam', $address->getName());
    }

    public function testCanAddReplyToAddressUsingAddressObject()
    {
        $address = new Address('zf-devteam@zend.com', 'ZF DevTeam');
        $this->message->addReplyTo($address);

        $addresses = $this->message->replyTo();
        $this->assertEquals(1, count($addresses));
        $test = $addresses->current();
        $this->assertSame($address, $test);
    }

    public function testCanAddManyReplyToAddressesUsingArray()
    {
        $addresses = array(
            'zf-devteam@zend.com',
            'zf-contributors@lists.zend.com' => 'ZF Contributors List',
            new Address('fw-announce@lists.zend.com', 'ZF Announce List'),
        );
        $this->message->addReplyTo($addresses);

        $replyTo = $this->message->replyTo();
        $this->assertEquals(3, count($replyTo));

        $this->assertTrue($replyTo->has('zf-devteam@zend.com'));
        $this->assertTrue($replyTo->has('zf-contributors@lists.zend.com'));
        $this->assertTrue($replyTo->has('fw-announce@lists.zend.com'));
    }

    public function testCanAddManyReplyToAddressesUsingAddressListObject()
    {
        $list = new AddressList();
        $list->add('zf-devteam@zend.com');

        $this->message->addReplyTo('fw-announce@lists.zend.com');
        $this->message->addReplyTo($list);
        $replyTo = $this->message->replyTo();
        $this->assertEquals(2, count($replyTo));
        $this->assertTrue($replyTo->has('fw-announce@lists.zend.com'));
        $this->assertTrue($replyTo->has('zf-devteam@zend.com'));
    }

    public function testCanSetReplyToListFromAddressList()
    {
        $list = new AddressList();
        $list->add('zf-devteam@zend.com');

        $this->message->addReplyTo('fw-announce@lists.zend.com');
        $this->message->setReplyTo($list);
        $replyTo = $this->message->replyTo();
        $this->assertEquals(1, count($replyTo));
        $this->assertFalse($replyTo->has('fw-announce@lists.zend.com'));
        $this->assertTrue($replyTo->has('zf-devteam@zend.com'));
    }

    public function testSubjectIsEmptyByDefault()
    {
        $this->assertNull($this->message->getSubject());
    }

    public function testSubjectIsMutable()
    {
        $this->message->setSubject('test subject');
        $subject = $this->message->getSubject();
        $this->assertEquals('test subject', $subject);
    }

    public function testSettingSubjectProxiesToHeader()
    {
        $this->message->setSubject('test subject');
        $headers = $this->message->headers();
        $this->assertInstanceOf('Zend\Mail\Headers', $headers);
        $this->assertTrue($headers->has('subject'));
        $header = $headers->get('subject');
        $this->assertEquals('test subject', $header->getFieldValue());
    }

    public function testBodyIsEmptyByDefault()
    {
        $this->assertNull($this->message->getBody());
    }

    public function testMaySetBodyFromString()
    {
        $this->message->setBody('body');
        $this->assertEquals('body', $this->message->getBody());
    }

    public function testMaySetBodyFromStringSerializableObject()
    {
        $object = new TestAsset\StringSerializableObject('body');
        $this->message->setBody($object);
        $this->assertSame($object, $this->message->getBody());
        $this->assertEquals('body', $this->message->getBodyText());
    }

    public function testMaySetBodyFromMimeMessage()
    {
        $body = new MimeMessage();
        $this->message->setBody($body);
        $this->assertSame($body, $this->message->getBody());
    }

    public function testMaySetNullBody()
    {
        $this->message->setBody(null);
        $this->assertNull($this->message->getBody());
    }

    public static function invalidBodyValues()
    {
        return array(
            array(array('foo')),
            array(true),
            array(false),
            array(new stdClass),
        );
    }

    /**
     * @dataProvider invalidBodyValues
     */
    public function testSettingNonScalarNonMimeNonStringSerializableValueForBodyRaisesException($body)
    {
        $this->setExpectedException('Zend\Mail\Exception\InvalidArgumentException');
        $this->message->setBody($body);
    }

    public function testSettingBodyFromSinglePartMimeMessageSetsAppropriateHeaders()
    {
        $mime = new Mime('foo-bar');
        $part = new MimePart('<b>foo</b>');
        $part->type = 'text/html';
        $body = new MimeMessage();
        $body->setMime($mime);
        $body->addPart($part);

        $this->message->setBody($body);
        $headers = $this->message->headers();
        $this->assertInstanceOf('Zend\Mail\Headers', $headers);

        $this->assertTrue($headers->has('mime-version'));
        $header = $headers->get('mime-version');
        $this->assertEquals('1.0', $header->getFieldValue());

        $this->assertTrue($headers->has('content-type'));
        $header = $headers->get('content-type');
        $this->assertEquals('text/html', $header->getFieldValue());
    }

    public function testSettingBodyFromMultiPartMimeMessageSetsAppropriateHeaders()
    {
        $mime = new Mime('foo-bar');
        $text = new MimePart('foo');
        $text->type = 'text/plain';
        $html = new MimePart('<b>foo</b>');
        $html->type = 'text/html';
        $body = new MimeMessage();
        $body->setMime($mime);
        $body->addPart($text);
        $body->addPart($html);

        $this->message->setBody($body);
        $headers = $this->message->headers();
        $this->assertInstanceOf('Zend\Mail\Headers', $headers);

        $this->assertTrue($headers->has('mime-version'));
        $header = $headers->get('mime-version');
        $this->assertEquals('1.0', $header->getFieldValue());

        $this->assertTrue($headers->has('content-type'));
        $header = $headers->get('content-type');
        $this->assertEquals("multipart/mixed;\r\n boundary=\"foo-bar\"", $header->getFieldValue());
    }

    public function testRetrievingBodyTextFromMessageWithMultiPartMimeBodyReturnsMimeSerialization()
    {
        $mime = new Mime('foo-bar');
        $text = new MimePart('foo');
        $text->type = 'text/plain';
        $html = new MimePart('<b>foo</b>');
        $html->type = 'text/html';
        $body = new MimeMessage();
        $body->setMime($mime);
        $body->addPart($text);
        $body->addPart($html);

        $this->message->setBody($body);

        $text = $this->message->getBodyText();
        $this->assertEquals($body->generateMessage(), $text);
        $this->assertContains('--foo-bar', $text);
        $this->assertContains('--foo-bar--', $text);
        $this->assertContains('Content-Type: text/plain', $text);
        $this->assertContains('Content-Type: text/html', $text);
    }

    public function testEncodingIsAsciiByDefault()
    {
        $this->assertEquals('ASCII', $this->message->getEncoding());
    }

    public function testEncodingIsMutable()
    {
        $this->message->setEncoding('UTF-8');
        $this->assertEquals('UTF-8', $this->message->getEncoding());
    }

    public function testSettingNonAsciiEncodingForcesMimeEncodingOfSomeHeaders()
    {
        if (!function_exists('iconv_mime_encode')) {
            $this->markTestSkipped('Encoding relies on iconv extension');
        }

        $this->message->addTo('zf-devteam@zend.com', 'ZF DevTeam');
        $this->message->addFrom('matthew@zend.com', "Matthew Weier O'Phinney");
        $this->message->addCc('zf-contributors@lists.zend.com', 'ZF Contributors List');
        $this->message->addBcc('zf-crteam@lists.zend.com', 'ZF CR Team');
        $this->message->setSubject('This is a subject');
        $this->message->setEncoding('UTF-8');

        $test = $this->message->headers()->toString();

        $expected = $this->encodeString('ZF DevTeam', 'UTF-8');
        $this->assertContains($expected, $test);
        $this->assertContains('<zf-devteam@zend.com>', $test);

        $expected = $this->encodeString("Matthew Weier O'Phinney", 'UTF-8');
        $this->assertContains($expected, $test, $test);
        $this->assertContains('<matthew@zend.com>', $test);

        $expected = $this->encodeString("ZF Contributors List", 'UTF-8');
        $this->assertContains($expected, $test);
        $this->assertContains('<zf-contributors@lists.zend.com>', $test);

        $expected = $this->encodeString("ZF CR Team", 'UTF-8');
        $this->assertContains($expected, $test);
        $this->assertContains('<zf-crteam@lists.zend.com>', $test);

        $self     = $this;
        $words    = array_map(function($word) use ($self) {
            return $self->encodeString($word, 'UTF-8');
        }, explode(' ', 'This is a subject'));
        $expected = 'Subject: ' . implode("\r\n ", $words);
        $this->assertContains($expected, $test, $test);
    }

    public function encodeString($string, $charset)
    {
        $encoded = iconv_mime_encode('Header', $string, array(
            'scheme'         => 'Q',
            'output-charset' => $charset,
            'line-length'    => 998,
        ));
        $encoded = str_replace('Header: ', '', $encoded);
        return $encoded;
    }
}
