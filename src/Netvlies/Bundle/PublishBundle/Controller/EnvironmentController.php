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

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Netvlies\Bundle\PublishBundle\Form\EnvironmentCreateType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Netvlies\Bundle\PublishBundle\Entity\Environment;


class EnvironmentController extends Controller
{

    /**
     * Returns edit form for certain env
     *
     * @Route("/environment/edit/{environment}")
     * @Template()
     */
    public function editAction(Environment $environment)
    {
        $request = $this->getRequest();
        $form = $this->createForm(new EnvironmentCreateType(), $environment, array());

        if($request->getMethod() == 'POST'){

            $form->handleRequest($request);

            if($form->isValid()){
                $em  = $this->getDoctrine()->getManager();
                $em->persist($environment);
                $em->flush($environment);

                $this->get('session')->getFlashBag()->add('success', sprintf('Environment %s is updated', $environment->getHostname()));
                return $this->redirect($this->generateUrl('netvlies_publish_environment_list'));
            }
        }

        $formView = $form->createView();
        $formView->vars['attr']['data-horizontal'] = true;

        return array(
            'form' => $formView
        );
    }

    /**
     * @Route("/environment/create")
     * @Template()
     */
    public function createAction()
    {
        $request = $this->getRequest();

        $environment = new Environment();

        $form = $this->createForm(new EnvironmentCreateType(), $environment, array());

        if($request->getMethod() == 'POST'){

            $form->handleRequest($request);

            if($form->isValid()){

                /**
                 * @var EntityManager $em
                 */
                $em = $this->get('doctrine.orm.entity_manager');
                $em->persist($environment);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success', sprintf('Environment %s is created', $environment->getHostname()));
                return $this->redirect($this->generateUrl('netvlies_publish_environment_list'));
            }
        }

        $formView = $form->createView();
        $formView->vars['attr']['data-horizontal'] = true;

        return array(
            'form' => $formView,
        );
    }


    /**
     * @Route("/environment/list")
     * @Template()
     */
    public function listAction()
    {
        /**
         * @var EntityManager $em
         */
        $em = $this->get('doctrine.orm.entity_manager');
        $environments = $em->getRepository('NetvliesPublishBundle:Environment')->getOrderedByTypeAndHost();

        return array(
            'environments' => $environments
        );
    }

    /**
     * @Route("/environment/delete/{environment}")
     * @return Response
     */
    public function deleteAction(Environment $environment)
    {
        $em  = $this->getDoctrine()->getManager();
        $label = $environment->getHostname();
        $em->remove($environment);
        $em->flush();

        $this->get('session')->getFlashBag()->add('warning', sprintf('Environment %s is deleted', $label));

        return $this->redirect($this->generateUrl('netvlies_publish_environment_list' ));
    }

}
