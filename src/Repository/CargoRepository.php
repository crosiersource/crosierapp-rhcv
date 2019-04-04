<?php

namespace App\Repository;

use App\Entity\Cargo;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 * Repository para a entidade Cargo.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class  CargoRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return Cargo::class;
    }

}
