<?php

namespace App\Form;

use App\Entity\GroupeClients;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class GroupeClientsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('listeInfoClient',CollectionType::class,[
                'entry_type'=>InfoClientType::class,
                'entry_options'=>['label'=>false],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GroupeClients::class,
        ]);
    }
}
