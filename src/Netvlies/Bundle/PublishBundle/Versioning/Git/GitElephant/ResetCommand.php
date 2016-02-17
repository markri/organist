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
use GitElephant\Repository;

/**
 * FetchCommand
 *
 * @author Matteo Giachino <matteog@gmail.com>
 */
class ResetCommand extends BaseCommand
{
    const GIT_RESET_COMMAND = 'reset --hard';

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
    public function resetCurrentBranch($originBranch)
    {
        $this->clearAll();
        $this->addCommandName(static::GIT_RESET_COMMAND);
        $this->addCommandArgument($originBranch);

        return $this->getCommand();
    }
}
