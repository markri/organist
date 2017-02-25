<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Markri\Bundle\OrganistBundle\Versioning;

interface ReferenceInterface
{

    /**
     * @return string
     */
    function getReference();

    /**
     * @return string
     */
    function getName();

}
