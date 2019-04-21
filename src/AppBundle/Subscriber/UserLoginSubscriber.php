<?php

namespace AppBundle\Subscriber;

use AppBundle\Entity\User;
use AppBundle\Event\UserLoginEvent;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Swift_Message;
use Exception;

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
     * @throws Exception
     */
    public function sendMessage(UserLoginEvent $event)
    {
        /** @var User $user */
        $user = $event->getUser();
        $mailer = $event->getMailer();
        $date = date('d-m-Y H:i');
        $message = Swift_Message::newInstance()
            ->setSubject('Authorization')
            ->setFrom( 'Maupo3311@mail.ru' )
            ->setTo($user->getEmail())
            ->setBody('User with username '.$user->getUsername().' logged in at '.$date.' with ip: '.$_SERVER['REMOTE_ADDR']);

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