<?php

namespace AppBundle\Event;

use AppBundle\Entity\User;
use FOS\UserBundle\Mailer\Mailer;
use Symfony\Component\EventDispatcher\Event;
use Swift_Mailer;

class UserLoginEvent extends Event
{
    protected $user;
    protected $mailer;

    /**
     * UserLoginEvent constructor.
     * @param User         $user
     * @param Swift_Mailer $mailer
     */
    public function __construct(User $user, Swift_Mailer $mailer)
    {
        $this->user = $user;
        $this->mailer = $mailer;
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
    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * @param Swift_Mailer $mailer
     * @return UserLoginEvent
     */
    public function setMailer(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;

        return $this;
    }
}