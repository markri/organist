<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Netvlies\Bundle\PublishBundle\Versioning;


interface CommitInterface
{

    /**
     * @return string
     */
    function getMessage();

    /**
     * @return string
     */
    function getReference();

    /**
     * @return string
     */
    function getAuthor();

    /**
     * @return \DateTime
     */
    function getDateTime();
}