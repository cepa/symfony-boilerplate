<?php

namespace Portal\Controller;

use Core\Service\UserService;
use Portal\Form\PasswordReminderForm;
use Portal\Form\PasswordResetForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PasswordResetController extends AbstractController
{
    /**
     * @var UserService
     */
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Route("/lost-password", name="portal_password_reminder")
     */
    public function reminder(Request $request)
    {
        $statusCode = Response::HTTP_OK;

        $form = $this->createForm(PasswordReminderForm::class);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $user = $this->userService->fetchByEmail($form['email']->getData());
                $this->userService->requestPassword($user);
                $this->addFlash('success', 'Check your mailbox.');
                $statusCode = Response::HTTP_ACCEPTED;

            } else {
                $statusCode = Response::HTTP_BAD_REQUEST;
            }
        }

        return $this->render('portal/password-reminder.html.twig', [
            'form' => $form->createView()
        ])->setStatusCode($statusCode);
    }

    /**
     * @Route("/reset-password/{uniqueId}/{token}",
     *     name="portal_password_reset",
     *     requirements={"uniqueId": "[a-zA-Z0-9]{10}", "token": "[a-zA-Z0-9]{32}"})
     */
    public function reset($uniqueId, $token, Request $request)
    {
        $statusCode = Response::HTTP_OK;

        $user = $this->userService->fetchByUniqueId($uniqueId);
        if ($user->getToken() != $token) {
            $this->addFlash('error', 'Invalid user token!');
            return $this->redirectToRoute('portal_index');
        }

        $form = $this->createForm(PasswordResetForm::class);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if (!$user->isActive()) {
                    $this->userService->activate($user);
                }

                $this->userService->changePassword($user, $form['password']->getData());
                $this->addFlash('success', 'Your password has been changed, you can sign in now.');
                return $this->redirectToRoute('portal_index');

            } else {
                $statusCode = Response::HTTP_BAD_REQUEST;
            }
        }

        return $this->render('portal/password-reset.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ])->setStatusCode($statusCode);
    }
}
