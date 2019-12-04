<?php

namespace Core\Service;

use Core\Entity\Admin;
use Psr\Log\LoggerInterface;

class MailerService
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var string
     */
    protected $emailFrom;

    /**
     * @var
     */
    protected $emailName;

    /**
     * @var AdminService
     */
    protected $adminService;

    public function __construct(LoggerInterface $logger,
                                \Swift_Mailer $mailer,
                                \Twig\Environment $twig,
                                string $emailFrom,
                                string $emailName,
                                AdminService $adminService
    )
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->emailFrom = $emailFrom;
        $this->emailName = $emailName;
        $this->adminService = $adminService;
    }

    public function createMessage($subject, $body, $contentType = 'text/plain'): \Swift_Message
    {
        $message = new \Swift_Message($subject, $body, $contentType);
        $message->setFrom([$this->emailFrom => $this->emailName]);
        return $message;
    }

    public function createTwigMessage($subject, $template, $context = []): \Swift_Message
    {
        return $this->createMessage($subject, $this->twig->render($template, $context), 'text/html');
    }

    public function send(\Swift_Message $message, $to = null)
    {
        if (isset($to)) {
            $message->setTo($to);
        }
        return $this->mailer->send($message);
    }

    public function broadcastToAdmins(\Swift_Message $message)
    {
        $list = $this->adminService->listAdmins();
        /** @var Admin $admin */
        foreach ($list->getItems() as $admin) {
            $message->setTo($admin->getEmail());
            $this->send($message);
        }
    }

    public function createEventMessage($subject, $payload): \Swift_Message
    {
        return $this->createTwigMessage($subject,'mail/event.html.twig', [
            'subject' => $subject,
            'payload' => $payload
        ]);
    }
}
