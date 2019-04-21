<?php

namespace Core\Event;

use Core\Entity\User;
use Core\Service\MailerService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserAccountEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var MailerService
     */
    protected $mailer;

    public function __construct(LoggerInterface $logger, MailerService $mailer)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserAccountEvent::REGISTERED         => 'onRegistered',
            UserAccountEvent::ACTIVATED          => 'onActivated',
            UserAccountEvent::LOGIN              => 'onLogin',
            UserAccountEvent::LOGOUT             => 'onLogout',
            UserAccountEvent::MODIFIED           => 'onModified',
            UserAccountEvent::PASSWORD_CHANGED   => 'onPasswordChanged',
            UserAccountEvent::PASSWORD_REQUESTED => 'onPasswordRequested',
        ];
    }

    public function onRegistered(UserAccountEvent $event)
    {
        $user = $event->getUser();
        $this->logger->info(sprintf('%s: User #%d registered account, email=%s',
            __METHOD__, $user->getId(), $user->getEmail()));
        $this->sendWelcomeEmail($user);
        $this->notifyAdminsAboutRegisteredAccount($user);
    }

    public function onActivated(UserAccountEvent $event)
    {
        $user = $event->getUser();
        $this->logger->info(sprintf('%s: User #%d activated account',
            __METHOD__, $user->getId(), $user->getEmail()));
    }

    public function onLogin(UserAccountEvent $event)
    {
        $this->logger->info(sprintf('%s: User #%d logged in',
            __METHOD__, $event->getUser()->getId()));
    }

    public function onLogout(UserAccountEvent $event)
    {
        $this->logger->info(sprintf('%s: User #%d logged out',
            __METHOD__, $event->getUser()->getId()));
    }

    public function onModified(UserAccountEvent $event)
    {
        $this->logger->info(sprintf('%s: User #%d modified account details',
            __METHOD__, $event->getUser()->getId()));
    }

    public function onPasswordChanged(UserAccountEvent $event)
    {
        $this->logger->info(sprintf('%s: User #%d changed password',
            __METHOD__, $event->getUser()->getId()));
    }

    public function onPasswordRequested(UserAccountEvent $event)
    {
        $this->logger->info(sprintf('%s: User #%d requested password change',
            __METHOD__, $event->getUser()->getId()));
        $this->sendPasswordResetEmail($event->getUser());
    }

    protected function sendWelcomeEmail(User $user)
    {
        $mail = $this->mailer->createTwigMessage(
            'Welcome',
            'mail/welcome.html.twig',
            ['user' => $user]
        );
        $mail->setReplyTo('test@devgrid.net');
        $mail->setTo($user->getEmail());
        $this->mailer->send($mail);
    }

    protected function sendPasswordResetEmail(User $user)
    {
        $mail = $this->mailer->createTwigMessage(
            'Change password',
            'mail/password-reset.html.twig',
            ['user' => $user]
        );
        $mail->setTo($user->getEmail());
        $this->mailer->send($mail);
    }

    protected function notifyAdminsAboutRegisteredAccount(User $user)
    {
        $payload = $user->toArray();

        $unsetFields = ['passwordHash', 'token'];
        foreach ($unsetFields as $field) {
            if (isset($payload[$field])) {
                unset($payload[$field]);
            }
        }

        $this->mailer->broadcastToAdmins($this->mailer->createEventMessage(
            sprintf('User #%d registered account', $user->getId()), $payload));
    }
}
