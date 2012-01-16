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


class KeyToEnvironment implements DataTransformerInterface
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
    public function transform($key)
    {
        return $key;
    }

    /**
     * @param array $array
     *
     * @return array
     *
     * @throws UnexpectedTypeException if the given value is not an array
     */
    public function reverseTransform($deployment)
    {
        $envKey = $deployment->getEnvironment();
        $envs = $this->entityManager->getRepository('NetvliesPublishBundle:Environment')->findByKeyname($envKey);

        if(count($envs) ==0){
            $deployment->setEnvironment(null);
        }
        else{
            $deployment->setEnvironment($envs[0]);
        }

        return $deployment;
    }

    
}
