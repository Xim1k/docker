<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            /* Redirect the user to the homepage */
            return $this->redirectToRoute('index');
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="logout")
     */

    public function logout()
    {
        // controller can be blank: it will never be executed!
    }

    /**
     * @Route("/activation/{token}", name="activation")
     */

    public function activation(string $token)
    {

        $manager = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository(User::class)->getUserByToken($token);

        if (!$user)
        {
            throw $this->createNotFoundException('Ooops, there is no such page');
        }

        $user->setToken('');
        $manager->persist($user);
        $manager->flush();

        $this->addFlash('notice', 'Вы успешно активировали свой аккаунт!');
        return $this->redirectToRoute('app_login');

    }


}
