<?php

namespace AppBundle\Subscriber;

use AppBundle\Entity\User;
use AppBundle\Event\UserLoginEvent;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Swift_Message;

/**
 * Class UserLoginSubscriber
 * @package AppBundle\Subscriber
 */
class UserLoginSubscriber implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    /**
     * UserLoginSubscriber constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public static function  getSubscribedEvents()
    {
        return ['message.login' => [
                ['sendMessage', 0],
                ['writeLog', -10],
            ]
        ];
    }

    /**
     * @param UserLoginEvent $event
     */
    public function sendMessage(UserLoginEvent $event)
    {
        /** @var User $user */
        $user = $event->getUser();
        $mailer = $event->getMailer();
        $message = Swift_Message::newInstance()
            ->setSubject('Authorization')
            ->setFrom( 'Maupo3311@mail.ru' )
            ->setTo($user->getEmail())
            ->setBody('test');

        $mailer->send($message);
    }

    /**
     * @param UserLoginEvent $event
     */
    public function writeLog(UserLoginEvent $event)
    {
        $this->logger->info(sprintf('Created a new user: %s', $event->getUser()->getEmail()));
    }
}