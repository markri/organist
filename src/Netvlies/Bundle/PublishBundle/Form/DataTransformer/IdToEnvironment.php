<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netvlies\Bundle\PublishBundle\Form\DataTransformer;

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

        $return = array();
        $return['environment']=$env->getId();

        return $return;
    }

    /**
     * @param array $array
     *
     * @return array
     * @throws UnexpectedTypeException if the given value is not an array
     */
    public function reverseTransform($choice)
    {
        if (empty($choice) || !isset($choice['environment'])) {
              return null;
        }

        $id = $choice['environment'];
        $env = $this->entityManager->getRepository('NetvliesPublishBundle:Environment')->findOneById($id);
        return $env;
    }

    
}
