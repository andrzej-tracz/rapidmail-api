<?php

namespace App\UI\Http\Web;

use App\Infrastructure\Template\Repository\TemplateRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class WebsiteController extends Controller
{
    /**
     * @Route("/", name="index", methods={"GET", "POST"})
     */
    public function index(TemplateRepository $templateRepository)
    {
        $data = [
            'templates' => $templateRepository->findAll(),
        ];

        return $this->render('website/index.html.twig', $data);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authUtils, Security $security, TokenStorageInterface $storage)
    {
        if ($security->isGranted('ROLE_USER')) {
            return $this->redirect('/admin');
        }

        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
}
