<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', ['label'=>'Pseudo :'])
            ->add('firstName', ['label'=>'Prénom :'])
            ->add('lastName', ['label'=>'Nom :'])
            ->add('phone', ['label'=>'Téléphone :'])
            ->add('mail', ['label'=>'Email :'])
            ->add('password', ['label'=>'Mot de passe :'])
            ->add('password', ['label'=>'Confirmation :'])
            ->add('campusName', ['label'=>'Campus :'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
