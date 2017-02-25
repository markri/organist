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

use GitElephant\Command\LogCommand as BaseCommand;
use GitElephant\Objects\Commit as BaseCommit;
use \GitElephant\Repository;

class LogCommand extends BaseCommand
{


    public static function getInstance(Repository $repo = null)
    {
        return new self($repo);
    }

    /**
     * Build a generic log command
     *
     * @param \GitElephant\Objects\TreeishInterface|string $ref    the reference to build the log for
     * @param string|null                                  $path   the physical path to the tree relative to the repository root
     * @param int|null                                     $limit  limit to n entries
     * @param int|null                                     $offset skip n entries
     *
     * @return string
     */
    public function showAllLog($ref, $path = null, $limit = null, $offset = null)
    {
        $this->clearAll();

        $this->addCommandName(self::GIT_LOG);
        $this->addCommandArgument('-s');
        $this->addCommandArgument('--all');
        $this->addCommandArgument('--pretty=raw');
        $this->addCommandArgument('--no-color');

        if (null !== $limit) {
            $limit = (int) $limit;
            $this->addCommandArgument('--max-count=' . $limit);
        }

        if (null !== $offset) {
            $offset = (int) $offset;
            $this->addCommandArgument('--skip=' . $offset);
        }

        if ($ref instanceof TreeishInterface) {
            $ref = $ref->getSha();
        }

        if (null !== $path && !empty($path)) {
            $this->addPath($path);
        }

        $this->addCommandSubject($ref);

        return $this->getCommand();
    }


    /**
     * Simplified version for the needed command
     * @param $from
     * @param $to
     * @return string
     */
    public function getCommitMessagesBetween($from, $to)
    {
        $this->clearAll();

        $this->addCommandName(self::GIT_LOG);
        $this->addCommandArgument('--pretty=raw');
        $this->addCommandArgument('--no-color');
        $this->addCommandSubject(sprintf('%s..%s', $from, $to));

        return $this->getCommand();
    }

}
