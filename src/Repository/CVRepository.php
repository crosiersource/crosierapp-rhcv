<?php

namespace App\Repository;

use App\Entity\CV;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 * Repository para a entidade DiaUtil.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class  CVRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return CV::class;
    }

}
