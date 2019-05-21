<?php

namespace AppBundle\Security\Core;

use AppBundle\Entity\User;
use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseFOSUBProvider;
use Symfony\Component\Security\Core\User\UserInterface;

class MyFOSUBUserProvider extends BaseFOSUBProvider
{
    /**
     * Constructor.
     *
     * @param UserManagerInterface $userManager fOSUB user provider
     * @param array                $properties  property mapping
     */
    public function __construct(UserManagerInterface $userManager, array $properties)
    {
        parent::__construct($userManager, $properties);
    }

    /**
     * @param UserInterface         $user
     * @param UserResponseInterface $response
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        // get property from provider configuration by provider name
        // , it will return `facebook_id` in that case (see service definition below)

        $property = $this->getProperty($response);
        $username = $response->getUsername();

        if (!$existingUser = $this->userManager->findUserBy([$property => $username])) {
            $existingUser = $this->userManager->findUserBy(['email' => $response->getEmail()]);
        }

        if (null !== $existingUser) {
            $this->disconnect($existingUser, $response);
        }

        if ($property === 'github_id') {
            /** @var User $user */
            $user->setGithubId($username);
        } elseif ($property === 'vkontakte_id') {
            /** @var User $user */
            $user->setVkontakteId($username);
        }

        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $userEmail = $response->getEmail();

        /** @var User $user */
        $user = $this->userManager->findUserByEmail($userEmail);

        $serviceName = $response->getResourceOwner()->getName();

        // if null just create new user and set it properties
        if (null === $user) {
            $user = new User();

            $user->setUsername($response->getUsername())
                ->setEmail($response->getEmail())
                ->setPassword('IsNotActive')
                ->setEnabled(true);

            // Set first and last name depending by service
            switch ($serviceName) {
                case 'vkontakte':
                    $user->setFirstName($response->getFirstName())
                        ->setLastName($response->getLastName());
                    break;
                case 'github':
                    $user->setFirstName('github')
                        ->setLastName('user');
                    break;
                case 'yandex':
                    $data = $response->getData();
                    $user->setFirstName($data['first_name'])
                        ->setLastName($data['last_name']);
                    break;
            }

            $this->userManager->updateUser($user);

            return $user;
        }
        // else update access token of existing user
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';
        $user->$setter($response->getAccessToken());//update access token

        return $user;
    }
}