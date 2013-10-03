<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Netvlies\Bundle\PublishBundle\Tests\Files;

class GitRepo
{

    /**
     * @todo we should include a tar.gz file which includes a remotely tracked repository, see comment below
     * @param $container
     */
    public static function createRepo($path)
    {
        mkdir($path);
        // Altough test, it must have a tag because branches are only allowed when tracking a remote within organist
        // A tag isnt checked for existing on remote origin, so use tag for now
        exec(sprintf('cd %s && git init && touch test && git add test && git commit -m "add test" && git tag develop && git branch develop', escapeshellarg($path)));
    }


    public static function deleteRepo($path)
    {
        exec(sprintf('rm -rf %s', escapeshellarg($path)));
    }


}