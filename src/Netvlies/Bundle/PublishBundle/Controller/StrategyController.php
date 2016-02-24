<?php
/**
 * Netvlies Internetdiensten
 *
 * @author M. de Krijger <mdekrijger@netvlies.nl>
 * @copyright For the full copyright and license information, please view the LICENSE file
 */

namespace Netvlies\Bundle\PublishBundle\Controller;

use Netvlies\Bundle\PublishBundle\Entity\Strategy;
use Netvlies\Bundle\PublishBundle\Form\StrategyCreateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;

class StrategyController extends Controller
{

    /**
     * @Route("/strategy/list")
     * @Template()
     */
    public function listAction()
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $strategies = $em->getRepository('NetvliesPublishBundle:Strategy')->findAll();

        return array(
            'strategies' => $strategies
        );
    }

    /**
     * @Route("/strategy/create")
     * @Template()
     */
    public function createAction()
    {
        $request = $this->getRequest();
        $strategy = new Strategy();

        $form = $this->createForm(
            new StrategyCreateType(),
            $strategy
        );

        if($request->getMethod()=='POST'){
            $form->handleRequest($request);

            if ($form->isValid()) {
                try {
                    $em = $this->container->get('doctrine.orm.entity_manager');

                    $em->persist($strategy);
                    $em->flush();

                    $this->get('session')->getFlashBag()->add(
                        'success',
                        sprintf('Strategy %s is succesfully created', $strategy->getTitle())
                    );

                    return $this->redirect($this->generateUrl('netvlies_publish_strategy_list'));

                } catch (\Exception $e) {
                    $form->addError(new FormError('Something went wrong while storing strategy. Duplicate name?'));
                }
            }
        }

        return array(
            'form' => $form->createView()
        );
    }


    /**
     * @param Strategy $strategy
     * @Route("/strategy/templates/{strategy}")
     * @Template()
     */
    public function templatesAction(Strategy $strategy)
    {
        return array(
            'strategy' => $strategy
        );
    }

    /**
     * @Route("/strategy/delete/{strategy}")
     * @return Response
     */
    public function deleteAction(Strategy $strategy)
    {
        $em  = $this->getDoctrine()->getManager();
        $label = $strategy->getTitle();
        $em->remove($strategy);
        $em->flush();

        $this->get('session')->getFlashBag()->add('warning', sprintf('Strategy %s is deleted', $label));

        return $this->redirect($this->generateUrl('netvlies_publish_strategy_list' ));
    }

}
