<?php
namespace ExchangeBundle\Controller;

use ExchangeBundle\Entity\User;
use ExchangeBundle\Entity\Wallet;
use ExchangeBundle\Form\CantorType;
use ExchangeBundle\Form\ChangeEmailType;
use ExchangeBundle\Form\ChangePasswordType;
use ExchangeBundle\Form\LoginType;
use ExchangeBundle\Form\UserType;
use ExchangeBundle\Form\WalletType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class MainController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {
        return $this->render('ExchangeBundle:Main:index.html.twig', array());
    }

    /**
     * @Route("/register", name="register")
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $formUserType = $this->createForm(UserType::class, $user);
        $formUserType->handleRequest($request);

        if ($formUserType->isSubmitted() && $formUserType->isValid()) {
            $post = $formUserType->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $wallet = new Wallet();
            $entityManager->persist($wallet);
            $post->setWallet($wallet);
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render('ExchangeBundle:Main:register.html.twig', array(
           'form' => $formUserType->createView()
        ));
    }

    /**
     * @Route("/login", name="login")
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastLogin = $authenticationUtils->getLastUsername();

        $formLoginType = $this->createForm(LoginType::class, array(
            'login' => $lastLogin,
        ));

        return $this->render('ExchangeBundle:Main:login.html.twig', array(
            'form' => $formLoginType->createView(),
            'error' => $error,
        ));
    }

    /**
     * @Route("/wallet", name="wallet")
     * @Security("is_granted('ROLE_USER')")
     */
    public function createWalletAction(Request $request)
    {
        $wallet = $this->getUser()->getWallet();
        $formWalletType = $this->createForm(WalletType::class, $wallet);
        $formWalletType->handleRequest($request);

        if ($formWalletType->isSubmitted() && $formWalletType->isValid()) {
            $post = $formWalletType->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $post->checkIsActive();
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('index');
        }

        return $this->render('ExchangeBundle:Main:wallet.html.twig', array(
            'form' => $formWalletType->createView()
        ));
    }

    /**
     * @Route("/exchange", name="exchange")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function createExchangeAction(Request $request)
    {
        $cantor = $this->getDoctrine()->
            getRepository('ExchangeBundle:Cantor')->find(1);
        $formCantorType = $this->createForm(CantorType::class, $cantor);
        $formCantorType->handleRequest($request);

        if ($formCantorType->isSubmitted() && $formCantorType->isValid()) {
            $post = $formCantorType->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $post->checkIsActive();
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('index');
        }

        return $this->render('ExchangeBundle:Main:exchange.html.twig', array(
            'form' => $formCantorType->createView()
        ));
    }

    /**
     * @Route("/editUser", name="editUser")
     * @Security("is_granted('ROLE_USER')")
     */
    public function changeUserInformationAction(Request $request)
    {
        $user = $this->getUser();
        $formEmail = $this->createForm(ChangeEmailType::class, $user);
        $formPassword = $this->createForm(ChangePasswordType::class, $user);
        $formEmail->handleRequest($request);
        $formPassword->handleRequest($request);

        if ($formEmail->isSubmitted() && $formEmail->isValid()) {
            $post = $formEmail->getData();
            $em = $this->getDoctrine()->getManager();
            $em->merge($post);
            $em->flush();
        }

        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            $post = $formPassword->getData();
            $em = $this->getDoctrine()->getManager();
            $em->merge($post);
            $em->flush();
        }

        return $this->render('@Exchange/Main/editUser.html.twig', array(
            'formEmail' => $formEmail->createView(),
            'formPassword' => $formPassword->createView()
        ));
    }
    
}
