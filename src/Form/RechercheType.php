<?php
/**
 * Created by PhpStorm.
 * User: ilbenjel
 * Date: 06/06/18
 * Time: 11:24.
 */

namespace App\Form;

use App\Entity\Poste;
use App\Entity\Recherche;
use App\Entity\Site;
use App\Repository\PosteRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choiceSites = [];
        foreach ($options['sites'] as $site) {
            $choiceSites[] = $site;
        }
        $builder->add('debut', DateType::class, [
                'label' => 'Date Début',
                'widget' => 'single_text',
                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'js-datepicker'],
            ])
            ->add('fin', DateType::class, [
                'label' => 'Date Fin',
                'widget' => 'single_text',
                'required' => false,
                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'js-datepicker'],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de Recherche',
                'choices' => [
                    'Recherche Site' => 'site',
                    'Recherche Globale' => 'global',
                    'Recherche Poste' => 'poste',
                ],
                'expanded' => true,
                'empty_data' => 'global',
                'data' => 'global',
            ])
            ->add('site', ChoiceType::class, [
                'choices' => $choiceSites,
                'choice_label' => function (?Site $site) {
                    return $site->getNomSite();
                },
                'choice_attr' => function (?Site $site) {
                    return ['class' => 'site_'.strtolower($site->getNomSite())];
                },
                'placeholder' => 'Choisir un Site',
                ]

            )
            ->add('rechercher', SubmitType::class);

        $formModifier = function (FormInterface $form, ?Site $site) {
            $formOptions = [
                'class' => Poste::class,
                'choice_label' => function (?Poste $poste) {
                    return $poste->getCodePoste();
                },
                'choice_attr' => function (?Poste $poste) {
                    return ['class' => 'poste_'.strtolower($poste->getCodePoste())];
                },
                'query_builder' => function (PosteRepository $entityRepository) use ($site) {
                    if ($site) {
                        return $entityRepository->findBySite($site);
                    } else {
                        return null;
                    }
                },
                'placeholder' => 'Choisir un Poste',
            ];
            $form->add('poste', EntityType::class, $formOptions);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $formModifier($event->getForm(), $event->getData()->getSite());
            });

        $builder->get('site')->addEventListener(FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $site = $event->getForm()->getData();

                dump($site);
                $formModifier($event->getForm()->getParent(), $site);
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recherche::class,
            'sites' => null,
        ]);
    }
}
