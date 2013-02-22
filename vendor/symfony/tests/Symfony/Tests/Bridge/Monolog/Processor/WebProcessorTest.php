<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Tests\Bridge\Monolog\Processor;

use Monolog\Logger;
use Symfony\Bridge\Monolog\Processor\WebProcessor;
use Symfony\Component\HttpFoundation\Request;

class WebProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!class_exists('Monolog\\Logger')) {
            $this->markTestSkipped('Monolog is not available.');
        }
    }

    public function testUsesRequestServerData()
    {
        $server = array(
            'REQUEST_URI'    => 'A',
            'REMOTE_ADDR'    => 'B',
            'REQUEST_METHOD' => 'C',
        );

        $request = new Request();
        $request->server->replace($server);

        $processor = new WebProcessor($request);
        $record = $processor($this->getRecord());

        $this->assertEquals($server['REQUEST_URI'], $record['extra']['url']);
        $this->assertEquals($server['REMOTE_ADDR'], $record['extra']['ip']);
        $this->assertEquals($server['REQUEST_METHOD'], $record['extra']['http_method']);
    }

    /**
     * @return array Record
     */
    protected function getRecord($level = Logger::WARNING, $message = 'test')
    {
        return array(
            'message' => $message,
            'context' => array(),
            'level' => $level,
            'level_name' => Logger::getLevelName($level),
            'channel' => 'test',
            'datetime' => new \DateTime(),
            'extra' => array(),
        );
    }
}
