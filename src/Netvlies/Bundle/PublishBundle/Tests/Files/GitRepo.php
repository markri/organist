<?php

namespace Netvlies\Bundle\PublishBundle\Tests\Files;

class GitRepo
{

    /**
     * @param $container
     */
    public static function createRepo($path)
    {
        mkdir($path);
        exec(sprintf('cd %s && git init && touch test && git add test && git commit -m "add test" && git branch develop', escapeshellarg($path)));
    }


    public static function deleteRepo($path)
    {
        exec(sprintf('rm -rf %s', escapeshellarg($path)));
    }


}