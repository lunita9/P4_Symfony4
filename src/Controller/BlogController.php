<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Reservation;
use App\Entity\GroupeClients;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use App\Entity\InfoClient;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\FormulaireType;
use App\Form\InfoClientType;
use App\Form\GroupeClientsType;
use Symfony\Component\Validator\Constraints\DateTime;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */

    /* public function index(Request $request, ObjectManager $manager)
    {

        $reservation=new Reservation();
        $form=$this->createFormBuilder($reservation)
                   ->add('title', EmailType::class, [
                    'attr'=>[
                    'placeholder'=>"Votre adresse email"
                    ]
                   ])
                   ->add('date_billet', DateType::class, [
                   
                   ])
                   ->add('type_jour', ChoiceType::class, [
                    'choices' => [
                        'Journée' => true,
                        'Demi-journée' => false
                    ],
                    'expanded' => true,
                    'multiple' => false,
                   ]) 
                   ->add('nombre_tarif_normal', IntegerType::class, [
                    'attr'=>[
                        'placeholder'=>"Le nombre de vos billets en tarif normal"
                       ]
                   ])
                   ->add('nombre_tarif_reduit', IntegerType::class, [
                    'attr'=>[
                        'placeholder'=>"Le nombre de vos billets en tarif réduit"
                        
                       ]
                   ])
                   ->add('nombre_tarif_enfant', IntegerType::class, [
                    'attr'=>[
                        'placeholder'=>"Le nombre de vos billets en tarif enfant"
                       ]
                   ])
                   ->add('nombre_tarif_senior', IntegerType::class, [
                    'attr'=>[
                        'placeholder'=>"Le nombre de vos billets en tarif senior"
                       ]
                   ])
                   ->getForm();
        
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'formFormulaire'=>$form->createView()
        ]);
    }*/

   
    /**
     * @Route("/blog", name="blog")
     */
     public function Billeterie(Request $request)
    {
        $session=$request->getSession();
        if (!($session->has('msg_trop_tard'))) {
            $session->set('msg_trop_tard','Veuillez effectuer votre choix');
        }

        $reservation=new Reservation();
        $form=$this->createForm(FormulaireType::class, $reservation);

        $form->handleRequest($request);
        
        if($form->isSubmitted()&& $form->isValid()){
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($reservation);
            $entityManager->flush();


            //$session->set('nombre_total_ticket', $reservation->getNombreTotalTicket());
            //$date1 = new DateTime("now");
            $today = strtotime(date('Y-m-d'));
            $dateSaisie = strtotime($reservation->getDateBillet()->format('Y-m-d'));
            $interval = $dateSaisie - $today;
            if($interval == 0) {//aujourd'hui, tester 14h00
                $today14hSecondes=strtotime(date('Y-m-d').' 14:00');
                $maintenant=strtotime(date('Y-m-d H:i:s'));
                $interval = $today14hSecondes - $maintenant;
            }
            if( $interval > 0) {// $interval >0 : à partir de demain ou aujourd'hui avant 14h
                $session->set('nombre_total_ticket', $reservation->getNombreTotalTicket());
                $session->set('date_billet', $reservation->getDateBillet());
                $session->set('msg_trop_tard','Veuillez effectuer votre choix');
            } else {// $interval=0 : aujourd'hui après 14h
                $session->set('nombre_total_ticket', $reservation->getNombreTotalTicket());
                $session->set('date_billet', $reservation->getDateBillet()); 
                //TODO : si on a choisi journée complète, message et ne pas changer de page
                if($reservation->getTypeJour() == 'Journée') {
                    $session->set('msg_trop_tard','Il est trop tard pour le billet Journée');
                    return $this->render('blog/index.html.twig', [
                    'controller_name' => 'BlogController',
                    'formFormulaire'=>$form->createView(),
                    'msg_trop_tard' => $session->get('msg_trop_tard')
                ]);
                }
                /*if($reservation.getTypeJour()=='Erreur'){
                    echo("Pas possible");
            }*/
            }

            /*$session=$request->getSession();
            $session->set('date_billet', $reservation->getDateBillet());*/


            return $this->redirectToRoute('accueil');
        }
        /*if ($form->getClickedButton() && 'btn_ajout_panier' === $form->getClickedButton()->getName()) {
            return $this->redirectToRoute('accueil');
        }*/

        



        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'formFormulaire'=>$form->createView(),
            'msg_trop_tard' => $session->get('msg_trop_tard')
        ]);    

        /*if ($form->isSubmitted() && $form->isValid()) {
        */
            // ... perform some action, such as saving the task to the database
        
           /* $nextAction = $form->get('btn_ajout_panier')->isClicked()
                ? 'task_new'
                : 'task_success';
        */

        
    }

    

    public function getNombreTotalTicket(Reservation $reservation, InfoClient $infoClient)
    {
        $nombreTarifNormal=$reservation->getNombreTarifNormal();
        $nombreTarifReduit=$reservation->getNombreTarifReduit();
        $nombreTarifEnfant=$reservation->getNombreTarifEnfant();
        $nombreTarifSenior=$reservation->getNombreTarifSenior();
        $nombreTotalTicket=$nombreTarifNormal+$nombreTarifReduit+$nombreTarifEnfant+$nombreTarifSenior;
        return $nombreTotalTicket;
    }
    /*public function getPrice(InfoClient $infoClient, Reservation $reservation)
    {
        $currentYear=2019;
        $price=0;
        //for()
        if($infoClient->getDateNaissance()->format('%Y') <= $currentYear-4 
        && $infoClient->getDateNaissance()->format('%Y') >= $currentYear-12){
            
            if($reservation.getTypeJour() == 'Journée'){
                $price=8;
            } else {
                $price=5;
            }
        }elseif($infoClient->getDateNaissance()->format('%Y')< $currentYear-12 
        && $infoClient->getDateNaissance()->format('%Y')> $currentYear-60){
            if($reservation.getTypeJour()=='Journée'){
                $price=16;
            }else{
                $price=9;
            }
            
        }elseif($infoClient->getDateNaissance()->format('%Y')<= $currentYear-60){
             if($reservation.getTypeJour()=='Journée'){
                 $price=12;
             }else{
                 $price=7;
             }
        }else{
            if($reservation.getTypeJour()=='Journée'){
                $price=10;
            }else{
                $price=6;
            }
        }
        return $price;
    }*/

    public function getTarif($anneeNaissance, $moisNaissance, $jourNaissance, $reduit, $typeJour, Reservation $reservation, InfoClient $infoClient){
        $anneeNaissance=strtotime($reservation->getDateBillet()->format('Y'));
        $moisNaissance=strotime($reservation->getDateBillet()->format('m'));
        $jourNaissance=strotime($reservation->getDateBillet()->format('d'));
        $anneeAjd=strotime(date('Y'));
        $moisAjd=strotime(date('m'));
        $jourAjd=strotime(date('d'));

        $tarifs = file_get_contents('/demo/tarifs.json');
        $tarifs= json_decode($tarifs);
        
        if ($reduit) return $tarifs['reduit'];
        
        $correctif = 0;
        $age = $anneeAjd - $anneeNaissance;
        if ($moisAjd < $moisNaissance) $correctif=1;
        if ($moisAjd == $moisNaissance && $jourAjd < $jourNaissance) $correctif=1;
        $age -= $correctif;
        
        if ($age < 4 )   return $tarifs['nourrisson'];
        if ($age < 12 && $typeJour=='Journée')  return $tarifs['enfantJ'];
        if ($age >= 60 && $typeJour=='Journée') return $tarifs['seniorJ'];
        if($age>=12 && $age<60 && $typeJour=='Journée') return $tarifs['normalJ'];
        if($age >=4 && $age< 12 && $typeJour=='Demi-journée' ) return $tarifs['enfantDJ'];
        if($age >= 60 && $typeJour=='Demi-journée') return $tarifs['seniorDJ'];
        return $tarifs['normalDJ'];

        
        }

       
    

    public function isClicked(){
        if ( $form -> isSubmitted () && $form -> isValid ()) {
        // ... perform some action, such as saving the task to the database
    
            $infoClient = $form -> get ( 'btn_ajout_panier' ) -> isClicked ()
            ? 'task_new'
            : 'task_success' ;
    
            return $this -> redirectToRoute ( $infoClient );
        }
    }


    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('blog/home.html.twig');
    }

    /**
     * @Route("/accueil", name="accueil")
     */
    public function Detail_Client(Request $request)
    {
        $session=$request->getSession();
        $nombreBillet=$session->get('nombre_total_ticket');
        $dateBillet=$session->get('date_billet');
        //$price=$session->get('price');

        $groupeClients = new GroupeClients();
        for($i=1;$i<=$nombreBillet;$i++){
            $infoClient=new InfoClient();
            $groupeClients->getListeInfoClient()-> add($infoClient);
        }

        $form=$this->createForm(GroupeClientsType::class, $groupeClients);

        $form->handleRequest($request);
        
        if($form->isSubmitted()&& $form->isValid()){
            //$entityManager=$this->getDoctrine()->getManager();
            //$entityManager->persist($ListInfoClient);
            //$entityManager->flush();

            //return $this->redirectToRoute('blog');
        }

        return $this->render('blog/accueil.html.twig', [
            'nbBillet'=>$nombreBillet,
            'dateBillet'=>$dateBillet->format('d-m-Y'),
            //'price'=>$price,
            'controller_name' => 'BlogController',
            'forminfo_client'=>$form->createView()
        ]);    

    }


    public function Detail_Client_old(Request $request)
    {
        $session=$request->getSession();
        $nombreBillet=$session->get('nombre_total_ticket');
        $dateBillet=$session->get('date_billet');

        $ListInfoClient= array([$nombreBillet]);
        for($i=1;$i<=$nombreBillet;$i++){
            $ListInfoClient[$i-1]=new InfoClient();
           // $form=$this->createForm(InfoClientType::class, $ListInfoClient[$i-1]);
        
        }

        /*$session=$request->getSession();
        $dateBillet=$session->get('date_billet');

        $ListInfoClient=array([$dateBillet]);
        $ListInfoClient=new InfoClient();*/
        $form=$this->createForm(InfoClientType::class); //, $ListInfoClient);

        $form->handleRequest($request);
        
        
        if($form->isSubmitted()&& $form->isValid()){
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($ListInfoClient);
            $entityManager->flush();

            return $this->redirectToRoute('blog');
        }
    
        


        return $this->render('blog/accueil.html.twig', [
            'nbBillet'=>$nombreBillet,
            'dateBillet'=>$dateBillet->format('d-m-Y'),
            'controller_name' => 'BlogController',
            'forminfo_client'=>$form->createView()
        ]);    

    }

        //$infoClient=new InfoClient();
        //$form=$this->createFormBuilder($infoClient)
        /*->add('nom', TextType::class, [
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
        ])
            
            
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
           ->getForm();
        return $this->render('blog/accueil.html.twig', [
            'forminfo_client'=>$form->createView()
            ]); */   

}



