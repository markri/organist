<?php
/**
 * (c) Netvlies Internetdiensten
 *
 * @author M. de Krijger <mdekrijger@netvlies.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netvlies\Bundle\PublishBundle\Versioning;


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
