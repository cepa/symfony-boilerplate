<?php

namespace Tests\Admin\Functional;

use Symfony\Component\HttpFoundation\Response;
use Tests\FunctionalTestCase;
use Tests\Traits\AdminServiceTrait;

class AuthTest extends FunctionalTestCase
{
    use AdminServiceTrait;

    const ADMIN_EMAIL = 'admin@domain.tld';
    const ADMIN_PASS  = 's3cr3t';

    const USER_EMAIL  = 'test@domain.tld';
    const USER_PASS   = 'test';

    public function testAdminLoginSuccess()
    {
        $crawler = $this->client->request('GET', '/admin/login');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Admin', $this->client->getResponse()->getContent());

        $form = $crawler->selectButton('Login')->form();
        $form['login_form[_username]'] = self::ADMIN_EMAIL;
        $form['login_form[_password]'] = self::ADMIN_PASS;

        $crawler = $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertStringContainsString('easyadmin', $this->client->getResponse()->getContent());

        $admin = $this->getAdminService()->fetchByEmail(self::ADMIN_EMAIL);
        $this->assertInstanceOf(\DateTime::class, $admin->getLastloginAt());
        $this->assertNotEmpty($admin->getLastLoginIp());
        $this->assertNotEmpty($admin->getLastUserAgent());
    }

    public function testInvalidPasswordFailure()
    {
        $crawler = $this->client->request('GET', '/admin/login');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Admin', $this->client->getResponse()->getContent());

        $form = $crawler->selectButton('Login')->form();
        $form['login_form[_username]'] = self::ADMIN_EMAIL;
        $form['login_form[_password]'] = 'Invalid Password';

        $crawler = $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertStringContainsString('Invalid credentials.', $this->client->getResponse()->getContent());
    }

    public function testUserLoginAttemptFailure()
    {
        $crawler = $this->client->request('GET', '/admin/login');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Admin', $this->client->getResponse()->getContent());

        $form = $crawler->selectButton('Login')->form();
        $form['login_form[_username]'] = self::USER_EMAIL;
        $form['login_form[_password]'] = self::USER_PASS;

        $crawler = $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertStringContainsString('Username could not be found.', $this->client->getResponse()->getContent());
    }

    public function testInactiveLoginFailure()
    {
        $admin = $this->getAdminService()->fetchByEmail(self::ADMIN_EMAIL);
        $admin->setIsActive(false);
        $this->getAdminService()->save($admin);

        $crawler = $this->client->request('GET', '/admin/login');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Admin', $this->client->getResponse()->getContent());

        $form = $crawler->selectButton('Login')->form();
        $form['login_form[_username]'] = self::ADMIN_EMAIL;
        $form['login_form[_password]'] = self::ADMIN_PASS;

        $crawler = $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertStringContainsString('Invalid credentials.', $this->client->getResponse()->getContent());
    }
}
