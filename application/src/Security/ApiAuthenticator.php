<?php

namespace App\Security;

use App\Exception\AuthenticationAppException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;

/**
 * Class ApiAuthenticator
 * @package App\Security
 */
class ApiAuthenticator implements SimplePreAuthenticatorInterface
{
    /**
     * @param Request $request
     * @param         $providerKey
     *
     * @return PreAuthenticatedToken
     */
    public function createToken(Request $request, $providerKey)
    {
        if (!$apiKey = $request->headers->get('x-api-key')) {
            throw new AuthenticationAppException('No API key in headers');
        }

        return new PreAuthenticatedToken('nobody', $apiKey, $providerKey);
    }

    /**
     * @param TokenInterface $token
     * @param                $providerKey
     *
     * @return bool
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * @param TokenInterface        $token
     * @param UserProviderInterface $userProvider
     * @param                       $providerKey
     *
     * @return PreAuthenticatedToken
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (!$userProvider instanceof ApiKeyUserProvider) {
            $message = 'The user provider must be an instance of ApiKeyUserProvider (%s was given).';
            throw new \InvalidArgumentException(sprintf($message, get_class($userProvider)));
        }

        $apiKey = $token->getCredentials();

        if (!$user = $userProvider->loadUserByUsername($apiKey)) {
            $message = sprintf('No user exists for API Key "%s".', $apiKey);
            throw new AuthenticationAppException($message);
        }

        return new PreAuthenticatedToken(
            $user,
            $apiKey,
            $providerKey,
            $user->getRoles()
        );
    }
}