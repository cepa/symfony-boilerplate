<?php

namespace Tests\Traits;

use Core\Service\AdminService;

trait AdminServiceTrait
{
    protected function getAdminService(): AdminService
    {
        /** @var AdminService $service */
        $service = $this->get('core.admin');
        $this->assertInstanceOf(AdminService::class, $service);
        return $service;
    }
}
