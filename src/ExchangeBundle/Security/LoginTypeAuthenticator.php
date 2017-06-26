<?php
namespace ExchangeBundle\Security;

use Doctrine\ORM\EntityManager;
use ExchangeBundle\Form\LoginType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class LoginTypeAuthenticator extends AbstractFormLoginAuthenticator
{
    private $formFactory;
    private $entityManager;
    private $router;
    private $passwordEncoder;

    public function __construct(FormFactoryInterface $formFactory, EntityManager $entityManager,
        RouterInterface $router, UserPasswordEncoder $passwordEncoder)
    {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function getLoginUrl()
    {
        return $this->router->generate('login');
    }

    public function getCredentials(Request $request)
    {
        $isLoginSubmit = $request->getPathInfo() == '/login' && $request->isMethod('POST');

        if (!$isLoginSubmit) {
            return null;
        }

        $formLogin = $this->formFactory->create(LoginType::class);
        $formLogin->handleRequest($request);

        if ($formLogin->isValid()) {
            $data = $formLogin->getData();
            $request->getSession()->set(
                Security::LAST_USERNAME,
                $data['login']
            );
            return $data;
        }

        return null;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $login = $credentials['login'];

        return $this->entityManager->getRepository('ExchangeBundle:User')
            ->findOneBy(array(
                'login' => $login
            ));
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $password = $credentials['password'];

        if ($this->passwordEncoder->isPasswordValid($user, $password)) {
            return true;
        }

        return false;
    }

    protected function getDefaultSuccessRedirectUrl()
    {
        return $this->router->generate('index');
    }
}