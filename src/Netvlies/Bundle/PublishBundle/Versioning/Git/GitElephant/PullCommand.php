<?php
/**
 * (c) Netvlies Internetdiensten
 *
 * @author M. de Krijger <mdekrijger@netvlies.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This is extra on top of the GitElephantBundle
 */

namespace Netvlies\Bundle\PublishBundle\Versioning\Git\GitElephant;

use GitElephant\Command\BaseCommand;

/**
 * PullCommand
 *
 * @author Matteo Giachino <matteog@gmail.com>
 */
class PullCommand extends BaseCommand
{
    const GIT_PULL_COMMAND = 'pull';

    /**
     * @return PullCommand
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
    public function pullAllUpdates()
    {
        $this->clearAll();
        $this->addCommandName(static::GIT_PULL_COMMAND);
        $this->addCommandArgument('--all');

        return $this->getCommand();
    }
}
