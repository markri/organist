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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Entity\Target;
use Netvlies\Bundle\PublishBundle\Action\InitCommand;

use Netvlies\Bundle\PublishBundle\Form\FormTargetEditType;
use Netvlies\Bundle\PublishBundle\Form\FormTargetStep1Type;
use Netvlies\Bundle\PublishBundle\Form\FormTargetStep2Type;

class TargetController extends Controller
{

    /**
     * Will return a list of all targets for this application
     *
     * @Route("/application/{id}/targets")
     * @ParamConverter("application", class="NetvliesPublishBundle:Application")
     * @Template()
     */
    public function targetsAction($application)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $targets = $em->getRepository('NetvliesPublishBundle:Target')->getOrderedByDTAP($application);

        return array(
            'application' => $application,
            'targets' => $targets
        );
    }

    /**
     * @Route("/application/{id}/target/new/step1")
     * @Template()
     */
    public function createStep1Action($id)
    {
        $em  = $this->getDoctrine()->getManager();
        $app = $em->getRepository('NetvliesPublishBundle:Application')->findOneById($id);

        return $this->handleFormStep1($app);
    }


    /**
     * @Route("/application/{id}/target/new/step2")
     * @Template()
     */
    public function createStep2Action($id)
    {
        $em  = $this->getDoctrine()->getManager();
        $app = $em->getRepository('NetvliesPublishBundle:Application')->findOneById($id);

        return $this->handleFormStep2($app);
    }


    /**
     * @Route("/target/edit/{id}")
     * @ParamConverter("target", class="NetvliesPublishBundle:Target")
     * @Template()
     */
    public function editAction($target)
    {
        $request = $this->getRequest();
        $form = $this->createForm(new FormTargetEditType(), $target, array());

        if($request->getMethod() == 'POST'){

            $form->bind($request);
            if($form->isValid()){
                $em->persist($target);
                $em->flush($target);

                $this->get('session')->getFlashBag()->add('success', sprintf('Target %s is updated', $target->getLabel()));
                return $this->redirect($this->generateUrl('netvlies_publish_target_targets', array('id'=>$target->getApplication()->getId())));
            }
        }

        return array(
            'application' => $target->getApplication(),
            'form' => $form->createView(),
        );
    }


    /**
     * @Route("/target/delete/{id}")
     * @ParamConverter("target", class="NetvliesPublishBundle:Target")
     */
    public function deleteAction($target)
    {
        $em  = $this->getDoctrine()->getManager();
        $label = $target->getLabel();
        $app = $target->getApplication();
        $target->setInactive(true);
        $em->flush();

        $this->get('session')->getFlashBag()->add('warning', sprintf('Target %s is deleted', $label));
        return $this->redirect($this->generateUrl('netvlies_publish_target_targets', array('id'=>$app->getId())));
    }


    /**
     * @param $app \Netvlies\Bundle\PublishBundle\Entity\Application
     * @return array
     */
    protected function handleFormStep1($app)
    {
        $request = $this->getRequest();

        $target = new Target();
        $target->setApplication($app);

        $formStep1 = $this->createForm(new FormTargetStep1Type(), $target, array());

        if($request->getMethod() == 'POST'){

            $formStep1->bind($request);

            // This is still an id, because we use a choicelist in order to get an ordered list of envs by O, T, A, P
            $envId = $target->getEnvironment()->getId();

            if($formStep1->isValid()){

                $request->getSession()->set('target.env', $envId);
                $request->getSession()->set('target.user', $target->getUsername());

                return $this->redirect($this->generateUrl('netvlies_publish_target_createstep2', array('id'=>$app->getId())));
            }
        }

        return array(
            'application' => $app,
            'form' => $formStep1->createView(),
        );

    }


    /**
     * @param Application $app
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function handleFormStep2($app)
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

        $target->setApplication($app);
        $target->setEnvironment($env);
        $target->setUsername($user);

        if($request->getMethod() != 'POST'){
            // Skip this part if not needed

            $homedir = '/home';

            // Init default values in target
            // @todo sure about this predefined stuff?
            switch($env->getType()){
                case 'D':
                    $appRoot = $homedir.'/'.$target->getUsername().'/vhosts/'.$app->getKeyName();
                    $target->setApproot($appRoot);
                    $target->setPrimaryDomain($app->getKeyName().'.'.$target->getUsername().'.'.$env->getHostname());
                    break;
                case 'T':
                    if($target->getUsername()=='tester'){
                        $target->setPrimaryDomain($app->getKeyName().'.'.$env->getHostname());
                    }
                    else{
                        $target->setPrimaryDomain($app->getKeyName().'.'.$target->getUsername().'.'.$env->getHostname());
                    }
                    $appRoot = $homedir.'/'.$target->getUsername().'/vhosts/'.$app->getKeyName().'/current';
                    $target->setApproot($appRoot);
                    $target->setCaproot($homedir.'/'.$target->getUsername().'/vhosts/'.$app->getKeyName());
                    break;
                case 'A':
                    $target->setPrimaryDomain($app->getKeyName().'.a.nvsotap.nl');
                    $appRoot = $homedir.'/'.$target->getUsername().'/www/current';
                    $target->setApproot($appRoot);
                    $target->setCaproot($homedir.'/'.$target->getUsername().'/www');
                    break;
                case 'P':
                    $target->setPrimaryDomain('www.'.$app->getKeyName().'.nl');
                    $appRoot = $homedir.'/'.$target->getUsername().'/www/current';
                    $target->setApproot($appRoot);
                    $target->setCaproot($homedir.'/'.$target->getUsername().'/www');
                    break;
            }

            switch($app->getApplicationType()){
                case 'symfony2':
                    $target->setWebroot($appRoot.'/web');
                    break;
                default:
                    $target->setWebroot($appRoot);
                    break;
            }

            $target->setMysqldb($app->getKeyName());
            $target->setMysqluser($app->getKeyName());

            // Just some random password
            $target->setMysqlpw(substr(str_shuffle(strtolower(sha1(rand() . time() . "my salty string"))),0, 10));
            $target->setLabel('('.$env->getType().') '.$app->getName());
        }

        $formStep2 = $this->createForm(new FormTargetStep2Type(), $target, array());
        $em  = $this->getDoctrine()->getManager();

        if($request->getMethod() == 'POST'){

            // Init form2
            $formStep2->bind($request);

            if($formStep2->isValid()){
                $em->persist($target);
                $em->flush($target);

                $this->get('session')->getFlashBag()->add('success', sprintf('Target %s is added', $target->getLabel()));
                return $this->redirect($this->generateUrl('netvlies_publish_target_targets', array('id'=>$app->getId())));
            }
        }

        return array(
            'application' => $app,
            'form' => $formStep2->createView(),
        );
    }


    /**
     * @Route("/target/init/{id}")
     * @ParamConverter("target", class="NetvliesPublishBundle:Target")
     * @param Target $target
     */
    public function initAction($target)
    {
        /**
         * @var \Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface $versioningService
         */
        $versioningService = $this->get($target->getApplication()->getScmService());

        $initCommand = new InitCommand();
        $initCommand->setApplication($target->getApplication());
        $initCommand->setTarget($target);
        $initCommand->setRepositoryPath($versioningService->getRepositoryPath($target->getApplication()));

        return $this->forward('NetvliesPublishBundle:Command:execTargetCommand', array(
            'command'  => $initCommand
        ));
    }

}
