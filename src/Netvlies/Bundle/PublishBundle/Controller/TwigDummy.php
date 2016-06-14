<?php
/**
 * Created by PhpStorm.
 * User: mdekrijger
 * Date: 14-6-16
 * Time: 22:28
 */

namespace Netvlies\Bundle\PublishBundle\Controller;


class TwigDummy
{
     public function __get($name)
     {
         return 'dummy';
     }

    public function __call($a, $b){

        return new TwigDummy();
    }


    public function __toString()
    {
        return '';
    }

}