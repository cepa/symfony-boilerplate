<?php

namespace Portal\Functional;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Tests\FunctionalTestCase;
use Tests\Traits\UserServiceTrait;

class RegistrationTest extends FunctionalTestCase
{
    use UserServiceTrait;

    const REGISTRATION_BUTTON = 'Sign up';
    const LOGIN_BUTTON        = 'Sign in';

    const NAME      = 'ProBiker!';
    const EMAIL     = 'pro.biker@domain.tld';
    const PASSWORD  = '$3cr3T666';

    public function testSuccessfulRegistration()
    {
        /*
         * Registration page, fill form and submit.
         */
        $crawler = $this->client->request('GET', '/sign-up');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Sign up', $this->client->getResponse()->getContent());

        $form = $crawler->selectButton(self::REGISTRATION_BUTTON)->form();
        $form['registration_form[name]'] = self::NAME;
        $form['registration_form[email]'] = mb_strtoupper(self::EMAIL); // Cheat, convert email to upper case.
        $form['registration_form[password][first]'] = self::PASSWORD;
        $form['registration_form[password][second]'] = self::PASSWORD;

        $this->client->enableProfiler();
        $this->client->submit($form);

        /*
         * Verify email send to user.
         */
        $collector = $this->client->getProfile()->getCollector('swiftmailer');
        $this->assertEquals(2, $collector->getMessageCount());

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        /*
         * Make sure user is inactive.
         */
        $user = $this->getUserService()->fetchByEmail(self::EMAIL);
        $this->assertFalse($user->isActive());
        $this->assertEquals(self::NAME, $user->getName());

        /** @var \Swift_Message $message */
        $message = $collector->getMessages()[0];
        $this->assertTrue($message instanceof \Swift_Message);
        $this->assertEquals('Welcome', $message->getSubject());
        $this->assertEquals(self::EMAIL, key($message->getTo()));
        $this->assertEquals('test@devgrid.net', key($message->getReplyTo()));
        $this->assertEquals('text/html', $message->getContentType());
        $this->assertStringContainsString(sprintf('Hi %s!', self::NAME), $message->getBody());

        /** @var RouterInterface $router */
        $router = $this->get('router');
        $activationPath = $router->generate('portal_registration_activate', [
            'uniqueId' => $user->getUniqueId(),
            'token' => $user->getToken()
        ]);
        $this->assertStringContainsString($activationPath, $message->getBody());

        /*
         * Verify email broadcasted to admins.
         */
        /** @var \Swift_Message $message */
        $message = $collector->getMessages()[1];
        $this->assertTrue($message instanceof \Swift_Message);
        $this->assertEquals(sprintf('User #%d registered account', $user->getId()), $message->getSubject());
        $this->assertStringNotContainsString($user->getPasswordHash(), $message->getBody());

        /*
         * Activate user account.
         */
        $this->client->request('GET', $activationPath);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        /*
         * Login as the new user.
         */
        $crawler = $this->client->request('GET', '/login');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton(self::LOGIN_BUTTON)->form();
        $form['login_form[_username]'] = self::EMAIL;
        $form['login_form[_password]'] = self::PASSWORD;

        $crawler = $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
    }

    public function testBlankFormFail()
    {
        $crawler = $this->client->request('GET', '/sign-up');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Sign up', $this->client->getResponse()->getContent());

        $form = $crawler->selectButton(self::REGISTRATION_BUTTON)->form();
        $this->client->submit($form);
        $this->assertStringContainsString('Sign up', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('This value should not be blank', $this->client->getResponse()->getContent());
    }

    public function testPasswordMismatchFail()
    {
        $crawler = $this->client->request('GET', '/sign-up');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Sign up', $this->client->getResponse()->getContent());

        $form = $crawler->selectButton(self::REGISTRATION_BUTTON)->form();
        $form['registration_form[name]'] = self::NAME;
        $form['registration_form[email]'] = self::EMAIL;
        $form['registration_form[password][first]'] = 'xxx';
        $form['registration_form[password][second]'] = 'yyy';

        $this->client->submit($form);
        $this->assertStringContainsString('Sign up', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('This value is not valid', $this->client->getResponse()->getContent());
    }

    public function testEmailAlreadyExistsFail()
    {
        $crawler = $this->client->request('GET', '/sign-up');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Sign up', $this->client->getResponse()->getContent());

        $form = $crawler->selectButton(self::REGISTRATION_BUTTON)->form();
        $form['registration_form[name]'] = self::NAME;
        $form['registration_form[email]'] = self::EMAIL;
        $form['registration_form[password][first]'] = self::PASSWORD;
        $form['registration_form[password][second]'] = self::PASSWORD;

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $crawler = $this->client->request('GET', '/sign-up');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Sign up', $this->client->getResponse()->getContent());

        $form = $crawler->selectButton(self::REGISTRATION_BUTTON)->form();
        $form['registration_form[name]'] = self::NAME;
        $form['registration_form[email]'] = self::EMAIL;
        $form['registration_form[password][first]'] = self::PASSWORD;
        $form['registration_form[password][second]'] = self::PASSWORD;

        $this->client->submit($form);
        $this->assertStringContainsString('Sign up', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('Email ' . self::EMAIL . ' has already been taken!', $this->client->getResponse()->getContent());
    }
}
