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
        $result = $price ->getTarifClient(2016, 10, 12, false, 'Journée');
        $this ->assertEquals(0, $result);
    }

    public function testPrice2()
    {
        $price = new PriceCalculator();
        $result = $price ->getTarifClient(2010, 10, 12, false, 'Demi-journée');
        $this ->assertEquals(5, $result);
    }

    public function testPrice3()
    {
        $price = new PriceCalculator();
        $result = $price ->getTarifClient(1992, 10, 12, false, 'Demi-journée');
        $this ->assertEquals(9, $result);
    }

    public function testPrice4()
    {
        $price = new PriceCalculator();
        $result = $price->getTarifClient(1992, 10, 12, true, 'Demi-journée');
        $this->assertEquals(6, $result);
    }

    public function testPrice5()
    {
        $price = new PriceCalculator();
        $result = $price->getTarifClient(1958, 10, 12, false, 'Demi-journée');
        $this->assertEquals(7, $result);
    }

}
?>