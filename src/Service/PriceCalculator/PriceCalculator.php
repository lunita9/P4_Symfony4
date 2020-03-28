<?php

namespace App\Service\PriceCalculator;

use App\Service\AgeCalculator\AgeCalculator;


class PriceCalculator
{
   
    public function getTarifClient($anneeNaissance, $moisNaissance, $jourNaissance, $reduit, $typeJour){
       
        $tarifs = file_get_contents('prixTarifs.json', true);
        
        $tarifs= json_decode($tarifs,true);
        
        if ($reduit=='Oui' && $typeJour=='Journée') return $tarifs['reduitJ']; 
        if ($reduit=='Oui' && $typeJour=='Demi-journée') return $tarifs['reduitDJ'];
        
        $ageCalculator = new AgeCalculator();
        $age=$ageCalculator->AgeCalculator($anneeNaissance, $moisNaissance, $jourNaissance);
        if ($age < 4 )   return $tarifs['nourrisson'];
        if ($age >= 4 && $age < 12 && $typeJour=='Journée')  return $tarifs['enfantJ'];
        if ($age >= 4 && $age < 12 && $typeJour=='Demi-journée') return $tarifs['enfantDJ'];
        if ($age >= 12 && $age < 60 && $typeJour=='Journée') return $tarifs['normalJ'];
        if ($age >= 12 && $age < 60 && $typeJour=='Demi-journée') return $tarifs['normalDJ'];
        if ($age >= 60 && $typeJour=='Journée') return $tarifs['seniorJ'];
        if ($age >= 60 && $typeJour=='Demi-journée') return $tarifs['seniorDJ'];
        return $tarifs['normalJ'];
    }

	public function getTarifs() {
        $tarifs = file_get_contents('prixTarifs.json', true);
        $tarifs= json_decode($tarifs,true);
	    return $tarifs;
	}

}
?>