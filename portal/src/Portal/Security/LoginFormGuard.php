<?php

namespace Portal\Security;

use Core\Entity\User;
use Core\Event\UserAccountEvent;
use Core\Service\UserService;
use Portal\Form\LoginForm;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class LoginFormGuard extends AbstractFormLoginAuthenticator
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(
            UserService $userService,
            FormFactoryInterface $formFactory,
            RouterInterface $router,
            LoggerInterface $logger,
            EventDispatcherInterface $eventDispatcher)
    {
        $this->userService = $userService;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->logger = $logger;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'portal_login' && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        return $this->formFactory->create(LoginForm::class)
            ->handleRequest($request)
            ->getData();
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (empty($credentials['_username']) || empty($credentials['_password'])) {
            return null;
        }
        return $this->userService->findByEmail($credentials['_username']);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $user->isActive() && $this->userService->validatePassword($user, $credentials['_password']);
    }

    public function getLoginUrl()
    {
        return $this->router->generate('portal_login');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $this->logger->info(sprintf("%s: Successful authentication of user %s",
            __METHOD__, $token->getUsername()));

        /** @var User $user */
        $user = $token->getUser();
        $user->setLastLoginAt(new \DateTime());
        $user->setLastLoginIp($request->getClientIp());
        if ($request->headers->has('User-Agent')) {
            $user->setLastUserAgent($request->headers->get('User-Agent'));
        }
        $this->userService->save($user);

        $this->eventDispatcher->dispatch(UserAccountEvent::LOGIN, new UserAccountEvent($user));

        return new RedirectResponse($this->router->generate('portal_index'));
    }
}
