<?php

namespace Tests\Core\Service;

use Tests\ServiceTestCase;
use Tests\Traits\MailerServiceTrait;

class MailerServiceTest extends ServiceTestCase
{
    use MailerServiceTrait;

    public function testMailer()
    {
        $mailer = $this->getMailerService();

        $message = $mailer->createMessage('xxx', 'yyy');
        $this->assertInstanceOf(\Swift_Message::class, $message);
        $this->assertEquals('xxx', $message->getSubject());
        $this->assertEquals('yyy', $message->getBody());

        $message = $mailer->createTwigMessage('test', 'mail/test.html.twig', [
            'message' => 'Hello!'
        ]);
        $this->assertInstanceOf(\Swift_Message::class, $message);
        $this->assertEquals('test', $message->getSubject());
        $this->assertEquals('text/html', $message->getContentType());
        $this->assertStringContainsString('Hello!', $message->getBody());

        $mailer->send($message, ['dummy@domain.tld' => 'Dummy']);
    }
}
