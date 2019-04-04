<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cargo.
 *
 * Registra os tipos de cargos da empresa.
 *
 * @ORM\Table(name="rhcv_cargo")
 * @ORM\Entity(repositoryClass="App\Repository\CargoRepository")
 */
class Cargo implements EntityId
{

    use EntityIdTrait;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cargo", type="string", length=100, nullable=true)
     */
    private $cargo;

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


}
