<?php

/*
 * This file is part of the FOSRestBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\RestBundle\Controller\Annotations;

use Symfony\Component\Routing\Annotation\Route as BaseRoute;

/**
 * Route annotation class.
 * @Annotation
 */
class Route extends BaseRoute
{
    public function __construct(array $data)
    {
        parent::__construct($data);
        $requirements = $this->getRequirements();
        $requirements['_method'] = $this->getMethod();
        $this->setRequirements($requirements);
    }

    public function getMethod()
    {
        return null;
    }
}
