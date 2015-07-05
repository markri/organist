<?php
/**
 * Created by PhpStorm.
 * User: mdekrijger
 * Date: 7/5/15
 * Time: 3:33 PM
 */

namespace Netvlies\Bundle\PublishBundle\Strategy\Commands;

use Doctrine\ORM\EntityManager;
use Netvlies\Bundle\PublishBundle\Entity\Command;
use Twig_Environment;
use Twig_Error_Loader;
use Twig_LoaderInterface;

class CommandTwigLoader implements Twig_LoaderInterface, \Twig_ExistsLoaderInterface
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Gets the source code of a template, given its name.
     *
     * @param string $name The name of the template to load
     *
     * @return string The template source code
     *
     * @throws Twig_Error_Loader When $name is not found
     */
    public function getSource($name)
    {
        /**
         * @var Command $command
         */
        $command = $this->em->getRepository('NetvliesPublishBundle:Command')->findOneById($name);
        return $command->getTemplate();
    }

    /**
     * Gets the cache key to use for the cache for a given template name.
     *
     * @param string $name The name of the template to load
     *
     * @return string The cache key
     *
     * @throws Twig_Error_Loader When $name is not found
     */
    public function getCacheKey($name)
    {
        return 'command_'.$name;
    }

    /**
     * Returns true if the template is still fresh.
     *
     * @param string $name The template name
     * @param timestamp $time The last modification time of the cached template
     *
     * @return bool    true if the template is fresh, false otherwise
     *
     * @throws Twig_Error_Loader When $name is not found
     */
    public function isFresh($name, $time)
    {
        return false; // For now always return fresh template
//        if (false === $lastModified = $this->getValue('last_modified', $name)) {
//            return false;
//        }
//
//        return $lastModified <= $time;
    }

    /**
     * Check if we have the source code of a template, given its name.
     *
     * @param string $name The name of the template to check if we can load
     *
     * @return bool    If the template source code is handled by this loader or not
     */
    public function exists($name)
    {
        $source = $this->getSource($name);
        return !empty($source);
    }

}