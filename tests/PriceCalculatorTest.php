<?php
namespace App\tests;
use PHPUnit\Framework\TestCase;
use App\Service\PriceCalculator\PriceCalculator;
use App\Service\AgeCalculator\AgeCalculator;

class PriceCalculatorTest extends TestCase
{

    public function testPrice1()
    {
        $price = new PriceCalculator();
        $anneeAjd=date('Y');
        $moisAjd=date('m');
        $jourAjd=date('d');
        $result = $price ->getTarifClient($anneeAjd - 3, $moisAjd, $jourAjd, false, 'Journée');
        $this ->assertEquals(0, $result);
    }

    public function testPrice2()
    {
        $price = new PriceCalculator();
        $anneeAjd=date('Y');
        $moisAjd=date('m');
        $jourAjd=date('d');
        $result = $price ->getTarifClient($anneeAjd - 7, $moisAjd, $jourAjd, false, 'Demi-journée');
        $this ->assertEquals(4, $result);
    }

    public function testPrice3()
    {
        $price = new PriceCalculator();
        $anneeAjd=date('Y');
        $moisAjd=date('m');
        $jourAjd=date('d');
        $result = $price ->getTarifClient($anneeAjd - 27, $moisAjd, $jourAjd, false, 'Demi-journée');
        $this ->assertEquals(8, $result);
    }

    public function testPrice4()
    {
        $price = new PriceCalculator();
        $anneeAjd=date('Y');
        $moisAjd=date('m');
        $jourAjd=date('d');
        $result = $price->getTarifClient($anneeAjd - 27, $moisAjd, $jourAjd, true, 'Demi-journée');
        $this->assertEquals(5, $result);
    }

    public function testPrice5()
    {
        $price = new PriceCalculator();
        $anneeAjd=date('Y');
        $moisAjd=date('m');
        $jourAjd=date('d');
        $result = $price->getTarifClient($anneeAjd - 62, $moisAjd, $jourAjd, false, 'Demi-journée');
        $this->assertEquals(6, $result);
    }
    
    public function testAge1()
    {
        $age = new AgeCalculator();
        $anneeAjd=date('Y');
        $moisAjd=date('m');
        $jourAjd=date('d');
        $result = $age->AgeCalculator($anneeAjd - 27, $moisAjd, $jourAjd);
        $this->assertEquals(27, $result);
    }
    
    public function testAge2()
    {
        $age = new AgeCalculator();
        $anneeAjd=date('Y');
        $result = $age->AgeCalculator($anneeAjd, 1, 1);
        $this->assertEquals(0, $result);
    }

}
?>