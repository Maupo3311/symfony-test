<?php

namespace AppBundle\Event;

use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;
use Swift_Mailer;

class UserRegisteredEvent extends Event
{
    const EVENT_NAME = 'successful.registration.message';

    /** @var User  */
    protected $user;

    /** @var Swift_Mailer  */
    protected $mailer;

    /** @var string  */
    protected $mailerUser;

    /**
     * UserRegisteredEvent constructor.
     * @param User         $user
     * @param Swift_Mailer $mailer
     * @param string       $mailerUser
     */
    public function __construct(User $user, Swift_Mailer $mailer, string $mailerUser)
    {
        $this->user       = $user;
        $this->mailer     = $mailer;
        $this->mailerUser = $mailerUser;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Swift_Mailer
     */
    public function getMailer(): Swift_Mailer
    {
        return $this->mailer;
    }

    /**
     * @param Swift_Mailer $mailer
     */
    public function setMailer(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @return string
     */
    public function getMailerUser(): string
    {
        return $this->mailerUser;
    }

    /**
     * @param string $mailerUser
     */
    public function setMailerUser(string $mailerUser)
    {
        $this->mailerUser = $mailerUser;
    }
}