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
use App\Service\PriceCalculator\PriceCalculator;
//use Symfony\Component\Mailer\MailerInterface;
//use Symfony\Component\Mime\Email;
use App\Service\MailValidator\MailValidator;


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
            $session->set('type_jour', $reservation->getTypeJour());
            if( $interval > 0) {// $interval >0 : à partir de demain ou aujourd'hui avant 14h
                $session->set('nombre_total_ticket', $reservation->getNombreTotalTicket());
                $session->set('date_billet', $reservation->getDateBillet());
                $session->set('title', $reservation->getTitle());
                $session->set('msg_trop_tard','Veuillez effectuer votre choix');
            } else {// $interval=0 : aujourd'hui après 14h
                $session->set('nombre_total_ticket', $reservation->getNombreTotalTicket());
                $session->set('date_billet', $reservation->getDateBillet());
                $session->set('title', $reservation->getTitle()); 
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

    //public function getTarif($anneeNaissance, $moisNaissance, $jourNaissance, $reduit, $typeJour){
        /*
        
        $anneeNaissance=strtotime($reservation->getDateBillet()->format('Y'));
        $moisNaissance=strotime($reservation->getDateBillet()->format('m'));
        $jourNaissance=strotime($reservation->getDateBillet()->format('d'));
        
        $age = $anneeAjd - $anneeNaissance;
        if ($moisAjd < $moisNaissance) $age--;
        if ($moisAjd == $moisNaissance && $jourAjd < $jourNaissance) $age--;
        
        
        $anneeAjd=strotime(date('Y'));
        $moisAjd=strotime(date('m'));
        $jourAjd=strotime(date('d'));

        

        $tarifs = file_get_contents('/demo/prixTarifs.json');
        $tarifs= json_decode($tarifs);
        
        if ($reduit) return $tarifs['reduitJ'] || $tarifs['reduitDJ'];*/
        
        /*$correctif = 0;
        $age = $anneeAjd - $anneeNaissance;
        if ($moisAjd < $moisNaissance) $correctif=1;
        if ($moisAjd == $moisNaissance && $jourAjd < $jourNaissance) $correctif=1;
        $age -= $correctif;*/
        
       /* if ($age < 4 )   return $tarifs['nourrisson'];
        if ($age < 12 && $typeJour=='Journée')  return $tarifs['enfantJ'];
        if ($age >= 60 && $typeJour=='Journée') return $tarifs['seniorJ'];
        if($age>=12 && $age<60 && $typeJour=='Journée') return $tarifs['normalJ'];
        if($age >=4 && $age< 12 && $typeJour=='Demi-journée' ) return $tarifs['enfantDJ'];
        if($age >= 60 && $typeJour=='Demi-journée') return $tarifs['seniorDJ'];
        return $tarifs['normalDJ'];*/

        
    //}

       
    

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
        $typeJour=$session->get('type_jour');
        $nombreBillet=$session->get('nombre_total_ticket');
        $dateBillet=$session->get('date_billet');
        $title=$session->get('title');
        //$priceTotal=$session->get('priceTotal');

        $groupeClients = new GroupeClients();
        for($i=1;$i<=$nombreBillet;$i++){
            $infoClient=new InfoClient();
            //$infoClient->setId("Titre du formulaire n$i");
            $groupeClients->getListeInfoClient()-> add($infoClient);
        }

        $form=$this->createForm(GroupeClientsType::class, $groupeClients);
        //$form=$this->createForm(InfoClientType::class, $infoClient);

        $form->handleRequest($request);
        
        if($form->isSubmitted()&& $form->isValid()){
            $priceTotal=0;
            $priceCalculator = new PriceCalculator();
            for($i=0;$i<$nombreBillet;$i++){
                $infoClient= $groupeClients->getListeInfoClient()[$i];
                $dateNaissance=$infoClient->getDateNaissance();
                $anneeNaissance=$dateNaissance->format('Y');
                $moisNaissance=$dateNaissance->format('m');
                $jourNaissance=$dateNaissance->format('d');
                $reduit=$infoClient->getAccesReduit();
                $prixClient=$priceCalculator->getTarifClient($anneeNaissance, $moisNaissance, $jourNaissance, $reduit, $typeJour);
                echo $prixClient;
                $priceTotal+=$prixClient;
                
            }
            echo $priceTotal;
            //$entityManager=$this->getDoctrine()->getManager();
            //$entityManager->persist($listInfoClient);
            //$entityManager->flush();

            $session->set('groupe_client', $groupeClients);
            $session->set('price_total', $priceTotal);
            $session->set('title', $title);
            return $this->redirectToRoute('confirmation');
        }
        /*return $this->render('blog/accueil.html.twig', [
            'controller_name' => 'BlogController',
            'forminfo_client'=>$form->createView()
        ]);*/
        return $this->render('blog/accueil.html.twig', [
            'nbBillet'=>$nombreBillet,
            'dateBillet'=>$dateBillet->format('d-m-Y'),
            'type_jour'=>$typeJour,
            'title'=>$title,
            'controller_name' => 'BlogController',
            'forminfo_client'=>$form->createView(),
            'groupe_client'=>$groupeClients
        ]);   
        
    }

    /**
     * @Route("/confirmation", name="confirmation")
     */
    public function Confirmer(Request $request)
    {
        $session=$request->getSession();
        $typeJour=$session->get('type_jour');
        $nombreBillet=$session->get('nombre_total_ticket');
        $dateBillet=$session->get('date_billet');
        $title=$session->get('title');
        $priceTotal=$session->get('price_total');
        
        $groupeClients=$session->get('groupe_client');
        /*$groupeClients = new GroupeClients();
        for($i=1;$i<=$nombreBillet;$i++){
            $infoClient=new InfoClient();
            //$infoClient->setId("Titre du formulaire n$i");
            $groupeClients->getListeInfoClient()-> add($infoClient);
        }*/

        $form=$this->createForm(GroupeClientsType::class, $groupeClients);
        //return $this->redirectToRoute('paiement');
        //echo $groupeClients->getListeInfoClient();
        return $this->render('blog/confirmation.html.twig', [
            'nbBillet'=>$nombreBillet,
            'dateBillet'=>$dateBillet->format('d-m-Y'),
            'price_total'=>$priceTotal,
            'type_jour'=>$typeJour,
            'title'=>$title,
            'groupe_client'=>$groupeClients,
            'controller_name' => 'BlogController',
            'forminfo_client'=>$form->createView()
            ]);
        //return $this->render('blog/confirmation.html.twig');
    }

    

    /**
     * @Route("/paiement", name="paiement")
     */
    public function Payer(Request $request)
    {
        $session=$request->getSession();
        $title=$session->get('title');
        $dateBillet=$session->get('date_billet');
        $priceTotal=$session->get('price_total');
        $typeJour=$session->get('type_jour');
        $groupeClients=$session->get('groupe_client');
        $code=$session->get('code');
        //$image=$session->get('image');
        //$stripeClient = $this->get('flosch.stripe.client');
        \Stripe\Stripe::setApiKey("sk_test_v58YrQMVbowbCjD1DUPz7D0900tM8CdbBG");

        \Stripe\Charge::create(array(
            "amount"=>2000,
            "currency"=>"eur",
            "source"=>$_POST['stripeToken'],
            //"source"=>$request->request->get('tok_visa'),
            "description"=>"Paiement de test"
        ));
        $transport = (new \Swift_SmtpTransport('smtp.orange.fr', 465))
        ->setUsername('openclassrooms_IF@orange.fr')
        ->setPassword('testOPp4')
        ->setEncryption('ssl');
        $mailer = new \Swift_Mailer($transport);

        $message= (new \Swift_Message('Soyez les bienvenus au Louvre!!'))
        ->setFrom('noreply@museedulouvre.fr')
        ->setTo($title);
        $image = $message->embed(\Swift_Image::fromPath('img/louvre_logo_01.jpg'));
        $code=strtoupper(substr(md5(uniqid()),0,10));
        $message->setBody($this->render('blog/email.html.twig',[
            'image'=>$image,
            'code'=>$code,
            'dateBillet' => $dateBillet->format('d-m-Y'),
            'groupe_client' => $groupeClients,
            'price_total' => $priceTotal,
            'type_jour'=>$typeJour,
            'title' => $title
         ]), 'text/html');
        

        $result = $mailer->send($message);

        return $this->render('blog/paiement.html.twig', [
            'title'=>$title
        ]);
    }

    

    /**
     * @Route("/email")
     */
    public function sendEmail(\Swift_Mailer $mailer, Request $request)
    {
        $session=$request->getSession();
        $typeJour=$session->get('type_jour');
        $nombreBillet=$session->get('nombre_total_ticket');
        $dateBillet=$session->get('date_billet');
        $title=$session->get('title');
        $priceTotal=$session->get('price_total');
        
        $groupeClients=$session->get('groupe_client');
        /*$groupeClients = new GroupeClients();
        for($i=1;$i<=$nombreBillet;$i++){
            $infoClient=new InfoClient();
            //$infoClient->setId("Titre du formulaire n$i");
            $groupeClients->getListeInfoClient()-> add($infoClient);
        }*/

        $form=$this->createForm(GroupeClientsType::class, $groupeClients);
        //return $this->redirectToRoute('paiement');
        //echo $groupeClients->getListeInfoClient();
        

        $email = (new MailValidator($request))
            ->from('openclassrooms_IF@orange.fr')
            ->to($title)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');
        echo $email;
        $mailer->send($email);

        return $this->render('blog/email.html.twig', [
            'nbBillet'=>$nombreBillet,
            'dateBillet'=>$dateBillet->format('d-m-Y'),
            'price_total'=>$priceTotal,
            'type_jour'=>$typeJour,
            'title'=>$title,
            'groupe_client'=>$groupeClients,
            'controller_name' => 'BlogController',
            'forminfo_client'=>$form->createView()
            ]);
    }



   /* public function Detail_Client_old(Request $request)
    {
        $session=$request->getSession();
        $nombreBillet=$session->get('nombre_total_ticket');
        $dateBillet=$session->get('date_billet');

        $ListInfoClient= array([$nombreBillet]);
        for($i=1;$i<=$nombreBillet;$i++){
            $ListInfoClient[$i-1]=new InfoClient();
           // $form=$this->createForm(InfoClientType::class, $ListInfoClient[$i-1]);
        
        }

        //$session=$request->getSession();
        //$dateBillet=$session->get('date_billet');

        //$ListInfoClient=array([$dateBillet]);
        //$ListInfoClient=new InfoClient();
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

    }*/


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



