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

use GitElephant\Command\TagCommand as BaseCommand;
use GitElephant\Repository;

class TagCommand extends BaseCommand
{

    // git fetch --tags --prune wont work with current git version 1.7.1, we need later version, at least version 1.7.9.5 is known to work
    // for know we hack it by removing every tag and fetching them again
    const GIT_SYNCTAGS_COMMAND = 'tag | xargs -n1 git tag -d && git fetch --tags';

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
    public function syncAllTags()
    {
        $this->clearAll();
        $this->addCommandName(static::GIT_SYNCTAGS_COMMAND);

        return $this->getCommand();
    }
}
