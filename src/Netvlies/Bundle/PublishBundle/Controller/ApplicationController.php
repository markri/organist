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

use Netvlies\Bundle\PublishBundle\ApplicationType\ApplicationType;
use Netvlies\Bundle\PublishBundle\Strategy\Commands\CheckoutCommand;
use Netvlies\Bundle\PublishBundle\Entity\UserFile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Netvlies\Bundle\PublishBundle\Entity\CommandLogRepository;
use Netvlies\Bundle\PublishBundle\Entity\Application;

use Netvlies\Bundle\PublishBundle\Form\ApplicationCreateType;
use Netvlies\Bundle\PublishBundle\Form\ApplicationEditType;

/**
 * Class ApplicationController
 * @package Netvlies\Bundle\PublishBundle\Controller
 * @Route("/application")
 */
class ApplicationController extends Controller
{

    /**
     * @Route("/create")
     * @Template()
     */
    public function createAction()
    {
        $request = $this->getRequest();
        $application = new Application();

        $form = $this->createForm(
            new ApplicationCreateType(),
            $application
        );

        if($request->getMethod()=='POST'){
            $form->handleRequest($request);

            if($form->isValid()){
                $em = $this->container->get('doctrine.orm.entity_manager');

                /**
                 * @var ApplicationType $appTypeService
                 */
                $appTypeService = $this->container->get($application->getApplicationType());

                if ($userFiles = $appTypeService->getUserfiles()) {
                    foreach($userFiles as $sharedFile){
                        $userFile = new UserFile();
                        $userFile->setApplication($application);
                        $userFile->setPath($sharedFile);
                        $userFile->setType('F');
                        $application->addUserFile($userFile);
                    }
                }
                if($userDirs = $appTypeService->getUserdirs()){
                    foreach($userDirs as $sharedDir){
                        $userFile = new UserFile();
                        $userFile->setApplication($application);
                        $userFile->setPath($sharedDir);
                        $userFile->setType('D');
                        $application->addUserFile($userFile);
                    }
                }

                $em->persist($application);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success', sprintf('Application %s is succesfully created', $application->getName()));
                return $this->redirect($this->generateUrl('netvlies_publish_application_dashboard', array('application' => $application->getId())));
            }
        }

        return array(
            'form' => $form->createView()
        );
    }


    /**
     * Dashboard view
     *
     * @Route("/{application}/dashboard")
     * @Template()
     * @param Application $application
     * @return array
     */
    public function dashboardAction(Application $application)
    {
        /**
         * @var CommandLogRepository
         */
        $logRepo = $this->getDoctrine()->getManager()->getRepository('NetvliesPublishBundle:CommandLog');
        $logs = $logRepo->getLogsForApplication($application, 5);

        return array(
            'logs' => $logs,
            'application' => $application
        );
    }



    /**
     * @Route("/{application}/settings")
     * @Template()
     * @param Application $application
     * @return array
     */
    public function editAction(Application $application)
    {
        $form = $this->createForm(new ApplicationEditType(), $application);
        $request = $this->getRequest();

        $originalUserFiles = clone $application->getUserFiles();

        if($request->getMethod() == 'POST'){

            $form->handleRequest($request);

            if($form->isValid()){
                $em = $this->container->get('doctrine.orm.entity_manager');

                foreach($application->getUserFiles() as $userFile){
                    foreach($originalUserFiles as $key=>$origUserFile){
                        if($userFile->getId() == $origUserFile->getId()){
                            $originalUserFiles->remove($key);
                        }
                    }
                }

                foreach($originalUserFiles as $deleteUserFile){
                    $em->remove($deleteUserFile);
                }

                $em->persist($application);
                $em->flush();

                // Redirect to same edit page
                $this->get('session')->getFlashBag()->add('success', sprintf('Settings for application %s is updated', $application->getName()));
                return $this->redirect($this->generateUrl('netvlies_publish_application_edit', array('application' => $application->getId())));
            }
        }

        return array(
            'form' => $form->createView(),
            'application' => $application
        );
    }

    /**
     * @Route("/delete/{application}")
     * @param $application Application
     * @return RedirectResponse
     */
    public function deleteAction(Application $application)
    {
        $em = $this->getDoctrine()->getManager();
        $application->setStatus(Application::STATUS_DELETED);
        $em->persist($application);
        $em->flush();

        return $this->redirect($this->generateUrl('netvlies_publish_default_index'));
    }


    /**
     * This action is used as subaction to load all available applications into its template.
     *
     * @Route("/list/widget")
     * @Template()
     * @return array
     */
    public function listWidgetAction()
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $apps = $em->getRepository('NetvliesPublishBundle:Application')->getAll();

        return array('apps' => $apps);
    }


    /**
     * @Route("/{application}/checkoutrepository")
     * @param Application $application
     * @return Response
     */
    public function checkoutRepository(Application $application)
    {
        /**
         * @var \Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface $versioningService
         */
        $versioningService = $this->get($application->getScmService());

        if(file_exists($versioningService->getRepositoryPath($application))){
            return $this->redirect($this->generateUrl('netvlies_publish_application_updaterepository', array('application' => $application->getId())));
        }

        $command = new CheckoutCommand();
        $command->setApplication($application);
        $command->setEnvironment($this->get('kernel')->getEnvironment());

        return $this->forward('NetvliesPublishBundle:Command:execApplicationCommand', array(
            'command'  => $command
        ));
    }

    /**
     * @Route("/{application}/updaterepository")
     * @param Application $application
     * @return RedirectResponse
     */
    public function updateRepositoryAction(Application $application)
    {
        /**
         * @var \Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface $versioningService
         */
        $versioningService = $this->get($application->getScmService());

        if(!file_exists($versioningService->getRepositoryPath($application))){
            return $this->redirect($this->generateUrl('netvlies_publish_application_checkoutrepository', array('application' => $application->getId())));
        }

        $versioningService->updateRepository($application);

        $this->get('session')->getFlashBag()->add('success', sprintf('Repository for %s is updated', $application->getName()));

        return $this->redirect($this->generateUrl('netvlies_publish_command_commandpanel', array('application' => $application->getId())));
    }
}
