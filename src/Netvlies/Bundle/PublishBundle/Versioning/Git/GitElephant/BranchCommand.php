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

use GitElephant\Command\BranchCommand as BaseCommand;
use GitElephant\Repository;

class BranchCommand extends BaseCommand
{

    /**
     * @return BranchCommand
     */
    public static function getInstance(Repository $repo = null)
    {
        return new self($repo);
    }

    /**
     * Delete a branch by its name
     *
     * @param string $name The branch to delete
     *
     * @return string the command
     */
    public function forceDelete($name)
    {
        $this->clearAll();
        $this->addCommandName(self::BRANCH_COMMAND);
        $this->addCommandArgument('-D');
        $this->addCommandSubject($name);

        return $this->getCommand();
    }

}
