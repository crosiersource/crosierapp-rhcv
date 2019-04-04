<?php

namespace App\Business;

use App\Entity\CV;
use App\Entity\CVExperProfis;
use App\Entity\CVFilho;
use App\EntityHandler\CVEntityHandler;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\StringUtils\StringUtils;
use Doctrine\ORM\ORMException;
use Swift_Mailer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class CVBusiness
 * @package App\Business
 * @author Carlos Eduardo Pauluk
 */
class CVBusiness extends BaseBusiness
{

    /** @var Swift_Mailer */
    private $swiftMailer;

    /** @var ContainerInterface */
    private $container;

    /** @var UserPasswordEncoderInterface */
    private $userPasswordEncoder;

    /** @var CVEntityHandler */
    private $cvEntityHandler;

    /**
     * @required
     * @param mixed $swiftMailer
     */
    public function setSwiftMailer(Swift_Mailer $swiftMailer): void
    {
        $this->swiftMailer = $swiftMailer;
    }

    /**
     * @required
     * @param mixed $container
     */
    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    /**
     * @required
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function setUserPasswordEncoder(UserPasswordEncoderInterface $userPasswordEncoder): void
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * @required
     * @param CVEntityHandler $cvEntityHandler
     */
    public function setCvEntityHandler(CVEntityHandler $cvEntityHandler): void
    {
        $this->cvEntityHandler = $cvEntityHandler;
    }

    /**
     * @return CVEntityHandler
     */
    public function getCvEntityHandler(): CVEntityHandler
    {
        return $this->cvEntityHandler;
    }


    /**
     * Verifica se o e-mail já foi confirmado.
     *
     * @param $cpf
     * @return bool
     * @throws ViewException
     */
    public function checkEmailConfirmado($cpf): bool
    {
        /** @var CV $cv */
        $cv = $this->getDoctrine()->getRepository(CV::class)->findOneBy(['cpf' => $cpf]);
        if ($cv) {
            if ($cv->getEmailConfirmado() === 'S') {
                return true;
            }
            if ($this->enviarEmailNovo($cv)) {
                throw new ViewException('Seu e-mail ainda não foi confirmado. Verifique sua Caixa de Entrada ou Spam. Um novo e-mail foi enviado.');
            }
            // else
            throw new ViewException('Ocorreu um erro ao enviar o e-mail de confirmação. Por favor, entre em contato através do e-mail casabonsucesso@gmail.com .');

        }
        return false;

    }

    /**
     * Salva o CPF, e-mail e a senha criptografada e envia o e-mail para confirmação.
     *
     * @param $cpf
     * @param $email
     * @param $senha
     * @throws \Exception
     */
    public function novo($cpf, $email, $senha): void
    {
        try {
            $this->getDoctrine()->getEntityManager()->beginTransaction();
            $cv = $this->getDoctrine()->getRepository(CV::class)->findOneBy(['cpf' => $cpf]);
            if ($cv) {
                throw new \RuntimeException('CPF já cadastrado');
            }
            $cv = $this->getDoctrine()->getRepository(CV::class)->findOneBy(['email' => $email]);
            if ($cv) {
                throw new \RuntimeException('E-mail já cadastrado');
            }
            $cv = new CV();
            $cv->setCpf($cpf);
            $cv->setEmail($email);

            $hashed = $this->userPasswordEncoder->encodePassword($cv, $senha);
            $cv->setSenha($hashed);
            $cv->setEmailConfirmado('N');
            $cv->setEmailConfirmUUID(md5(uniqid(mt_rand(), true)));
            try {
                $this->cvEntityHandler->save($cv);
            } catch (ORMException $e) {
                throw new \RuntimeException('Erro ao salvar novo registro', 0, $e);
            }
            if (!$this->enviarEmailNovo($cv)) {
                throw new \RuntimeException('Não foi possível enviar o e-mail de confirmação.');
            }
            $this->getDoctrine()->getEntityManager()->commit();
        } catch (\Exception $e) {
            $this->getDoctrine()->getEntityManager()->rollback();
            throw $e;
        }
    }

    /**
     * @param CV $cv
     * @return int
     */
    public function enviarEmailNovo(CV $cv): int
    {
        $link = $_SERVER['LINK_CONFIRM_EMAIL'] . '?cv=' . $cv->getId() . '&uuid=' . $cv->getEmailConfirmUUID();
        $body = $this->container->get('twig')->render('cvForm/emailConfirm.html.twig', ['link' => $link]);
        $message = (new \Swift_Message('Confirmação de cadastro.'))
            ->setFrom('mailer@casabonsucesso.com.br', 'Casa Bonsucesso')
            ->setSubject('Confirmação de Cadastro - Cadastro de Currículos')
            ->setTo($cv->getEmail())
            ->setBody($body, 'text/html');
        return $this->swiftMailer->send($message);
    }

    /**
     * @param $cpf
     * @return int
     * @throws \Exception
     */
    public function reenviarSenha($cpf): int
    {
        /** @var CV $cv */
        $cvs = $this->getDoctrine()->getRepository(CV::class)->findBy(['cpf' => $cpf], ['versao' => 'DESC']);
        $cv = $cvs ? $cvs[0] : null;
        if (!$cv) {
            throw new \RuntimeException('Cadastro não encontrado.');
        }
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = random_int(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        $novaSenhaTemp = implode($pass); //turn the array into a string

        $passwordEncoder = $this->userPasswordEncoder; //new Pbkdf2PasswordEncoder();
        $hashed = $passwordEncoder->encodePassword($cv, $novaSenhaTemp);
        $cv->setSenhaTemp($hashed);
        $cv->setUpdated(new \DateTime());
        $this->getDoctrine()->getEntityManager()->flush();


        $body = $this->container->get('twig')->render('cvForm/emailNovaSenha.html.twig', ['novaSenha' => $novaSenhaTemp]);
        $message = (new \Swift_Message())
            ->setFrom('mailer@casabonsucesso.com.br', 'Casa Bonsucesso')
            ->setSubject('Cadastro de Currículos - Nova Senha')
            ->setTo($cv->getEmail())
            ->setBody($body, 'text/html');
        return $this->swiftMailer->send($message);
    }

    /**
     * Confirma o e-mail do usuário.
     *
     * @param $cvId
     * @param $uuid
     * @return null|CV
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function confirmEmail($cvId, $uuid): ?CV
    {
        /** @var CV $cv */
        $cv = $this->getDoctrine()->getRepository(CV::class)->find($cvId);
        if (!$cv || $cv->getEmailConfirmUUID() !== $uuid) {
            return null;
        }
        $cv->setEmailConfirmado('S');
        $this->getDoctrine()->getEntityManager()->flush();
        return $cv;
    }

    /**
     * @param CV $cv
     * @param $senhaAtual
     * @param $novaSenha
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     */
    public function alterarSenha(CV $cv, $senhaAtual, $novaSenha): void
    {
        $passwordEncoder = $this->userPasswordEncoder;
        if (!$passwordEncoder->isPasswordValid($cv, $senhaAtual)) {
            throw new \RuntimeException('Senha atual inválida.');
        }

        $novaSenhaHash = $passwordEncoder->encodePassword($cv, $novaSenha);
        $cv->setSenha($novaSenhaHash);
        $this->cvEntityHandler->save($cv);
    }

    /**
     * Seta o campo status = 'FECHADO' para não permitir mais edições.
     *
     * @param CV $cv
     * @return bool
     * @throws \Exception
     */
    public function fechar(CV $cv): ?bool
    {
        $cv->setStatus('FECHADO');
        $this->getCvEntityHandler()->save($cv);
        return true;
    }

    /**
     * Cria uma nova versão para poder editar novamente.
     *
     * @param CV $cv
     * @return bool
     * @throws \Exception
     */
    public function versionar(CV $cv): ?bool
    {
        try {
            // Se já estiver aberto, não tem pq versionar
            if ($cv->getStatus() !== 'FECHADO') {
                return false;
            }

            // Verifica qual é o último CV. Se ainda estiver aberto, não tem pq versionar.
            /** @var CV $ultimoCv */
            $ultimoCv = $this->getDoctrine()->getRepository(CV::class)->findBy(['cpf' => $cv->getCpf()], ['versao' => 'DESC']);
            if (!$ultimoCv) {
                throw new \RuntimeException('Cadastro não encontrado.');
            }
            $ultimoCv = $ultimoCv[0];
            if ($ultimoCv->getStatus() !== 'FECHADO') {
                return false;
            }

            $cv = $ultimoCv;
            $cv->setAtual(false);

            $novoCv = clone $cv;
            $novoCv->setAtual(true);
            $novoCv->setId(null);
            $novoCv->setUpdated(new \DateTime());
            $novoCv->setStatus('ABERTO');
            $novoCv->setVersao($cv->getVersao() + 1);
            $filhos = clone $cv->getFilhos();
            $experProfi = clone $cv->getExperProfis();
            $filhos->clear();
            $experProfi->clear();
            $novoCv->setFilhos($filhos);
            $novoCv->setExperProfis($experProfi);

            $this->getDoctrine()->getEntityManager()->persist($novoCv);

            foreach ($cv->getFilhos() as $filho) {
                $novoFilho = clone $filho;
                $novoFilho->setCv($novoCv);
                $novoFilho->setInserted(new \DateTime());
                $novoFilho->setUpdated(new \DateTime());
                $this->getDoctrine()->getEntityManager()->persist($novoFilho);
            }
            foreach ($cv->getExperProfis() as $experProfi) {
                $novaExperProfi = clone $experProfi;
                $novaExperProfi->setCv($novoCv);
                $novaExperProfi->setInserted(new \DateTime());
                $novaExperProfi->setUpdated(new \DateTime());
                $this->getDoctrine()->getEntityManager()->persist($novaExperProfi);
            }

            $this->getDoctrine()->getEntityManager()->flush();

            return true;

        } catch (ORMException $e) {
            throw new \RuntimeException('Erro ao gerar nova versão. Por favor, informe este problema através do e-mail casabonsucesso@gmail.com .');
        }
    }

    /**
     * @param CV $cv
     * @param $arrFilhos
     * @return CV
     * @throws \Exception
     */
    public function saveFilhos(CV $cv, $arrFilhos): ?CV
    {
        try {
            if ($cv->getTemFilhos() === 'N') {
                $cv->setQtdeFilhos(null);
                $cv->getFilhos()->clear();
                $this->getDoctrine()->getEntityManager()->flush();
                return $cv;
            }

            if ($arrFilhos && count($arrFilhos) > 0) {
                $cv->getFilhos()->clear();
                $this->getDoctrine()->getEntityManager()->flush();
                foreach ($arrFilhos as $filho) {
                    if (!$filho['nome']) {
                        continue;
                    }
                    $cvFilho = new CVFilho();
                    $cvFilho->setCv($cv);
                    $cvFilho->setNome($filho['nome']);
                    $cvFilho->setDtNascimento($filho['dtNascimento'] ? \DateTime::createFromFormat('Y-m-d', $filho['dtNascimento']) : null);
                    $cvFilho->setOcupacao($filho['ocupacao']);
                    $cvFilho->setObs($filho['obs']);

                    // Aqui, o usuário logado não é da tabela sec_user
                    $cvFilho->setUserUpdatedId(1);
                    $cvFilho->setUserInsertedId(1);
                    $cvFilho->setEstabelecimentoId(1);
                    $cvFilho->setInserted(new \DateTime('now'));
                    $cvFilho->setUpdated(new \DateTime('now'));

                    $this->getDoctrine()->getEntityManager()->persist($cvFilho);
                }
                $this->getDoctrine()->getEntityManager()->flush();
                $this->getDoctrine()->getEntityManager()->refresh($cv);
                return $cv;
            }
            return null;
        } catch (ORMException $e) {
            throw new \RuntimeException('Erro ao salvar dados dos filhos.');
        }
    }

    /**
     *
     * @param CV $cv
     * @return false|string
     */
    public function dadosFilhos2JSON(CV $cv)
    {
        $dadosFilhosJSON = [];
        if ($cv && $cv->getFilhos()) {
            /** @var CVFilho $filho */
            foreach ($cv->getFilhos() as $filho) {
                $d['nome'] = $filho->getNome();
                $d['dtNascimento'] = $filho->getDtNascimento() ? $filho->getDtNascimento()->format('Y-m-d') : null;
                $d['ocupacao'] = $filho->getOcupacao();
                $d['obs'] = $filho->getObs();
                $dadosFilhosJSON[] = $d;
            }
        }
        return json_encode($dadosFilhosJSON);
    }

    /**
     * @param CV $cv
     * @param $arrEmpregos
     * @return CV
     * @throws \Exception
     */
    public function saveEmpregos(CV $cv, $arrEmpregos): ?CV
    {
        try {
            if ($arrEmpregos && count($arrEmpregos) > 0) {
                $cv->getExperProfis()->clear();
                $this->getDoctrine()->getEntityManager()->flush();

                foreach ($arrEmpregos as $emprego) {
                    if ($emprego['nomeEmpresa']) {
                        $cvExperProfiss = new CVExperProfis();
                        $cvExperProfiss->setCv($cv);
                        $cvExperProfiss->setNomeEmpresa($emprego['nomeEmpresa']);
                        $cvExperProfiss->setLocalEmpresa($emprego['localEmpresa']);
                        $cvExperProfiss->setNomeSuperior($emprego['nomeSuperior']);
                        $cvExperProfiss->setCargo($emprego['cargo']);
                        $cvExperProfiss->setHorario($emprego['horario']);


                        $cvExperProfiss->setAdmissao(DateTimeUtils::parseDateStr($emprego['admissao']));
                        $cvExperProfiss->setDemissao(DateTimeUtils::parseDateStr($emprego['demissao']));
                        $cvExperProfiss->setUltimoSalario(StringUtils::parseFloat($emprego['ultimoSalario']));
                        $cvExperProfiss->setBeneficios($emprego['beneficios']);
                        $cvExperProfiss->setMotivoDesligamento($emprego['motivoDesligamento']);

                        // Aqui, o usuário logado não é da tabela sec_user
                        $cvExperProfiss->setUserUpdatedId(1);
                        $cvExperProfiss->setUserInsertedId(1);
                        $cvExperProfiss->setEstabelecimentoId(1);
                        $cvExperProfiss->setInserted(new \DateTime('now'));
                        $cvExperProfiss->setUpdated(new \DateTime('now'));

                        $this->getDoctrine()->getEntityManager()->persist($cvExperProfiss);
                        $this->getDoctrine()->getEntityManager()->flush();
                    }
                }
                $this->getDoctrine()->getEntityManager()->refresh($cv);
                return $cv;
            }
            return null;
        } catch (ORMException $e) {
            throw new \RuntimeException('Erro ao salvar dados dos filhos.');
        }
    }

    /**
     *
     * @param CV $cv
     * @return false|string
     */
    public function dadosEmpregos2JSON(CV $cv)
    {
        $dadosEmpregosJSON = [];
        if ($cv && $cv->getExperProfis()) {
            foreach ($cv->getExperProfis() as $emprego) {
                $d['nomeEmpresa'] = $emprego->getNomeEmpresa();
                $d['localEmpresa'] = $emprego->getLocalEmpresa();
                $d['nomeSuperior'] = $emprego->getNomeSuperior();
                $d['horario'] = $emprego->getHorario();
                $d['cargo'] = $emprego->getCargo();
                $d['admissao'] = $emprego->getAdmissao() instanceof \DateTime ? $emprego->getAdmissao()->format('d/m/Y') : '';
                $d['demissao'] = $emprego->getDemissao() instanceof \DateTime ? $emprego->getDemissao()->format('d/m/Y') : '';
                $d['ultimoSalario'] = $emprego->getUltimoSalario();
                $d['beneficios'] = $emprego->getBeneficios();
                $d['motivoDesligamento'] = $emprego->getMotivoDesligamento();
                $dadosEmpregosJSON[] = $d;
            }
        }
        return json_encode($dadosEmpregosJSON);
    }


}