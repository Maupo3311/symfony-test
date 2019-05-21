<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\User;
use Swift_Message;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Swift_Mailer;

/**
 * Class AuthenticationSuccessHandler
 * @package AppBundle\EventListener
 */
class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /** @var Swift_Mailer */
    protected $mailer;

    /** @var string */
    protected $mailerUser;

    /**
     * AuthenticationSuccessHandler constructor.
     * @param Swift_Mailer $mailer
     * @param string       $mailerUser
     */
    public function __construct(Swift_Mailer $mailer, string $mailerUser)
    {
        $this->mailer     = $mailer;
        $this->mailerUser = $mailerUser;
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {

//        /** @var User $user */
//        $user = $event->getAuthenticationToken()->getUser();
//        $date = date('d-m-Y H:i');
//
//        $message = Swift_Message::newInstance()
//            ->setSubject('Authorization')
//            ->setFrom($this->mailerUser)
//            ->setTo($user->getEmail())
//            ->setBody(
//                "User with username
//                {$user->getUsername()} logged in at {$date}
//                with ip: {$_SERVER['REMOTE_ADDR']}"
//            );
//
//        $this->mailer->send($message);

        $token   = $event->getAuthenticationToken();
        $request = $event->getRequest();
        $this->onAuthenticationSuccess($request, $token);
    }

    /**
     * @param Request        $request
     * @param TokenInterface $token
     * @return Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        return new RedirectResponse('/');
    }
}