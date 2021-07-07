<?php

namespace App\Form;

use App\Entity\Journee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JourneeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('adultes')
            ->add('enfants')
            ->add('piscine_enfant')
            ->add('piscine_adulte')
            ->add('majoration')
            // ->add('commande')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Journee::class,
        ]);
    }
}
