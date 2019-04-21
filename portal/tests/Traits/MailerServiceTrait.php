<?php

namespace Tests\Traits;

use Core\Service\MailerService;

trait MailerServiceTrait
{
    protected function getMailerService(): MailerService
    {
        /** @var MailerService $service */
        $service = $this->get('core.mailer');
        $this->assertInstanceOf(MailerService::class, $service);
        return $service;
    }
}
