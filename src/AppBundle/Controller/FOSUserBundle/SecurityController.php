<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Controller\FOSUserBundle;

use AppBundle\Entity\User;
use AppBundle\Event\UserLoginEvent;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use FOS\UserBundle\Controller\SecurityController as BaseController;
use Swift_Mailer;

/**
 * Controller managing security.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class SecurityController extends BaseController
{
    private $tokenManager;

    /**
     * SecurityController constructor.
     * @param CsrfTokenManagerInterface|null $tokenManager
     */
    public function __construct(CsrfTokenManagerInterface $tokenManager = null)
    {
        parent::__construct($tokenManager);
        $this->tokenManager = $tokenManager;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function loginAction(Request $request)
    {
        /** @var $session Session */
        $session = $request->getSession();

        $authErrorKey = Security::AUTHENTICATION_ERROR;
        $lastUsernameKey = Security::LAST_USERNAME;

        /** get the error if any (works with forward and redirect -- see below) */
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null; /** The value does not come from the security component. */
        }

        /** last username entered by the user */
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);

        $csrfToken = $this->tokenManager
            ? $this->tokenManager->getToken('authenticate')->getValue()
            : null;

        return $this->renderLogin(array(
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $csrfToken,
        ));
    }

    /**
     * Send message by mail user after login
     *
     * @Route("/send_message")
     * @return RedirectResponse
     */
    public function afterLoginAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Swift_Mailer $mailer */
        $mailer     = $this->get('mailer');
        $loginEvent = new UserLoginEvent($user, $mailer);

        $this
            ->get('event_dispatcher')
            ->dispatch('message.login', $loginEvent);

        return $this->redirectToRoute('homepage');
    }
}
