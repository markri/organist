<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Netvlies\Bundle\PublishBundle\Versioning\Git\GitElephant;

use GitElephant\Command\BaseCommand;

/**
 * FetchCommand
 */
class FetchCommand extends BaseCommand
{
    const GIT_FETCH_COMMAND = 'fetch';

    /**
     * @return FetchCommand
     */
    static public function getInstance()
    {
        return new self();
    }

    /**
     * Command to clone a repository
     *
     * @param string $url repository url
     * @param string $to  where to clone the repo
     *
     * @return string command
     */
    public function fetchOrigin()
    {
        $this->clearAll();
        $this->addCommandName(static::GIT_FETCH_COMMAND);
        $this->addCommandArgument('origin');

        return $this->getCommand();
    }
}
