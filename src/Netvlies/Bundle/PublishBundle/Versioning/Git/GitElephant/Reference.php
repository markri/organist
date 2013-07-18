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

use Netvlies\Bundle\PublishBundle\Versioning\ReferenceInterface;

class Reference implements ReferenceInterface
{

    protected $reference;

    protected $name;


    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    public function getReference()
    {
        return $this->reference;
    }

}
