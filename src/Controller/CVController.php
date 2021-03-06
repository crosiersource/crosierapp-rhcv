<?php

namespace App\Controller;

use App\Business\CVBusiness;
use App\Entity\Cargo;
use App\Entity\CV;
use App\Form\CVAvaliaType;
use CrosierSource\CrosierLibBaseBundle\Controller\BaseController;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\WhereBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller CRUD para a entidade CV.
 * @package App\Controller\CV
 * @author Carlos Eduardo Pauluk
 */
class CVController extends BaseController
{

    private CVBusiness $cvBusiness;

    /**
     * @required
     * @param CVBusiness $cvBusiness
     */
    public function setCvBusiness(CVBusiness $cvBusiness): void
    {
        $this->cvBusiness = $cvBusiness;
    }


    public function getFilterDatas(array $params): array
    {
        return [
            new FilterData(['nome'], 'LIKE', 'nome', $params),
            new FilterData(['updated'], 'BETWEEN_DATE', 'updated', $params),
            new FilterData(['dtNascimento'], 'BETWEEN_IDADE', 'idade', $params),
            new FilterData(['status'], 'IN', 'status', $params),
            new FilterData(['cargosPretendidos'], 'IN', 'cargosPretendidos', $params),
            new FilterData(['temFilhos'], 'EQ', 'temFilhos', $params),
            new FilterData(['atual'], 'EQ', 'atual', $params),
            new FilterData(['sexo'], 'IN', 'sexo', $params),
            new FilterData(['emailConfirmado'], 'EQ', 'emailConfirmado', $params),
            new FilterData(['nome'], 'IS_NOT_NULL', 'nomeIsNotNull', $params),
        ];
    }

    /**
     *
     * @Route("/cv/list/", name="cv_list")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function list(Request $request): Response
    {
        $params['filter'] = $request->get('filter');
        // Fixos
        $params['filter']['atual'] = true;
        $params['filter']['emailConfirmado'] = 'S';
        $params['filter']['nomeIsNotNull'] = true;

        $params['filter']['status'] = $request->get('filter')['status'] ?? ['FECHADO'];

        $filterDatas = $this->getFilterDatas($params);

        /** @var FilterRepository $repo */
        $repo = $this->getDoctrine()->getRepository(CV::class);
        $dados = $repo->findByFilters($filterDatas, ['e.updated' => 'DESC'], 0, null);

        $vParams = [];
        $vParams['dados'] = $dados;
        $vParams['page_title'] = 'CVs';
        $vParams['filterChoices'] = $this->getFilterChoices();
        $vParams['filter'] = $params['filter'];

        return $this->doRender('cvList.html.twig', $vParams);
    }

    /**
     * Constrói os valores para os campos de filtros.
     *
     * @return array
     */
    protected function getFilterChoices(): array
    {
        $filterChoices = [];

        /** @var CarteiraRepository $repoCarteira */
        $repoCargo = $this->getDoctrine()->getRepository(Cargo::class);
        $cargos = $repoCargo->findAll(WhereBuilder::buildOrderBy('cargo'));
        $filterChoices['cargos'] = $cargos;

        $filterChoices['status'] = [
            'ABERTO',
            'FECHADO',
            'APROVADO',
            'REPROVADO',
        ];


        return $filterChoices;
    }


    /**
     * Form para preenchimento do CV.
     *
     * @Route("/cv/avalia/{cv}", name="cv_avalia", defaults={"cv"=null}, requirements={"cv"="\d+"})
     * @param Request $request
     * @param CV $cv
     * @return Response
     * @throws \Exception
     */
    public function avaliar(Request $request, CV $cv): Response
    {
        $vParams = [];

        $form = $this->createForm(CVAvaliaType::class, $cv);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $this->cvBusiness->getCvEntityHandler()->save($cv);
                    $this->getDoctrine()->getManager()->refresh($cv);
                    $form = $this->createForm(CVAvaliaType::class, $cv);
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

        // Pode ou não ter vindo algo no $parameters. Independentemente disto, só adiciono form e foi-se.
        $vParams['form'] = $form->createView();
        $vParams['status'] = $cv->getStatus();
        $vParams['cv'] = $cv;


        return $this->doRender('avalia.html.twig', $vParams);
    }


    /**
     * Form para preenchimento do CV.
     *
     * @Route("/cv/reenviarEmails", name="cv_reenviarEmails")
     * @return Response
     * @throws \Exception
     */
    public function reenviarEmails(): Response
    {
        $this->cvBusiness->reenviarEmailsNaoConfirmados();
        return new Response('foi');

    }

    /**
     * Avisar foto faltando.
     *
     * @Route("/cv/avisarFotoFaltando", name="cv_avisaFotoFaltando")
     * @return Response
     */
    public function avisarFotoFaltando(): Response
    {
        $qtdeEnviados = $this->cvBusiness->avisarFotoFaltando();
        return new Response('foi avisarFotoFaltando: [' . $qtdeEnviados . ']');

    }


    /**
     * Avisar 'não fechado'.
     *
     * @Route("/cv/avisarNaoFechado", name="cv_avisarNaoFechado ")
     * @return Response
     */
    public function avisarNaoFechado(): Response
    {
        $qtdeEnviados = $this->cvBusiness->avisarNaoFechado();
        return new Response('foi avisarNaoFechado: [' . $qtdeEnviados . ']');

    }


    /**
     * Form para preenchimento do CV.
     *
     * @Route("/cv/testarEmail", name="cv_testarEmail")
     * @return Response
     * @throws \Exception
     */
    public function testarEmail(): Response
    {
        $this->cvBusiness->testarEmail();
        return new Response('foi p/ carlospauluk@gmail.com');

    }


}