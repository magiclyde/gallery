<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    private function createRegistrationForm(Request $request)
    {
        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        return $form;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, 
        UserManager $userManager): Response
    {
        $form = $this->createRegistrationForm($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $userManager->register($data);
            $userManager->login($user, $request);

            $this->addFlash('success', 
                'You\'ve been registered successfully');

            // do anything else you need here, like send an email
            //$this->get('app_mailer')->sendMailInscriptionMjml(
            //  $invite, $this->getParameter('client_mail_to')
            //);

            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

}
