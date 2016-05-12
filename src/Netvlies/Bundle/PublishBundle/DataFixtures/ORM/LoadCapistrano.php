<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Netvlies\Bundle\PublishBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Netvlies\Bundle\PublishBundle\Entity\CommandTemplate;
use Netvlies\Bundle\PublishBundle\Entity\Strategy;
use Symfony\Component\Yaml\Yaml;


class LoadCapistrano implements FixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $yaml = Yaml::parse(file_get_contents(dirname(__DIR__) . '/Resources/fixtures.yml'));

        foreach ($yaml as $strategyLabel => $commandTemplate) {
            $strategy = $this->createStrategy($strategyLabel);
            $manager->persist($strategy);

            foreach ($commandTemplate as $title => $config) {
                $template = $this->createTemplate($strategy, $config, $title);
                $manager->persist($template);
            }
        }

        $manager->flush();
    }

    function getOrder()
    {
        return 10;
    }

    /**
     * @param $label
     * @return Strategy
     */
    private function  createStrategy($label) {
        $strategy = new Strategy();
        $strategy->setTitle($label);
        return $strategy;
    }

    /**
     * @param $strategy
     * @param $config
     * @param $title
     * @return CommandTemplate
     */
    private function createTemplate($strategy, $config, $title)
    {
        $template = new CommandTemplate();
        $template->setStrategy($strategy);
        $template->setEnabledByDefault($config['enabled']);
        $template->setTitle($title);
        $template->setTemplate($config['template']);
        return $template;
    }

}