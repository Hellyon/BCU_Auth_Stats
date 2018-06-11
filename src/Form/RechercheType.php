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
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
            ->add('type', HiddenType::class, [
                'empty_data' => 'global',
                'data' => 'global',
            ])
            ->add('site', EntityType::class, [
               'query_builder' => function (EntityRepository $entityManager) {
                   return $entityManager->createQueryBuilder('s')->orderBy('s.nomSite', 'ASC');
               },
                'choice_label' => function (?Site $site) {
                    return $site->getNomSite();
                },
                'choice_attr' => function (?Site $site) {
                    return ['class' => 'site_'.strtolower($site->getNomSite())];
                },
                'placeholder' => 'Choisir un Site',
                'class' => 'App\Entity\Site',
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

                $formModifier($event->getForm()->getParent(), $site);
            });
        $builder->addEventListener(FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $site = $event->getData()->getSite();
                $poste = $event->getData()->getPoste();

                if ($site) {
                    $event->getData()->setType('site');
                }
                if ($poste) {
                    $event->getData()->setType('poste');
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recherche::class,
            'sites' => null,
        ]);
    }
}
