<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Markri\Bundle\OrganistBundle\Versioning\Git\GitElephant;

use GitElephant\Command\BaseCommand;
use GitElephant\Repository;

/**
 * FetchCommand
 */
class FetchCommand extends BaseCommand
{
    const GIT_FETCH_COMMAND = 'fetch';

    /**
     * @return FetchCommand
     */
    public static function getInstance(Repository $repo = null)
    {
        return new self($repo);
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
