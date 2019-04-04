<?php

namespace App\Form;

use App\Entity\Cargo;
use App\Entity\CV;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\WhereBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CVAvaliaType.
 *
 * @package App\Form\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class CVAvaliaType extends AbstractType
{
    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

            $cv = $event->getData();
            $builder = $event->getForm();

            $disabled = $cv && ($cv->getStatus() !== 'ABERTO');

            $builder->add('id', HiddenType::class, array(
                'required' => false,
                'disabled' => $disabled
            ));


            $builder->add('status', ChoiceType::class, array(
                'label' => 'Status',
                'choices' => array(
                    'ABERTO' => 'ABERTO',
                    'FECHADO' => 'FECHADO',
                    'APROVADO' => 'APROVADO',
                    'REPROVADO' => 'REPROVADO'
                ),
                'required' => false
            ));


            $builder->add('avaliacao', TextareaType::class, array(
                'label' => 'Avaliação',
                'required' => false,
                'attr' => array(
                    'rows' => '15'
                ),
            ));

        });


    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => CV::class
        ));
    }
}