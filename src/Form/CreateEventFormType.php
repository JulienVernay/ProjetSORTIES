<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Location;
use App\Entity\Event;
use App\Entity\City;
use Doctrine\ORM\Mapping\Entity;
use Faker\Core\Number;
use PhpParser\Builder\Class_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Choice;

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
                'empty_data' => '',
                'attr' => ['class' => 'js-datepicker'],
                'required' => true,
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
                'empty_data' => null,
                'attr' => ['class' => 'js-datepicker'],
                'required' => true

            ])
            ->add('nbMaxRegistration',NumberType::class,[
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
            ])
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