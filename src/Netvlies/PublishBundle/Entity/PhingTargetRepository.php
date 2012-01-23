<?php
/**
 * @author: M. de Krijger
 * Creation date: 23-12-11
 */
namespace Netvlies\PublishBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PhingTargetRepository extends EntityRepository
{

    /**
     * This will parse the build.xml file and add all non-existing phing targets to database
     *
     * @param $app
     * @return mixed
     */
    public function updatePhingTargets($app){

        $oEntityManager = $this->getEntityManager();

        if(!file_exists($app->getBuildFile())){
            return;
        }

        // Build file exists so get targets
        $targets = $this->parsePhingTargets($app);
        $targetNames = array();
        $appId = $app->getId();

        foreach($targets as $target){

            $targetNames[] = $appId.$target['name'];

            $query = $oEntityManager->createQuery('
                SELECT t FROM Netvlies\PublishBundle\Entity\PhingTarget t
                WHERE t.application = :app
                AND t.name = :name
            ');

            $query->setParameter('name', $target['name']);
            $query->setParameter('app', $app);
            $records = $query->getResult();

            if(count($records) > 0 ){
                continue;
            }

            $record = new PhingTarget();
            $record->setName($target['name']);
            $record->setApplication($app);

            $oEntityManager->persist($record);
            $oEntityManager->flush();
        }
// We cant just delete old phing targets, because they could still be coupled in deployment entity, which will give constraint error on deletion
// Further more we dont know on which branch we're on (which could have different deployment descriptors)

//        $query = $oEntityManager->createQuery('
//            SELECT t FROM Netvlies\PublishBundle\Entity\PhingTarget t
//            WHERE t.application = :app');
//
//        $query->setParameter('app', $app);
//        $phingTargets = $query->getResult();
//
//        foreach($phingTargets as $target){
//            if(in_array($appId.$target->getName(), $targetNames)){
//                continue;
//            }
//
//            $oEntityManager->remove($target);
//            $oEntityManager->flush();
//        }
    }


    /**
     * @todo later on we need to make a distinction based on different roles
     * e.g. a PM will see different targets compared to a developer, maybe we can do this
     */
    protected function parsePhingTargets($application){
        $sBuildFile = $application->getBuildFile();
		$oXML = simplexml_load_file($sBuildFile);

		$aTargets = array();

		foreach($oXML->target as $oTarget){
			if(isset($oTarget['name']) ){
				$aTargets[] = array('name'=>(string)$oTarget['name'], 'description'=>(string)$oTarget['description']);
			}
		}

		return $aTargets;
    }

}
