<?php

namespace App\EntityHandler;

use App\Entity\CV;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para a entidade CV.
 *
 * @package App\EntityHandler
 * @author Carlos Eduardo Pauluk
 */
class CVEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return CV::class;
    }

    public function beforeSave($cv)
    {
        // Aqui, o usuário logado não é da tabela sec_user
        /** @var CV $cv */
        $cv->setUserInsertedId(1);
        $cv->setUserUpdatedId(1);
        $cv->setEstabelecimentoId(1);
    }


}