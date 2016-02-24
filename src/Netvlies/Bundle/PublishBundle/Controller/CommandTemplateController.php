<?php
/**
 * Netvlies Internetdiensten
 *
 * @author M. de Krijger <mdekrijger@netvlies.nl>
 * @copyright For the full copyright and license information, please view the LICENSE file
 */

namespace Netvlies\Bundle\PublishBundle\Controller;

use Netvlies\Bundle\PublishBundle\Entity\CommandTemplate;
use Netvlies\Bundle\PublishBundle\Form\CommandTemplateCreateType;
use Netvlies\Bundle\PublishBundle\Form\StrategyCreateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;

class CommandTemplateController extends Controller
{

    /**
     * @Route("/template/list")
     * @Template()
     */
    public function listAction()
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $commandTemplates = $em
            ->getRepository('NetvliesPublishBundle:CommandTemplate')
            ->createQueryBuilder('c')
            ->select('c')
            ->orderBy('c.strategy')
            ->getQuery()
            ->getResult();

        return array(
            'commandTemplates' => $commandTemplates
        );
    }

    /**
     * @Route("/template/create")
     * @Template()
     */
    public function createAction()
    {
        $request = $this->getRequest();
        $commandTemplate = new CommandTemplate();

        $form = $this->createForm(
            new CommandTemplateCreateType(),
            $commandTemplate
        );

        if($request->getMethod()=='POST'){
            $form->handleRequest($request);

            if ($form->isValid() && $this->validateTwig($form, $commandTemplate)) {
                try {
                    $em = $this->container->get('doctrine.orm.entity_manager');

                    $em->persist($commandTemplate);
                    $em->flush();

                } catch (\Exception $e) {
                    $form->addError(new FormError('Something went wrong while storing command template.'));
                }

                $this->get('session')->getFlashBag()->add(
                    'success',
                    sprintf('Command template %s is succesfully created', $commandTemplate->getTitle())
                );

                return $this->redirect($this->generateUrl('netvlies_publish_commandtemplate_list'));
            }
        }

        return array(
            'form' => $form->createView()
        );

    }


    /**
     * @Route("/template/{commandTemplate}/edit")
     * @Template()
     *
     * @param CommandTemplate $template
     */
    public function editAction(CommandTemplate $commandTemplate)
    {

        $form = $this->createForm(
            new CommandTemplateCreateType(),
            $commandTemplate
        );

        $request = $this->getRequest();

        if($request->getMethod()=='POST'){
            $form->handleRequest($request);

            if ($form->isValid() && $this->validateTwig($form, $commandTemplate)) {

                try {
                    $em = $this->container->get('doctrine.orm.entity_manager');

                    $em->flush();

                } catch (\Exception $e) {
                    $form->addError(new FormError('Something went wrong while updating command template.'));
                }

                $this->get('session')->getFlashBag()->add(
                    'success',
                    sprintf('Command template %s is succesfully updated', $commandTemplate->getTitle())
                );

                return $this->redirect($this->generateUrl('netvlies_publish_commandtemplate_list'));
            }
        }

        return array(
            'form' => $form->createView()
        );
    }


    private function validateTwig(Form $form, CommandTemplate $commandTemplate)
    {
        $twigEnvironment = $this->get('twig');
        try {
            $twigEnvironment->parse($twigEnvironment->tokenize($commandTemplate->getTemplate()));
            return true;
        } catch (\Twig_Error $e) {

            $form->addError(new FormError('Twig validation: ' . $e->getMessage()));

            return false;
        }
    }
}
