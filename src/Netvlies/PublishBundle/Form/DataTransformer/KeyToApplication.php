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


class KeyToApplication implements DataTransformerInterface
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
    public function reverseTransform($target)
    {
        $appKey = $target->getApplication();
        $apps = $this->entityManager->getRepository('NetvliesPublishBundle:Application')->findByName($appKey);

        if(count($apps) ==0){
            $target->setApplication(null);
        }
        else{
            $target->setApplication($apps[0]);
        }

        return $target;
    }

    
}
