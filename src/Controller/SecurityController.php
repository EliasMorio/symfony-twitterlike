<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\RegistrationType;
use Doctrine\Migrations\Configuration\EntityManager\ManagerRegistryEntityManager;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(Request $request): Response
    {
        //function automatically called by Symfony
        return $this->render('security/login.html.twig');
    }

        /**
         * @Route("/logout", name="app_logout")
         */
        public function logout()
        {
            //function automatically called by Symfony
        }


    /**
     * @Route("/registration", name="app_registration")
     */
    public function registration(Request $request, ManagerRegistry $managerRegistry, UserPasswordHasherInterface $passwordHasher): Response
    {
        $manager = $managerRegistry->getManager();
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()){

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);

            $manager->persist($user);
            //don't forget to flush
            $manager->flush();

            return $this->redirectToRoute('app_login');
        }



        return $this->render('security/registration.html.twig',
        [
            'form' => $form->createView(),
        ]);
    }

}
