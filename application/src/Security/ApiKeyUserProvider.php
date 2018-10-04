<?php

namespace App\Security;

use App\Entity\User;
use App\Exception\AuthenticationAppException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ApiKeyUserProvider
 * @package App\Security
 */
class ApiKeyUserProvider implements UserProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * ApiKeyUserProvider constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($apiKey)
    {
        return $this->entityManager->getRepository(User::class)->getUserFromApiKey($apiKey);
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        $message = 'ApiKeyUserProvider::refreshUser called; this should not have happened';
        throw new AuthenticationAppException($message);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return User::class === $class;
    }
}