<?php

namespace App\Form;

use App\Entity\Formulaire;
use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class FormulaireType extends AbstractType
{
    private function getDisabledDateForDaysOff()
    {
        
        $disabledDateCurrentYear = '';
        $disabledDateNextYear = '';
        $currentYear = date('Y');
        $nextYear = date('Y')+1;
        
        $daysOff = ["/01/01", "/05/01", "/05/08", "/07/14", "/08/15", "/11/01", "/11/11", "/12/25"];
        
        $listDate="";
        foreach ($daysOff as $dayOff){
            
            $listDate=$listDate.$currentYear.$dayOff.', ';
            $listDate=$listDate.$nextYear.$dayOff.', '; //Attention : la virgule à la fin ajoute aujourd'hui à la liste
        }
        $listDate=substr($listDate,0,-2);//enlever les deux derniers caractères : ", "
        return $listDate;
        
        
    }
    

   

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        date_default_timezone_set('Europe/Paris');//sinon on récupère l'heure del'ordi moins un
        setlocale(LC_TIME, 'fr_FR.UTF8', 'fr.UTF8', 'fr_FR.UTF-8', 'fr.UTF-8');
        
        $builder
            ->add('title',EmailType::class, [
                'label' => 'Entrez votre adresse e-mail :'
            ])
            ->add('date_billet', DateType::class, array(
                'widget' => 'single_text', 
                'label'  => 'Date de visite :',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control  input-inline datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'yyyy-mm-dd',
                    'placeholder'=> "now"|date('d/m/Y'),
                    'data-date-language'=>'fr-FR',
                    'data-date-days-of-week-disabled' => '0,2',
                    'data-date-start-date' => "0d",
                    'data-date-end-date' => '+364d',
                    'data-date-dates-disabled' => $this->getDisabledDateForDaysOff()
                ]
                
                ));
            


            if (localtime(time(),true)['tm_hour']>=14 && date("now")) {
                $builder    ->add('type_jour', ChoiceType::class,array('choices'  => array( 
                    
                        'Journée' => 'Journée',
                        'Demi-journée' => 'Demi-journée'),
                        
                    
                    
                    ));
            } else {
                $builder->add('type_jour', ChoiceType::class,array('choices'  => array( 
                
                    'Journée' => 'Journée',
                    'Demi-journée' => 'Demi-journée'),
                    
                
                
                'label'=>'Durée de la visite :',
                'expanded' => true,
                'multiple' => false,
                ));
            }

                $builder ->add('nombre_total_ticket', IntegerType::class, array(
                'label' => 'Nombre total de billets :',
                'attr'=>array('min'=>0, 'max'=>1000)
                
            ))
            ->add('btn_ajout_panier', SubmitType::class, [
                'label'=>'Continuer'
            ])
            
        ;

       
        
        }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
