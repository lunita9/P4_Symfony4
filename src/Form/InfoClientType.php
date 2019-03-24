<?php

namespace App\Form;

use App\Entity\InfoClient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\InfoClientType;

class InfoClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
      /*      ->add('nom')
            ->add('prenom')
            ->add('pays')
            ->add('dateNaissance')
            ->add('accesReduit')
        ;*/



        ->add('nom', TextType::class, [
            'attr'=>[
            'placeholder'=>"Votre nom"
            ]
           ])
        ->add('prenom', TextType::class, [
            'attr'=>[
            'placeholder'=>"Votre prénom"
            ]
           ])
        ->add('pays', TextType::class, [
            'attr'=>[
            'placeholder'=>"Votre pays de résidence"
            ]
           ])
        ->add('date_naissance', DateType::class, array(
            /*'attr'=>[
                'placeholder'=>"Votre date de naissance"
            ]
        ])*/
            
            
            'format' => 'dd/MM/yyyy',
            'years' => range(1920, date('Y'))
            
            
        ))
        ->add('acces_reduit', ChoiceType::class, [
            'choices' => [
                'Oui' => true,
                'Non' => false
            ],
            'expanded' => true,
            'multiple' => false,
           ])
          /* ->getForm();
        return $this->render('blog/accueil.html.twig', [
            'forminfo_client'=>$form->createView()
            ]); */   
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => InfoClient::class,
        ]);
    }
}
