<?php
/**
 * Created by JetBrains PhpStorm.
 * User: markri
 * Date: 7/26/13
 * Time: 1:54 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Netvlies\Bundle\PublishBundle\Security;

use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Security\Core\Exception\LockedException;

class UserProvider extends OAuthUserProvider
{

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        // Check if we are netvlies
        if(!preg_match('#@netvlies.net$#i', $response->getEmail())){
            // If not we bail
            throw new LockedException();
        }

        return parent::loadUserByOAuthUserResponse($response);
    }



}