<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Company;
use App\Form\DriverType;
use App\Form\CompanyType;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationCompanyController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register-user-company/{slug}', name: 'app_register_user_company')]
    public function registerDriver(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface, $slug): Response
    {
        $user = new User();

        $role = $slug == 'driver' ? 'a7#ddd8' : '8y*2s2';

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setRoles([$role == 'a7#ddd8' ? 'ROLE_DRIVER' : 'ROLE_COMPANY']);
            $user->setPassword(
            $userPasswordHasherInterface->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_register_compagny', ['user' => $user->getId(),
                'role' => $role
            ]);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/register-company/{user}/{role}', name: 'app_register_compagny')]
    public function registerDriverCompangy(Request $request, $user, $role): Response
    {
        $company = new Company();
        $user = $this->getDoctrine()->getRepository(User::class)->find($user);

        if($role == 'a7#ddd8')
        {
            $form = $this->createForm(DriverType::class, $company);
        }else
        {
            $form = $this->createForm(CompanyType::class, $company);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($company);
            $entityManager->flush();

            $user->setCompany($company);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register-compagny.html.twig', [
            'registrationForm' => $form->createView(),
            'role' => $role
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('homepage');
    }
}
