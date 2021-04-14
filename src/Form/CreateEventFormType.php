<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Location;
use App\Entity\City;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CreateEventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class,[
                'label' => "Nom de la Sortie :",
                'attr' => [
                    'class' => "form-control"
                ]
            ])
            ->add('site',EntityType::class,[
                'label' => "Campus :",
                'class' => Campus::class,
                'choice_label' => 'campusName',
                'choice_value' => 'id',
            ])
            ->add('startingDateTime',DateTimeType::class,[
                'label' => "Date et heure de la sortie :",
                'widget' => 'single_text',
                'input' => 'datetime',
                'html5' => 'false',
                'required' => true
            ])
            ->add('duration',IntegerType::class,[
                'label' => "Durée :",
                'attr' => [
                    'type' => 'number',
                    'min' => 1,
                ]
            ])
            ->add('inscriptionDeadLine',DateTimeType::class,[
                'label' => "Date limite d'inscription :",
                'widget' => 'single_text',
                'html5' => 'false',
                'input' => 'datetime',
                'required' => true

            ])
            ->add('nbMaxRegistration',IntegerType::class,[
                'label' => "Nombre de places :",
                'attr' => [
                    'min' => 1
                ]
            ])
            ->add('eventDetails', TextareaType::class, [
                'label' => 'Informations et description :'
            ])
            ->add('city',EntityType::class,[
                'mapped' => false,
                'label' => "Ville :",
                'class' => City::class,
                'choice_label' => 'name',
                'choice_value' => 'id'
            ])
            ->add('location',EntityType::class,[
                'label' => "Lieu",
                'class' => Location::class,
                'choice_label' => 'name',
                'choice_value' => 'id'
            ])
            ->add('street',TextType::class,[
                'mapped' => false,
                'label' => "Rue :",
                'disabled' =>true
            ])
            ->add('zipCode',TextType::class,[
                'mapped' => false,
                'label' => "Code Postal :",
                'disabled' =>true
            ]);
    }
}

