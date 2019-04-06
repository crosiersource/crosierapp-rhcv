<?php

namespace App\Controller;

use App\Business\CVBusiness;
use App\Entity\CV;
use App\Form\CVType;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\StringUtils\ValidaCPFCNPJ;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Log\LoggerInterface;
use ReCaptcha\ReCaptcha;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Handler\UploadHandler;

/**
 * Class CVFormController.
 *
 * Cuida do workflow de preenchimento de CV.
 * Passos:
 *  - checkCPF
 *  - i
 *  - emailConfirmEnviado
 *  - login
 *
 * @package App\Controller
 * @author Carlos Eduardo Pauluk
 */
class CVFormController extends AbstractController
{

    /** @var LoggerInterface */
    private $logger;

    /** @var CVBusiness */
    private $cvBusiness;

    /** @var SessionInterface */
    private $session;

    /** @var UploadHandler */
    private $uploadHandler;

    /**
     * @required
     * @param mixed $cvBusiness
     */
    public function setCvBusiness(CVBusiness $cvBusiness): void
    {
        $this->cvBusiness = $cvBusiness;
    }

    /**
     * @required
     * @param mixed $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @required
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session): void
    {
        $this->session = $session;
    }

    /**
     * @required
     * @param UploadHandler $uploadHandler
     */
    public function setUploadHandler(UploadHandler $uploadHandler): void
    {
        $this->uploadHandler = $uploadHandler;
    }


    /**
     * Formulário apenas com o CPF. Verifica se já existe e redireciona.
     *
     * @Route("/cvForm/checkCPF", name="cvForm_checkCPF")
     * @param Request $request
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ViewException
     */
    public function checkCPF(Request $request): Response
    {
        $vParams = [];
        $vParams['cpf'] = preg_replace('/[^\d]/', '', $request->get('cpf') ?? $this->session->get('cpf'));

        if ($vParams['cpf']) {
            $submittedToken = $request->request->get('_csrf_token');
            if ($submittedToken && !$this->isCsrfTokenValid('checkCPF', $submittedToken)) {
                $this->addFlash('error', 'Erro de submissão');
            } elseif (!ValidaCPFCNPJ::validaCPF($vParams['cpf'])) {
                $this->addFlash('error', 'CPF inválido');
            } elseif (!$_SERVER['APP_ENV'] === 'dev' && !$request->get('g-recaptcha-response')) {
                $this->addFlash('error', 'Você é um robô?');
            } else {
                if (!$_SERVER['APP_ENV'] === 'dev') {
                    $secret = $_SERVER['GOOGLE_RECAPTCHA_SECRET'];
                    $gRecaptchaResponse = $request->get('g-recaptcha-response');
                    $recaptcha = new ReCaptcha($secret);
                    $urlSistema = $_SERVER['URL_SISTEMA'];
                    $resp = $recaptcha->setExpectedHostname($urlSistema)
                        ->verify($gRecaptchaResponse, $request->server->get('REMOTE_ADDR'));
                    if (!$resp->isSuccess()) {
                        throw new ViewException('Você é um robô ou não??');
                    }
                }

                $this->session->set('cpf', $vParams['cpf']);
                /** @var CV $cv */
                $cv = $this->cvBusiness->getDoctrine()->getRepository(CV::class)->findBy(['cpf' => $vParams['cpf']]);
                // Se não tem cadastro, redireciona para a página de cadastro
                if (!$cv) {
                    return $this->redirectToRoute('cvForm_novo');
                }
                // Se já confirmou o e-mail, redireciona para a página do CV (que, caso não esteja logado, irá para a login)
                try {
                    if ($this->cvBusiness->checkEmailConfirmado($vParams['cpf'])) {
                        return $this->redirectToRoute('cvForm_cv');
                    }
                } catch (ViewException $e) {
                    $this->addFlash('warn', $e->getMessage());
                }
            }
        }

        return $this->render('cvForm/checkCPF.html.twig', $vParams);
    }

    /**
     *
     * @Route("/cvForm/login", name="cvForm_login")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(): Response
    {
        if ($this->session->has('_security.last_error')) {
            $this->addFlash('error', $this->session->get('_security.last_error')->getMessage());
        }
        $cpf = $this->session->get('cpf');
        return $this->render('cvForm/login.html.twig', ['cpf' => $cpf]);
    }

    /**
     *
     * @Route("/cvForm/esqueciMinhaSenha", name="cvForm_esqueciMinhaSenha")
     * @param $vParams
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function esqueciMinhaSenha(Request $request): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        try {
            $cpf = $this->session->get('cpf');
            $this->cvBusiness->reenviarSenha($cpf);
            $this->addFlash('success', 'Nova senha enviada para seu e-mail.');
            $this->addFlash('info', 'Verifique sua Caixa de Entrada e também o "Spam".');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erro ao gerar nova senha.');
        }
        return $this->redirectToRoute('cvForm_login');
    }

    /**
     *
     * @Route("/cvForm/novo", name="cvForm_novo")
     * @param Request $request
     * @return Response
     */
    public function novo(Request $request): Response
    {
        $vParams = [];
        $vParams['cpf'] = preg_replace('/[^\d]/', '', $this->session->get('cpf'));
        $vParams['email'] = $request->get('email');
        $vParams['password'] = $request->get('password');
        $vParams['password2'] = $request->get('password2');
        $vParams['email'] = $request->get('email');
        $vParams['password_login'] = $request->get('password_login');

        if ($request->get('btnNovo')) {
            $submittedToken = $request->request->get('_csrf_token');

            if ($submittedToken && !$this->isCsrfTokenValid('novo', $submittedToken)) {
                $this->addFlash('error', 'Erro de submissão');

            } else {

                if ($vParams['password'] !== $vParams['password2']) {
                    $this->addFlash('error', 'As senhas não coincidem.');
                } else {
                    try {
                        $this->cvBusiness->novo($vParams['cpf'], $vParams['email'], $vParams['password']);
                        $vParams['cadastroIniciado'] = true;
                    } catch (\Exception $e) {
                        $this->addFlash('error', $e->getMessage());
                    }
                }

                if (isset($vParams['cadastroIniciado']) && $vParams['cadastroIniciado']) {
                    $this->addFlash('info', 'O e-mail foi enviado. Verifique sua Caixa de Entrada ou o Spam.');
                    return $this->redirectToRoute('cvForm_checkCPF');
                }
            }
        }


        return $this->render('cvForm/novo.html.twig', $vParams);
    }


    /**
     * Chamado a partir do link que é enviado para o e-mail.
     *
     * @Route("/cvForm/confirmEmail", name="cvForm_confirmEmail")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function confirmEmail(Request $request): Response
    {
        $cvId = $request->get('cv');
        $uuid = $request->get('uuid');

        try {
            $cv = $this->cvBusiness->confirmEmail($cvId, $uuid);
            if (!$cv) {
                $this->addFlash('error', 'Não foi possível confirmar seu e-mail.');
            } else {
                $this->addFlash('info', 'E-mail confirmado com sucesso. Efetue o login...');
                return $this->redirectToRoute('cvForm_login', $request->request->all());
            }
        } catch (\Exception $e) {
            $this->logger->error('Não foi possível confirmar o e-mail');
            $this->logger->error($e->getMessage());
            $this->addFlash('error', 'Não foi possível confirmar seu e-mail.');
        }

        return $this->redirectToRoute('cvForm_checkCPF');

    }

    /**
     * Para não precisar deslogar e logar novamente quando ocorre um versionamento.
     *
     * @return CV
     */
    public function getUser(): CV
    {
        /** @var CV $logged */
        $logged = parent::getUser();
        return $this->getDoctrine()->getRepository(CV::class)->findOneBy(['cpf' => $logged->getCpf()], ['versao' => 'DESC']);
    }


    /**
     * Form para preenchimento do CV.
     *
     * @Route("/cvForm/cv", name="cvForm_cv")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cv(Request $request): Response
    {
        $vParams = [];

        /** @var CV $cv */
        $cv = $this->getUser();

        $form = $this->createForm(CVType::class, $cv);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($cv->getStatus() === 'FECHADO') {
                $this->addFlash('error', 'CV fechado. Para editar, clique no botão "Abrir para edição" no topo da página.');
            } else {
                if ($form->isValid()) {
                    try {
                        $this->cvBusiness->getCvEntityHandler()->save($cv);
                        $this->cvBusiness->saveFilhos($cv, $request->get('filho'));
                        $this->cvBusiness->saveEmpregos($cv, $request->get('emprego'));
                        $this->getDoctrine()->getManager()->refresh($cv);
                        $form = $this->createForm(CVType::class, $cv);
                        $this->addFlash('success', 'Registro salvo com sucesso!');
                    } catch (\Throwable $e) {
                        $this->addFlash('error', 'Erro ao salvar!');
                    }
                } else {
                    $errors = $form->getErrors(true, true);
                    foreach ($errors as $error) {
                        $this->addFlash('error', $error->getMessage());
                    }
                }
            }
        }

        // Pode ou não ter vindo algo no $parameters. Independentemente disto, só adiciono form e foi-se.
        $vParams['form'] = $form->createView();
        $vParams['status'] = $cv->getStatus();
        $vParams['cv'] = $cv;
        $vParams['modoExibicao'] = false;

        $vParams['dadosFilhosJSON'] = $this->cvBusiness->dadosFilhos2JSON($cv);
        $vParams['dadosEmpregosJSON'] = $this->cvBusiness->dadosEmpregos2JSON($cv);

        return $this->render('cvForm/cv.html.twig', $vParams);
    }

    /**
     * "Fecha" a versão do CV.
     *
     * @Route("/cvForm/fechar", name="cvForm_fechar")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fechar(Request $request): Response
    {
        try {
            /** @var CV $cv */
            $cv = $this->getUser();
            $this->cvBusiness->fechar($cv);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erro ao finalizar.');
        }
        return $this->redirectToRoute('cvForm_cv');
    }

    /**
     * Abre uma nova versão do CV.
     *
     * @Route("/cvForm/versionar", name="cvForm_versionar")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function versionar(Request $request): Response
    {
        try {
            /** @var CV $cv */
            $cv = $this->getUser();
            if (!$this->cvBusiness->versionar($cv)) {
                $this->addFlash('warn', 'Não é possível versionar.');
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erro ao modificar.');
        }
        return $this->redirectToRoute('cvForm_cv');
    }

    /**
     * Form para alteração de senha.
     *
     * @Route("/cvForm/alterarSenha", name="cvForm_alterarSenha")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\ORMEx ception
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     */
    public function alterarSenha(Request $request): Response
    {

        $vParams = [];

        if ($request->get('btnAlterarSenha')) {
            $senhaAtual = $request->request->get('password_login');
            $password = $request->request->get('password');
            $password2 = $request->request->get('password2');
            if ($password !== $password2) {
                $this->addFlash('error', 'As senhas não coincidem.');
            } else {
                $this->cvBusiness->alterarSenha($this->getUser(), $senhaAtual, $password);
                $this->addFlash('info', 'Senha alterada com sucesso!');
                return $this->redirectToRoute('cvForm_logout');
            }
        }

        return $this->render('cvForm/alterarSenha.html.twig', $vParams);
    }

    /**
     *
     * @Route("/cvForm/uploadFoto", name="cvForm_uploadFoto")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function uploadFoto(Request $request): JsonResponse
    {
        $output = ['uploaded' => false];
        if ($request->files->get('file')) {
            /** @var CV $cv */
            $cv = $this->getUser();
            $cv->setFotoFile($request->files->get('file'));
            $this->cvBusiness->getCvEntityHandler()->save($cv);
            $output['uploaded'] = true;
        }
        return new JsonResponse($output);

    }

    /**
     *
     * @Route("/cvForm/deleteFoto", name="cvForm_deleteFoto")
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteFoto(Request $request): RedirectResponse
    {
        try {
            $cv = $this->getUser();
            $this->uploadHandler->remove($cv, 'fotoFile'); // https://github.com/dustin10/VichUploaderBundle/issues/323
            $cv->setUpdated(new \DateTime());
            $cv->setFoto(null);
            $cv->setFotoFile(null);
            $this->cvBusiness->getCvEntityHandler()->save($cv);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Ocorreu um erro ao remover a foto.');
        }
        return $this->redirectToRoute('cvForm_cv');
    }

    /**
     *
     * @Route("/cvForm/logout", name="cvForm_logout")
     */
    public function logout(): void
    {
        // apenas para declara a route
    }


}