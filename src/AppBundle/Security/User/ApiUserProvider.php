<?php

namespace AppBundle\Security\User;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class ApiUserProvider
 * @package AppBundle\Security\User
 */
class ApiUserProvider implements UserProviderInterface
{
    /**
     * @var EntityManager EntityManager
     */
    public $entityManager;

    /**
     * ApiUserProvider constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $username
     * @return mixed|UserInterface
     * @throws NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
        return $this->fetchUser($username);
    }

    /**
     * @param $apiKey
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function loadUserByApiKey($apiKey)
    {
        return $this->fetchUser($apiKey);
    }

    /**
     * @param UserInterface $user
     * @return mixed|UserInterface
     * @throws NonUniqueResultException
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        $username = $user->getUsername();

        return $this->fetchUser($username);
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return User::class === $class;
    }

    /**
     * @param $usernameOrApiKey
     * @return mixed
     * @throws NonUniqueResultException
     */
    private function fetchUser($usernameOrApiKey)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        if (!$user = $userRepository->findByApiKey($usernameOrApiKey)) {
            if (!$user = $userRepository->findByUsername($usernameOrApiKey)) {
                throw new UsernameNotFoundException(
                    sprintf('Username "%s" does not exist.', $usernameOrApiKey)
                );
            }
        }

        return $user;
    }
}