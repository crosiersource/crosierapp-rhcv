<?php

namespace App\Form;

use App\Entity\Cargo;
use App\Entity\CV;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
 * Class CVType.
 *
 * @package App\Form\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class CVType extends AbstractType
{
    private EntityManagerInterface $doctrine;

    public function __construct(EntityManagerInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

            $cv = $event->getData();
            $builder = $event->getForm();

            $disabled = $cv && ($cv->getStatus() !== 'ABERTO');

            $builder->add('id', HiddenType::class, [
                'required' => false,
                'disabled' => $disabled
            ]);

            $builder->add('updated', DateType::class, [
                'label' => 'Data do currículo',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy HH:mm',
                'html5' => false,
                'attr' => [
                    'class' => 'crsr-datetime',
                    'readonly' => true
                ],
                'required' => true,
                'disabled' => $disabled
            ]);

            $builder->add('sexo', ChoiceType::class, [
                'label' => 'Sexo',
                'choices' => [
                    '...' => '',
                    'MASCULINO' => 'MASCULINO',
                    'FEMININO' => 'FEMININO'
                ],
                'attr' => ['class' => 'autoSelect2'],
                'required' => true,
                'disabled' => $disabled
            ]);

            $builder->add('cargosPretendidos', EntityType::class, [
                'label' => 'Cargos pretendidos',
                'class' => Cargo::class,
                'choices' => $this->doctrine->getRepository(Cargo::class)->findAll(),
                'multiple' => true,
                'choice_label' => 'cargo',
                'expanded' => false,
                'help' => 'Selecione cada um dos cargos pretendidos (caso mais de um)',
                'disabled' => $disabled
            ]);

            $builder->add('cpf', TextType::class, [
                    'label' => 'CPF',
                    'attr' => [
                        'class' => 'cpf',
                        'readonly' => true
                    ],
                    'required' => true,
                    'disabled' => $disabled
                ]
            );

            $builder->add('email', EmailType::class, [
                'label' => 'E-mail',
                'attr' => [
                    'class' => 'email',
                    'readonly' => true
                ],
                'required' => true,
                'disabled' => $disabled
            ]);

            $builder->add('nome', TextType::class, [
                'label' => 'Nome',
                'required' => true,
                'disabled' => $disabled
            ]);

            $builder->add('dtNascimento', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
                'label' => 'Dt Nascimento',
                'html5' => false,
                'attr' => [
                    'class' => 'crsr-date'
                ],
                'disabled' => $disabled
            ]);

            $builder->add('naturalidade', TextType::class, [
                'label' => 'Em que cidade nasceu',
                'disabled' => $disabled
            ]);

            for ($i = 1; $i <= 5; $i++) {
                $builder->add('telefone' . $i, TextType::class, [
                    'label' => 'Fone (' . $i . ')',
                    'attr' => [
                        'class' => 'telefone'
                    ],
                    'required' => false,
                    'disabled' => $disabled
                ]);
                $builder->add('telefone' . $i . 'Tipo', ChoiceType::class, [
                    'label' => 'Fone (' . $i . ') Tipo',
                    'choices' => [
                        'CELULAR COM WHATSAPP' => 'CELULAR COM WHATSAPP',
                        'CELULAR SEM WHATSAPP' => 'CELULAR SEM WHATSAPP',
                        'RESIDENCIAL' => 'RESIDENCIAL',
                        'COMERCIAL' => 'COMERCIAL',
                    ],
                    'attr' => ['class' => 'autoSelect2'],
                    'required' => false,
                    'disabled' => $disabled
                ]);
            }

            $builder->add('enderecoAtualLogr', TextType::class, [
                'label' => 'Logradouro',
                'required' => false,
                'disabled' => $disabled
            ]);
            $builder->add('enderecoAtualNumero', TextType::class, [
                'label' => 'Número',
                'required' => false,
                'disabled' => $disabled
            ]);
            $builder->add('enderecoAtualCompl', TextType::class, [
                'label' => 'Complemento',
                'required' => false,
                'disabled' => $disabled
            ]);
            $builder->add('enderecoAtualBairro', TextType::class, [
                'label' => 'Bairro',
                'required' => false,
                'disabled' => $disabled
            ]);
            $builder->add('enderecoAtualCidade', TextType::class, [
                'label' => 'Cidade',
                'required' => false,
                'disabled' => $disabled
            ]);
            $builder->add('enderecoAtualUf', ChoiceType::class, [
                'label' => 'Estado',
                'choices' => [
                    'Selecione...' => '',
                    'Acre' => 'AC',
                    'Alagoas' => 'AL',
                    'Amapá' => 'AP',
                    'Amazonas' => 'AM',
                    'Bahia' => 'BA',
                    'Ceará' => 'CE',
                    'Distrito Federal' => 'DF',
                    'Espírito Santo' => 'ES',
                    'Goiás' => 'GO',
                    'Maranhão' => 'MA',
                    'Mato Grosso' => 'MT',
                    'Mato Grosso do Sul' => 'MS',
                    'Minas Gerais' => 'MG',
                    'Pará' => 'PA',
                    'Paraíba' => 'PB',
                    'Paraná' => 'PR',
                    'Pernambuco' => 'PE',
                    'Piauí' => 'PI',
                    'Rio de Janeiro' => 'RJ',
                    'Rio Grande do Norte' => 'RN',
                    'Rio Grande do Sul' => 'RS',
                    'Rondônia' => 'RO',
                    'Roraima' => 'RR',
                    'Santa Catarina' => 'SC',
                    'São Paulo' => 'SP',
                    'Sergipe' => 'SE',
                    'Tocantins' => 'TO'
                ],
                'required' => false,
                'disabled' => $disabled,
                'attr' => ['class' => 'autoSelect2'],
            ]);
            $builder->add('enderecoAtualTempoResid', TextType::class, [
                'label' => 'Tempo em que reside',
                'required' => false,
                'disabled' => $disabled
            ]);


            $builder->add('estadoCivil', ChoiceType::class, [
                'label' => 'Estado Civil',
                'choices' => [
                    'Solteiro(a)' => 'SOLTEIRO',
                    'Casado(a)' => 'CASADO',
                    'Viúvo(a)' => 'VIUVO',
                    'Separado(a)' => 'SEPARADO',
                    'Divorciado(a)' => 'DIVORCIDADO'
                ],
                'required' => false,
                'attr' => ['class' => 'autoSelect2'],
                'disabled' => $disabled
            ]);

            $builder->add('conjugeNome', TextType::class, [
                'label' => 'Nome do cônjuge',
                'required' => false,
                'disabled' => $disabled
            ]);
            $builder->add('conjugeProfissao', TextType::class, [
                'label' => 'Profissão do cônjuge',
                'required' => false,
                'disabled' => $disabled
            ]);

            $builder->add('temFilhos', ChoiceType::class, [
                'label' => 'Filhos',
                'choices' => [
                    'Sim' => 'S',
                    'Não' => 'N'
                ],
                'attr' => ['class' => 'autoSelect2'],
                'required' => false,
                'disabled' => $disabled
            ]);

            $builder->add('qtdeFilhos', IntegerType::class, [
                'label' => 'Qtde Filhos',
                'required' => false,
                'disabled' => $disabled,
                'attr' => [
                    'min' => 0,
                    'max' => 50
                ],
            ]);

            $builder->add('paiNome', TextType::class, [
                'label' => 'Nome do pai',
                'required' => false,
                'help' => 'Caso desconhecido, não informar.',
                'disabled' => $disabled
            ]);
            $builder->add('paiProfissao', TextType::class, [
                'label' => 'Profissão do pai',
                'required' => false,
                'help' => 'Caso aposentado ou falecido, informe.',
                'disabled' => $disabled
            ]);
            $builder->add('maeNome', TextType::class, [
                'label' => 'Nome da mãe',
                'required' => false,
                'help' => 'Caso desconhecida, não informar.',
                'disabled' => $disabled
            ]);
            $builder->add('maeProfissao', TextType::class, [
                'label' => 'Profissão da mãe',
                'required' => false,
                'help' => 'Caso aposentada ou falecida, informe.',
                'disabled' => $disabled
            ]);


            $builder->add('referencia1Nome', TextType::class, [
                'label' => 'Nome',
                'required' => false,
                'disabled' => $disabled
            ]);
            $builder->add('referencia1Relacao', TextType::class, [
                'label' => 'Relação',
                'required' => false,
                'help' => 'Informar o tipo de relação: amigo, vizinho, familiar, colega de trabalho, etc.',
                'disabled' => $disabled
            ]);
            $builder->add('referencia1Telefone1', TextType::class, [
                'label' => 'Fone (1)',
                'attr' => [
                    'class' => 'telefone'
                ],
                'required' => false,
                'disabled' => $disabled
            ]);
            $builder->add('referencia1Telefone2', TextType::class, [
                'label' => 'Fone (2)',
                'attr' => [
                    'class' => 'telefone'
                ],
                'required' => false,
                'disabled' => $disabled
            ]);
            $builder->add('referencia2Nome', TextType::class, [
                'label' => 'Nome',
                'required' => false,
                'disabled' => $disabled
            ]);
            $builder->add('referencia2Relacao', TextType::class, [
                'label' => 'Relação',
                'required' => false,
                'help' => 'Informar o tipo de relação: amigo, vizinho, familiar, colega de trabalho, etc.',
                'disabled' => $disabled
            ]);
            $builder->add('referencia2Telefone1', TextType::class, [
                'label' => 'Fone (1)',
                'attr' => [
                    'class' => 'telefone'
                ],
                'required' => false,
                'disabled' => $disabled
            ]);
            $builder->add('referencia2Telefone2', TextType::class, [
                'label' => 'Fone (2)',
                'attr' => [
                    'class' => 'telefone'
                ],
                'required' => false,
                'disabled' => $disabled
            ]);


            $builder->add('ensinoFundamentalStatus', ChoiceType::class, [
                'label' => 'Ensino Fundamental',
                'choices' => [
                    'Não cursado' => 'NC',
                    'Cursando' => 'CR',
                    'Incompleto' => 'I',
                    'Concluído' => 'C'
                ],
                'required' => false,
                'disabled' => $disabled,
                'attr' => ['class' => 'autoSelect2'],
            ]);
            $builder->add('ensinoFundamentalLocal', TextType::class, [
                'label' => 'Escola',
                'required' => false,
                'disabled' => $disabled
            ]);

            $builder->add('ensinoMedioStatus', ChoiceType::class, [
                'label' => 'Ensino Médio',
                'choices' => [
                    'Não cursado' => 'NC',
                    'Cursando' => 'CR',
                    'Incompleto' => 'I',
                    'Concluído' => 'C'
                ],
                'required' => false,
                'disabled' => $disabled,
                'attr' => ['class' => 'autoSelect2'],
            ]);
            $builder->add('ensinoMedioLocal', TextType::class, [
                'label' => 'Escola',
                'required' => false,
                'disabled' => $disabled
            ]);

            $builder->add('ensinoSuperiorStatus', ChoiceType::class, [
                'label' => 'Ensino Superior',
                'choices' => [
                    'Não cursado' => 'NC',
                    'Cursando' => 'CR',
                    'Incompleto' => 'I',
                    'Concluído' => 'C'
                ],
                'required' => false,
                'disabled' => $disabled,
                'attr' => ['class' => 'autoSelect2'],
            ]);
            $builder->add('ensinoSuperiorLocal', TextType::class, [
                'label' => 'Faculdade/Curso',
                'required' => false,
                'help' => 'Informar a faculdade e o curso',
                'disabled' => $disabled,
            ]);

            $builder->add('ensinoDemaisObs', TextareaType::class, [
                'label' => 'Outros',
                'required' => false,
                'help' => 'Caso tenha cursado, informe aqui sobre cursos técnicos, pós-graduações, mestrados, etc',
                'disabled' => $disabled
            ]);

            $builder->add('conheceAEmpresaTempo', TextType::class, [
                'label' => 'Há quanto tempo conhece nossa empresa?',
                'required' => false,
                'help' => 'Informe também caso ainda não conheça',
                'disabled' => $disabled
            ]);

            $builder->add('ehNossoCliente', ChoiceType::class, [
                'label' => 'É nosso cliente?',
                'choices' => [
                    'Sim' => 'S',
                    'Não' => 'N'
                ],
                'required' => false,
                'disabled' => $disabled,
                'attr' => ['class' => 'autoSelect2'],
            ]);

            $builder->add('parente1FichaCrediarioNome', TextType::class, [
                'label' => 'Nome (1)',
                'required' => false,
                'disabled' => $disabled
            ]);
            $builder->add('parente2FichaCrediarioNome', TextType::class, [
                'label' => 'Nome (2)',
                'required' => false,
                'disabled' => $disabled
            ]);

            $builder->add('conhecido1TrabalhouNaEmpresa', TextType::class, [
                'label' => 'Nome (1)',
                'required' => false,
                'disabled' => $disabled
            ]);

            $builder->add('conhecido2TrabalhouNaEmpresa', TextType::class, [
                'label' => 'Nome (2)',
                'required' => false,
                'disabled' => $disabled
            ]);

            $builder->add('motivosQuerTrabalharAqui', TextareaType::class, [
                'label' => 'Por quais motivos deseja trabalhar em nossa empresa?',
                'attr' => [
                    'rows' => '5'
                ],
                'required' => false,
                'disabled' => $disabled
            ]);


            $builder->add('jaTrabalhou', ChoiceType::class, [
                'label' => 'Já trabalhou?',
                'choices' => [
                    'Sim' => 'S',
                    'Não' => 'N'
                ],
                'required' => false,
                'disabled' => $disabled,
                'attr' => ['class' => 'autoSelect2'],
            ]);

            $builder->add('qtdeEmpregos', IntegerType::class, [
                'label' => 'Qtde Empregos',
                'required' => false,
                'disabled' => $disabled,
                'attr' => [
                    'min' => 0,
                    'max' => 10
                ]
            ]);

        });


    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => CV::class
        ));
    }
}