<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Netvlies\Bundle\PublishBundle\Controller;

use Netvlies\Bundle\PublishBundle\Entity\DomainAlias;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Entity\Target;
use Netvlies\Bundle\PublishBundle\Action\InitCommand;

use Netvlies\Bundle\PublishBundle\Form\TargetEditType;
use Netvlies\Bundle\PublishBundle\Form\TargetStep1Type;
use Netvlies\Bundle\PublishBundle\Form\TargetStep2Type;

/**
 * Class TargetController
 * @package Netvlies\Bundle\PublishBundle\Controller
 * @Route("/target")
 */
class TargetController extends Controller
{

    /**
     * Will return a list of all targets for this application
     *
     * @Route("/{application}/targetlist")
     * @Template()
     */
    public function targetsAction(Application $application)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $targets = $em->getRepository('NetvliesPublishBundle:Target')->getOrderedByDTAP($application);

        return array(
            'application' => $application,
            'targets' => $targets
        );
    }

    /**
     * @Route("/{target}")
     * @Template()
     * @param Target $target
     */
    public function detailAction(Target $target)
    {
        $em = $this->getDoctrine()->getManager();
        $logs = $em->getRepository('NetvliesPublishBundle:CommandLog')->getLogsForApplication($target->getApplication(), 50);
        $countLogs = $em->getRepository('NetvliesPublishBundle:CommandLog')->countLogsForTarget($target);
        $lastLog = $em->getRepository('NetvliesPublishBundle:CommandLog')->getLastDeployment($target);

        return array(
            'countLogs' => $countLogs,
            'lastLog' => $lastLog,
            'target' => $target,
            'logs' => $logs
        );
    }


    /**
     * @Route("/{application}/new/step1")
     * @Template()
     */
    public function createStep1Action(Application $application)
    {
        $request = $this->getRequest();

        $target = new Target();
        $target->setApplication($application);

        $formStep1 = $this->createForm(new TargetStep1Type(), $target, array());

        if($request->getMethod() == 'POST'){

            $formStep1->handleRequest($request);

            // This is still an id, because we use a choicelist in order to get an ordered list of envs by O, T, A, P
            $envId = $target->getEnvironment()->getId();

            if($formStep1->isValid()){

                $request->getSession()->set('target.env', $envId);
                $request->getSession()->set('target.user', $target->getUsername());
                $request->getSession()->save();

                return $this->redirect($this->generateUrl('netvlies_publish_target_createstep2', array('application' => $application->getId())));
            }
        }

        return array(
            'form' => $formStep1->createView(),
            'application' => $application
        );
    }


    /**
     * @Route("/{application}/target/new/step2")
     * @Template()
     */
    public function createStep2Action(Application $application)
    {
        $target = new Target();
        $request = $this->getRequest();
        $em  = $this->getDoctrine()->getManager();

        $envId = $request->getSession()->get('target.env');
        $user = $request->getSession()->get('target.user');

        /**
         * @var \Netvlies\Bundle\PublishBundle\Entity\Environment $env
         */
        $env = $em->getRepository('NetvliesPublishBundle:Environment')->findOneById($envId);
        if(!$env){
            throw new \Exception(sprintf('No such environment with id "%s"', $envId));
        }

        $target->setApplication($application);
        $target->setEnvironment($env);
        $target->setUsername($user);

        if($request->getMethod() != 'POST'){
            // Skip this part if not needed

            $homedir = '/home';

            // Init default values in target
            // @todo move this to config
            switch($env->getType()){
                case 'D':
                    $appRoot = $homedir.'/'.$target->getUsername().'/vhosts/'.$application->getKeyName();

                    $target->setApproot($appRoot);
                    $target->setPrimaryDomain($application->getKeyName().'.'.$target->getUsername().'.'.$env->getHostname());
                    break;
                case 'T':
                    if($target->getUsername()=='tester'){
                        $target->setPrimaryDomain($application->getKeyName().'.'.$env->getHostname());
                    }
                    else{
                        $target->setPrimaryDomain($application->getKeyName().'.'.$target->getUsername().'.'.$env->getHostname());
                    }
                    $appRoot = $homedir.'/'.$target->getUsername().'/vhosts/'.$application->getKeyName().'/current';
                    $target->setApproot($appRoot);
                    $target->setCaproot($homedir.'/'.$target->getUsername().'/vhosts/'.$application->getKeyName());
                    break;
                case 'A':
                    $target->setPrimaryDomain($application->getKeyName().'.a.nvsotap.nl');
                    $appRoot = $homedir.'/'.$target->getUsername().'/www/current';
                    $target->setApproot($appRoot);
                    $target->setCaproot($homedir.'/'.$target->getUsername().'/www');
                    break;
                case 'P':
                    $target->setPrimaryDomain('www.'.$application->getKeyName().'.nl');
                    $appRoot = $homedir.'/'.$target->getUsername().'/www/current';
                    $target->setApproot($appRoot);
                    $target->setCaproot($homedir.'/'.$target->getUsername().'/www');
                    break;
                default:
                    throw new \Exception('No such type (DTAP)');
            }


            // @todo put these variables in default application type
            $target->setWebroot($appRoot);
//            switch($application->getApplicationType()->getId()){
//                case 'symfony2':
//                    $target->setWebroot($appRoot.'/web');
//                    break;
//                default:
//                    $target->setWebroot($appRoot);
//                    break;
//            }

            $target->setMysqldb($application->getKeyName());
            $target->setMysqluser($application->getKeyName());

            // Just some random password
            $target->setMysqlpw(substr(str_shuffle(strtolower(sha1(rand() . time() . "my salty string"))),0, 10));
            $target->setLabel('('.$env->getType().') '.$application->getName());
        }

        $formStep2 = $this->createForm(new TargetStep2Type(), $target, array());
        $em  = $this->getDoctrine()->getManager();

        if($request->getMethod() == 'POST'){

            // Init form2
            $formStep2->handleRequest($request);

            if($formStep2->isValid()){

                foreach($target->getDomainAliases() as $alias){
                    /**
                     * @var DomainAlias $alias
                     */
                    $target->addDomainAlias($alias);
                }

                $em->persist($target);

                $em->flush($target);

                $this->get('session')->getFlashBag()->add('success', sprintf('Target %s is added', $target->getLabel()));
                return $this->redirect($this->generateUrl('netvlies_publish_target_targets', array('application' => $application->getId())));
            }
        }

        return array(
            'form' => $formStep2->createView(),
            'application' => $application
        );
    }


    /**
     * @Route("/edit/{target}")
     * @Template()
     * @param Target $target
     * @return Response
     */
    public function editAction(Target $target)
    {
        $request = $this->getRequest();
        $form = $this->createForm(new TargetEditType(), $target, array());

        $originalAliases = clone $target->getDomainAliases();

        if($request->getMethod() == 'POST'){

            $form->handleRequest($request);
            if($form->isValid()){

                $em  = $this->getDoctrine()->getManager();

                foreach ($originalAliases as $alias) {
                    if (false === $target->getDomainAliases()->contains($alias)) {
                        $em->remove($alias);
                    }
                }

                $em->persist($target);
                $em->flush($target);

                $this->get('session')->getFlashBag()->add('success', sprintf('Target %s is updated', $target->getLabel()));
                return $this->redirect($this->generateUrl('netvlies_publish_target_targets', array('application' => $target->getApplication()->getId())));
            }
        }

        return array(
            'target' => $target,
            'form' => $form->createView(),
        );
    }


    /**
     * @Route("/delete/{target}")
     */
    public function deleteAction(Target $target)
    {
        $em  = $this->getDoctrine()->getManager();
        $label = $target->getLabel();
        $app = $target->getApplication();
        $target->setInactive(true);
        $em->flush();

        $this->get('session')->getFlashBag()->add('warning', sprintf('Target %s is deleted', $label));
        return $this->redirect($this->generateUrl('netvlies_publish_target_targets', array('application' => $app->getId())));
    }
}
