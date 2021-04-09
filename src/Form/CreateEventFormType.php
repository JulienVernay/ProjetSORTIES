<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CreateEventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la sortie :'
            ])
            ->add('startingDateTime', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date et heure de la sortie :'
            ])
            ->add('duration', TextType::class, [
                'label' => 'Durée :'
            ])
            ->add('inscriptionDeadLine', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => "Date limite d'inscription :"
            ])
            ->add('nbMaxRegistration', TextType::class, [
                'label' => 'Nombre maximum de place :'
            ])
            ->add('eventDetails', TextareaType::class, [
                'label' => 'Informations et description :'
            ])
            ->add('location')
            ->add('enregistrer', SubmitType::class, [
                'attr' => ['class' => 'btn-block btn-info btn'],
            ])
            ->add('publier', SubmitType::class, [
                'attr' => ['class' => 'btn-block btn-success btn'],
            ])
            ->add('annuler', SubmitType::class, [
             'attr' => ['class' => 'btn-block btn-danger btn'],
    ]);
    }
}