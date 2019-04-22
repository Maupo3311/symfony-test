<?php

namespace AppBundle\Subscriber;

use AppBundle\Event\UserRegisteredEvent;
use Psr\Log\LoggerAwareTrait;
use Swift_Message;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UserConfirmRegistrationSubscriber
 * @package AppBundle\Subscriber
 */
class UserConfirmRegistrationSubscriber implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [UserRegisteredEvent::EVENT_NAME => 'onRegistrationSuccess'];
    }

    /**
     * @param UserRegisteredEvent $event
     */
    public function onRegistrationSuccess(UserRegisteredEvent $event)
    {
        $user   = $event->getUser();
        $mailer = $event->getMailer();

        $message = Swift_Message::newInstance()
            ->setSubject('Successful registration')
            ->setFrom($event->getMailerUser())
            ->setTo($user->getEmail())
            ->setBody('User ' . $user->getUsername() . ' successfully registered!');

        $mailer->send($message);
    }
}