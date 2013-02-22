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
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Controller\Router\Rewrite\Route;
use Zend\Controller\Router\Rewrite\PriorityList;
use Zend\Controller\Request\Http as HttpRequest;

/**
 * Route part
 *
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Part implements Route
{
    /**
     * Route to match
     * 
     * @var Route
     */
    protected $_route;

    /**
     * Wether the route may terminate
     *
     * @var boolean
     */
    protected $_mayTerminate;

    /**
     * Children of the route
     *
     * @var PriorityList
     */
    protected $_children;

    /**
     * __construct(): defined by Route interface
     *
     * @see    Route::__construct()
     * @param  array $options
     * @return void
     */
    public function __construct(array $options)
    {
        if (!isset($options['route']) || !$options['route'] instanceof Route) {
            throw new UnexpectedValueException('Options must contain a route');
        }

        $this->_route        = $options['route'];
        $this->_mayTerminate = (isset($options['may_terminate']) && $options['may_terminate']);
        $this->_children     = new PriorityList();
    }

    /**
     * Append a route to the part
     *
     * @param  string $name
     * @param  Route $route
     * @return Part
     */
    public function append($name, Route $route)
    {
        $this->_children[$name] = $route;

        return $this;
    }

    /**
     * match(): defined by Route interface
     *
     * @see    Route::match()
     * @param  HttpRequest $request
     * @param  integer     $pathOffset
     * @return boolean
     */
    public function match(HttpRequest $request, $pathOffset = null)
    {
        $match = $this->_route->match($request, $pathOffset);

        if ($match !== null) {
            foreach ($this->_children as $name => $route) {
                $subMatch = $route->match($match, $pathOffset);

                if ($subMatch !== null) {
                    return $match->merge($subMatch);
                }
            }

            if ($this->_mayTerminate) {
                // @todo: also check that the http request is at it's end
                return $match;
            }
        }

        return null;
    }

    /**
     * assemble(): Defined by Route interface
     *
     * @see    Route::assemble()
     * @param  array $params
     * @param  array $options
     * @return string
     */
    public function assemble(array $params = null, array $options = null)
    {
        // @todo
    }
}
