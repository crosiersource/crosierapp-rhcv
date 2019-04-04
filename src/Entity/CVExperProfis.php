<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entidade CVExperProfis.
 * Registra as experiências profissionais do CV.
 *
 * @ORM\Table(name="rhcv_cv_exper_profis")
 * @ORM\Entity
 */
class CVExperProfis implements EntityId
{

    use EntityIdTrait;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\CV",inversedBy="experProfis")
     * @ORM\JoinColumn(name="cv_id", nullable=false)
     *
     * @var $cv CV
     */
    private $cv;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nome_empresa", type="string", length=100, nullable=true)
     */
    private $nomeEmpresa;

    /**
     * @var string|null
     *
     * @ORM\Column(name="local_empresa", type="string", length=100, nullable=true)
     */
    private $localEmpresa;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nome_superior", type="string", length=100, nullable=true)
     */
    private $nomeSuperior;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cargo", type="string", length=100, nullable=true)
     */
    private $cargo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="horario", type="string", length=100, nullable=true)
     */
    private $horario;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="admissao", type="date", nullable=true)
     */
    private $admissao;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="demissao", type="date", nullable=true)
     */
    private $demissao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ultimo_salario", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $ultimoSalario;

    /**
     * @var string|null
     *
     * @ORM\Column(name="beneficios", type="string", length=100, nullable=true)
     */
    private $beneficios;

    /**
     * @var string|null
     *
     * @ORM\Column(name="motivo_desligamento", type="string", length=3000, nullable=true)
     */
    private $motivoDesligamento;

    /**
     * @return CV
     */
    public function getCv(): CV
    {
        return $this->cv;
    }

    /**
     * @param CV $cv
     */
    public function setCv(CV $cv): void
    {
        $this->cv = $cv;
    }

    /**
     * @return null|string
     */
    public function getNomeEmpresa(): ?string
    {
        return $this->nomeEmpresa;
    }

    /**
     * @param null|string $nomeEmpresa
     */
    public function setNomeEmpresa(?string $nomeEmpresa): void
    {
        $this->nomeEmpresa = $nomeEmpresa;
    }

    /**
     * @return null|string
     */
    public function getLocalEmpresa(): ?string
    {
        return $this->localEmpresa;
    }

    /**
     * @param null|string $localEmpresa
     */
    public function setLocalEmpresa(?string $localEmpresa): void
    {
        $this->localEmpresa = $localEmpresa;
    }

    /**
     * @return null|string
     */
    public function getNomeSuperior(): ?string
    {
        return $this->nomeSuperior;
    }

    /**
     * @param null|string $nomeSuperior
     */
    public function setNomeSuperior(?string $nomeSuperior): void
    {
        $this->nomeSuperior = $nomeSuperior;
    }

    /**
     * @return null|string
     */
    public function getCargo(): ?string
    {
        return $this->cargo;
    }

    /**
     * @param null|string $cargo
     */
    public function setCargo(?string $cargo): void
    {
        $this->cargo = $cargo;
    }

    /**
     * @return null|string
     */
    public function getHorario(): ?string
    {
        return $this->horario;
    }

    /**
     * @param null|string $horario
     */
    public function setHorario(?string $horario): void
    {
        $this->horario = $horario;
    }

    /**
     * @return DateTime|null
     */
    public function getAdmissao(): ?DateTime
    {
        return $this->admissao;
    }

    /**
     * @param DateTime|null $admissao
     */
    public function setAdmissao(?DateTime $admissao): void
    {
        $this->admissao = $admissao;
    }

    /**
     * @return DateTime|null
     */
    public function getDemissao(): ?DateTime
    {
        return $this->demissao;
    }

    /**
     * @param DateTime|null $demissao
     */
    public function setDemissao(?DateTime $demissao): void
    {
        $this->demissao = $demissao;
    }

    /**
     * @return null|string
     */
    public function getUltimoSalario(): ?string
    {
        return $this->ultimoSalario;
    }

    /**
     * @param null|string $ultimoSalario
     */
    public function setUltimoSalario(?string $ultimoSalario): void
    {
        $this->ultimoSalario = $ultimoSalario;
    }

    /**
     * @return null|string
     */
    public function getBeneficios(): ?string
    {
        return $this->beneficios;
    }

    /**
     * @param null|string $beneficios
     */
    public function setBeneficios(?string $beneficios): void
    {
        $this->beneficios = $beneficios;
    }

    /**
     * @return null|string
     */
    public function getMotivoDesligamento(): ?string
    {
        return $this->motivoDesligamento;
    }

    /**
     * @param null|string $motivoDesligamento
     */
    public function setMotivoDesligamento(?string $motivoDesligamento): void
    {
        $this->motivoDesligamento = $motivoDesligamento;
    }


}
