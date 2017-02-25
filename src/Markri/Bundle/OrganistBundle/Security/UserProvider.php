<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Markri\Bundle\OrganistBundle\Security;

use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Security\Core\Exception\LockedException;

class UserProvider extends OAuthUserProvider
{

    protected $mailRegex;

    public function __construct($mailRegex)
    {
        $this->mailRegex = $mailRegex;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        // Check if we are netvlies
        if(!empty($this->mailRegex) && !preg_match($this->mailRegex, $response->getEmail())){
            // If not we bail
            throw new LockedException();
        }

        return parent::loadUserByOAuthUserResponse($response);
    }
}