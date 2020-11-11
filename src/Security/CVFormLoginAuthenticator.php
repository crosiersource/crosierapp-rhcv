<?php

namespace App\Security;

use App\EntityHandler\CVEntityHandler;
use App\Repository\CVRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Class LoginFormAuthenticator.
 *
 * Authenticador padrão para o formulário de login do crosier.
 *
 * @author Carlos Eduardo Pauluk
 * @package App\Security
 */
class CVFormLoginAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    /** @var CVRepository */
    private $cvRepository;

    /** @var RouterInterface */
    private $router;

    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var CVEntityHandler */
    private $cvEntityHandler;

    /** @var LoggerInterface */
    private $logger;

    /** @var SessionInterface */
    private $session;

    public function __construct(CVRepository $cvRepository,
                                RouterInterface $router,
                                CsrfTokenManagerInterface $csrfTokenManager,
                                UserPasswordEncoderInterface $passwordEncoder,
                                CVEntityHandler $cvEntityHandler,
                                LoggerInterface $logger,
                                SessionInterface $session)
    {
        $this->cvRepository = $cvRepository;
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->cvEntityHandler = $cvEntityHandler;
        $this->logger = $logger;
        $this->session = $session;
    }

    public function supports(Request $request)
    {
        $this->logger->debug('LoginFormAuthenticator supports?');
        // do your work when we're POSTing to the login page
        return $request->attributes->get('_route') === 'cvForm_login'
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'cpf' => preg_replace('/[^\d]/', '', $request->get('cpf')),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['cpf']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('login', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $cvs = $this->cvRepository->findBy(['cpf' => $credentials['cpf']], ['versao' => 'DESC']);
        return $cvs ? $cvs[0] : null;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // $user->setSenha($user->getSenhaTemp());
        if (!$this->passwordEncoder->isPasswordValid($user, $credentials['password'])) {
            $user->setSenha($user->getSenhaTemp());
            $validou = $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
            if ($validou) {
                $user->setSenhaTemp(null);
                $this->cvEntityHandler->save($user);
                $this->session->set('cvId', $user->getId()); // o cvId no session define o status de logado
                return true;
            } else {
                // return false;
                throw new AuthenticationException('Senha inválida.');
            }
        }
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }
        return new RedirectResponse($this->router->generate('index'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return parent::onAuthenticationFailure($request, $exception); // TODO: Change the autogenerated stub
    }


    protected function getLoginUrl()
    {
        return $this->router->generate('cvForm_login');
    }


}
