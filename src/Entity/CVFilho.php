<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * CVFilho.
 *
 * Registra os filhos para o CV.
 *
 * @ORM\Table(name="rhcv_cv_filho")
 * @ORM\Entity
 */
class CVFilho implements EntityId
{

    use EntityIdTrait;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\CV",inversedBy="filhos")
     * @ORM\JoinColumn(name="cv_id", nullable=false)
     *
     * @var $cv CV
     */
    private $cv;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nome", type="string", length=100, nullable=true)
     */
    private $nome;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dt_nascimento", type="datetime", nullable=true)
     */
    private $dtNascimento;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ocupacao", type="string", length=100, nullable=true)
     */
    private $ocupacao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="obs", type="string", length=3000, nullable=true)
     */
    private $obs;


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
    public function getNome(): ?string
    {
        return $this->nome;
    }

    /**
     * @param null|string $nome
     */
    public function setNome(?string $nome): void
    {
        $this->nome = $nome;
    }

    /**
     * @return DateTime|null
     */
    public function getDtNascimento(): ?DateTime
    {
        return $this->dtNascimento;
    }

    /**
     * @param DateTime|null $dtNascimento
     */
    public function setDtNascimento(?DateTime $dtNascimento): void
    {
        $this->dtNascimento = $dtNascimento;
    }

    /**
     * @return null|string
     */
    public function getOcupacao(): ?string
    {
        return $this->ocupacao;
    }

    /**
     * @param null|string $ocupacao
     */
    public function setOcupacao(?string $ocupacao): void
    {
        $this->ocupacao = $ocupacao;
    }

    /**
     * @return null|string
     */
    public function getObs(): ?string
    {
        return $this->obs;
    }

    /**
     * @param null|string $obs
     */
    public function setObs(?string $obs): void
    {
        $this->obs = $obs;
    }


}
