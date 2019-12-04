<?php

namespace Tests\Portal\Functional;

use Symfony\Component\HttpFoundation\Response;
use Tests\FunctionalTestCase;
use Tests\Traits\UserServiceTrait;

class AuthTest extends FunctionalTestCase
{
    use UserServiceTrait;

    const ADMIN_EMAIL = 'admin@domain.tld';
    const ADMIN_PASS  = 's3cr3t';

    const USER_EMAIL  = 'test@test.tld';
    const USER_PASS   = 'test';

    const USER_EMAIL_CASE_INSENSITIVE = 'TeSt@tEsT.TlD';

    public function testUserLoginSuccess()
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Sign in')->form();
        $form['login_form[_username]'] = self::USER_EMAIL;
        $form['login_form[_password]'] = self::USER_PASS;

        $crawler = $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $user = $this->getUserService()->fetchByEmail(self::USER_EMAIL);
        $this->assertInstanceOf(\DateTime::class, $user->getLastloginAt());
        $this->assertNotEmpty($user->getLastLoginIp());
        $this->assertNotEmpty($user->getLastUserAgent());
    }

    public function testUserLoginWithCaseInsensitiveSuccess()
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Sign in')->form();
        $form['login_form[_username]'] = self::USER_EMAIL_CASE_INSENSITIVE;
        $form['login_form[_password]'] = self::USER_PASS;

        $crawler = $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
    }

    public function testInvalidPasswordFailure()
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Sign in')->form();
        $form['login_form[_username]'] = self::USER_EMAIL;
        $form['login_form[_password]'] = 'invalidP@$$word';

        $crawler = $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertStringContainsString('Invalid email or password.', $this->client->getResponse()->getContent());
    }

    public function testAdminLoginAttemptFailure()
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Sign in')->form();
        $form['login_form[_username]'] = self::ADMIN_EMAIL;
        $form['login_form[_password]'] = self::ADMIN_PASS;

        $crawler = $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertStringContainsString('Invalid email or password.', $this->client->getResponse()->getContent());
    }

    public function testInactiveLoginFailure()
    {
        $user = $this->getUserService()->fetchByEmail(self::USER_EMAIL);
        $user->setIsActive(false);
        $this->getUserService()->save($user);

        $crawler = $this->client->request('GET', '/login');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Sign in')->form();
        $form['login_form[_username]'] = self::USER_EMAIL;
        $form['login_form[_password]'] = self::USER_PASS;

        $crawler = $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertStringContainsString('Invalid email or password.', $this->client->getResponse()->getContent());
    }
}
