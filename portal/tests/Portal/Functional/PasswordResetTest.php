<?php

namespace Tests\Portal\Functional;

use Symfony\Component\HttpFoundation\Response;
use Tests\FunctionalTestCase;
use Tests\Traits\UserServiceTrait;

class PasswordResetTest extends FunctionalTestCase
{
    use UserServiceTrait;

    const EMAIL = 'test@test.tld';
    const NEW_PASSWORD = 'HasłoMasło';

    public function testRemindAndReset()
    {
        /**
         * Reminder page, fill form and submit.
         */
        $crawler = $this->client->request('GET', '/lost-password');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Lost password?', $this->client->getResponse()->getContent());

        $form = $crawler->filter('.app-form')->form();
        $form['password_reminder_form[email]'] = self::EMAIL;

        $this->client->enableProfiler();
        $this->client->submit($form);
        $this->assertEquals(Response::HTTP_ACCEPTED, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Check your mailbox', $this->client->getResponse()->getContent());

        /*
         * Verify email send to user.
         */
        $collector = $this->client->getProfile()->getCollector('swiftmailer');
        $this->assertEquals(1, $collector->getMessageCount());

        $message = $collector->getMessages()[0];
        $this->assertTrue($message instanceof \Swift_Message);
        $this->assertEquals('Change password', $message->getSubject());
        $this->assertEquals(self::EMAIL, key($message->getTo()));
        $this->assertEquals('text/html', $message->getContentType());
        $this->assertStringContainsString('Click on the following link to reset your password', $message->getBody());

        /*
         * Make user inactive to check if password reset re-activates the account.
         */
        $user = $this->getUserService()->fetchByEmail(self::EMAIL);
        $user->setIsActive(false);
        $this->getUserService()->save($user);

        /** @var RouterInterface $router */
        $router = $this->get('router');
        $resetPath = $router->generate('portal_password_reset', [
            'uniqueId' => $user->getUniqueId(),
            'token' => $user->getToken()
        ]);
        $this->assertStringContainsString($resetPath, $message->getBody());

        /**
         * Reset page, fill form and submit.
         */
        $crawler = $this->client->request('GET', $resetPath);
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Reset your password', $this->client->getResponse()->getContent());

        $form = $crawler->filter('.app-form')->form();
        $form['password_reset_form[password][first]'] = self::NEW_PASSWORD;
        $form['password_reset_form[password][second]'] = self::NEW_PASSWORD;

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        /*
         * Login with the new password, user account must have been reactivated!
         */
        $crawler = $this->client->request('GET', '/login');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Sign in')->form();
        $form['login_form[_username]'] = self::EMAIL;
        $form['login_form[_password]'] = self::NEW_PASSWORD;

        $crawler = $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertStringContainsString('index', $this->client->getResponse()->getContent());
    }

    public function testEmailNotFoundFail()
    {
        $crawler = $this->client->request('GET', '/lost-password');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Lost password?', $this->client->getResponse()->getContent());

        $form = $crawler->filter('.app-form')->form();
        $form['password_reminder_form[email]'] = 'dummy@test.tld';

        $this->client->enableProfiler();
        $this->client->submit($form);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('has not been found!', $this->client->getResponse()->getContent());

        $collector = $this->client->getProfile()->getCollector('swiftmailer');
        $this->assertEquals(0, $collector->getMessageCount());
    }

    public function testInvalidUniqueIdRedirectsToIndex()
    {
        $user = $this->getUserService()->fetchByEmail(self::EMAIL);
        $this->getUserService()->changePassword($user, 'yyy');
        $invalidToken = $user->getToken();
        $this->getUserService()->requestPassword($user);

        /** @var RouterInterface $router */
        $router = $this->get('router');
        $resetPath = $router->generate('portal_password_reset', [
            'uniqueId' => $user->getUniqueId(),
            'token' => $invalidToken
        ]);

        $crawler = $this->client->request('GET', $resetPath);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertStringContainsString('Invalid user token!', $this->client->getResponse()->getContent());
    }

    public function testInvalidNewPasswordFail()
    {
        $user = $this->getUserService()->fetchByEmail(self::EMAIL);
        $this->getUserService()->changePassword($user, 'yyy');

        /** @var RouterInterface $router */
        $router = $this->get('router');
        $resetPath = $router->generate('portal_password_reset', [
            'uniqueId' => $user->getUniqueId(),
            'token' => $user->getToken()
        ]);

        $crawler = $this->client->request('GET', $resetPath);
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Reset your password', $this->client->getResponse()->getContent());

        $form = $crawler->filter('.app-form')->form();
        $form['password_reset_form[password][first]'] = '';
        $form['password_reset_form[password][second]'] = '';

        $this->client->submit($form);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('This value should not be blank.', $this->client->getResponse()->getContent());

        $form = $crawler->filter('.app-form')->form();
        $form['password_reset_form[password][first]'] = 'xxx';
        $form['password_reset_form[password][second]'] = '';

        $this->client->submit($form);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('This value is not valid.', $this->client->getResponse()->getContent());

        $form = $crawler->filter('.app-form')->form();
        $form['password_reset_form[password][first]'] = 'xxx';
        $form['password_reset_form[password][second]'] = 'yyy';

        $this->client->submit($form);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('This value is not valid.', $this->client->getResponse()->getContent());
    }
}
