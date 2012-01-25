<?php

namespace Netvlies\PublishBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * EnvironmentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ApplicationRepository extends EntityRepository
{

    /**
     * Get all sites
     */
    public function getAll(){
        return $this->findBy(array(), array('name' => 'ASC'));
    }
}