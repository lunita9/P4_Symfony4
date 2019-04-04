<?php

namespace App\Service\PriceCalculator;

use App\Service\AgeCalculator\AgeCalculator;


class PriceCalculator
{
    /*private $ageCalculator;

    public function __construct(ServiceAgeCalculator $ageCalculator)
	{
		$this->ageCalculator = $ageCalculator;
	}*/



//    public function getTarif($anneeNaissance, $moisNaissance, $jourNaissance, $reduit, $typeJour){
        public function getTarif($anneeNaissance, $moisNaissance, $jourNaissance, $reduit, $typeJour){
       

        

        //$age=$this->ageCalculator->
    
    /*
        
        $anneeNaissance=strtotime($reservation->getDateBillet()->format('Y'));
        $moisNaissance=strotime($reservation->getDateBillet()->format('m'));
        $jourNaissance=strotime($reservation->getDateBillet()->format('d'));
        
        $age = $anneeAjd - $anneeNaissance;
        if ($moisAjd < $moisNaissance) $age--;
        if ($moisAjd == $moisNaissance && $jourAjd < $jourNaissance) $age--;
        
        */
        

        
        
        
        $tarifs = file_get_contents('prixTarifs.json', true);
        //die(var_dump($tarifs));
        //$tarifs='{"reduitJ":10,"nourrisson":0,"enfantJ":8,"enfantDJ":5,"seniorJ":12,"seniorDJ":7,"reduitDJ":6,"normalJ":16,"normalDJ":9}';
        $tarifs= json_decode($tarifs,true);
        
        if ($reduit && $typeJour=='Journée') return $tarifs['reduitJ']; 
        if ($reduit && $typeJour=='Demi-journée') return $tarifs['reduitDJ'];

        /*$correctif = 0;
        $age = $anneeAjd - $anneeNaissance;
        if ($moisAjd < $moisNaissance) $correctif=1;
        if ($moisAjd == $moisNaissance && $jourAjd < $jourNaissance) $correctif=1;
        $age -= $correctif;*/
        
        $ageCalculator = new AgeCalculator();
        $age=$ageCalculator->AgeCalculator($anneeNaissance, $moisNaissance, $jourNaissance);
        if ($age < 4 )   return $tarifs['nourrisson'];
        if ($age < 12 && $typeJour=='Journée')  return $tarifs['enfantJ'];
        if ($age >= 60 && $typeJour=='Journée') return $tarifs['seniorJ'];
        if($age>=12 && $age<60 && $typeJour=='Journée') return $tarifs['normalJ'];
        if($age >=4 && $age< 12 && $typeJour=='Demi-journée' ) return $tarifs['enfantDJ'];
        if($age >= 60 && $typeJour=='Demi-journée') return $tarifs['seniorDJ'];
        return $tarifs['normalDJ'];

        
    }
    
}
?>