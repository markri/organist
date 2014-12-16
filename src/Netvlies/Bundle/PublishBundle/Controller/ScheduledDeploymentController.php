<?php
/**
 * Created by PhpStorm.
 * User: mdekrijger
 * Date: 12/11/14
 * Time: 5:25 PM
 */

namespace Netvlies\Bundle\PublishBundle\Controller;

use Doctrine\ORM\EntityManager;
use Netvlies\Bundle\PublishBundle\Entity\ScheduledDeployment;
use Netvlies\Bundle\PublishBundle\Form\ScheduledDeploymentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Netvlies\Bundle\PublishBundle\Entity\Application;
use Symfony\Component\HttpFoundation\Request;


class ScheduledDeploymentController extends Controller
{

    /**
     * @Route("/application/{application}/scheduled", name="organist_scheduled_list")
     * @Template()
     * @param Application $application
     */
    public function listAction(Application $application)
    {
        return array(
            'scheduled' => $application->getScheduledDeployments()
        );
    }


    /**
     * @param Application $application
     *
     * @Route("/application/{application}/scheduled/create", name="organist_scheduled_create")
     * @Template()
     */
    public function createAction(Request $request, Application $application)
    {
        $scheduledDeployment = new ScheduledDeployment();
        $scheduledDeployment->setApplication($application);

        $form = $this->createForm(new ScheduledDeploymentType(), $scheduledDeployment, array('app' => $application));

        if ($request->getMethod() == 'POST') {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($scheduledDeployment);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success', 'Nightly build %s is created');
                return $this->redirect($this->generateUrl('organist_scheduled_list', array('application' => $application->getId())));
            }
        }

        return array(
            'form' => $form->createView()
        );
    }


    /**
     * @param ScheduledDeployment $scheduledDeployment
     * @Route("/application/{application}/scheduled/{scheduledDeployment}/delete", name="scheduled_delete")
     */
    public function deleteAction(Application $application, ScheduledDeployment $scheduledDeployment)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($scheduledDeployment);
        $em->flush();

        return $this->redirect($this->generateUrl('organist_scheduled_list', array('application' => $application->getId())));
    }
} 