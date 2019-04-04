<?php

namespace App\Service\AgeCalculator;

use App\Entity\Reservation;
use App\Entity\InfoClient;



class AgeCalculator
{
    public function AgeCalculator($anneeNaissance, $moisNaissance, $jourNaissance )
    {
        //$anneeNaissance=strtotime($reservation->getDateBillet()->format('Y'));
        //$moisNaissance=strotime($reservation->getDateBillet()->format('m'));
        //$jourNaissance=strotime($reservation->getDateBillet()->format('d'));
        
        $anneeAjd=date('Y');
        $moisAjd=date('m');
        $jourAjd=date('d');

        $age = $anneeAjd - $anneeNaissance;
        if ($moisAjd < $moisNaissance) $age--;
        if ($moisAjd == $moisNaissance && $jourAjd < $jourNaissance) $age--;

        return $age;
    }
    
    
}        
?>