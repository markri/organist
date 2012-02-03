<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netvlies\PublishBundle\Form\DataTransformer;

use Symfony\Component\Form\Util\FormUtil;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;


class IdToEnvironment implements DataTransformerInterface
{

    private $entityManager;

    public function __construct($entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * @param array $array
     *
     * @return array
     *
     * @throws UnexpectedTypeException if the given value is not an array
     */
    public function transform($env)
    {
        if (is_null($env)) {
              return null;
        }
        echo $env->getId();
        echo '<hr>';


        return array($env->getId());
    }

    /**
     * @param array $array
     *
     * @return array
     *
     * @throws UnexpectedTypeException if the given value is not an array
     */
    public function reverseTransform($id)
    {
        if (is_null($id)) {
              return null;
        }
        $env = $this->entityManager->getRepository('NetvliesPublishBundle:Environment')->findOneById($id);
        return $env;
    }

    
}
