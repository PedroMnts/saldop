<?php

namespace App\Tests\Entity;

use App\Entity\Gasto;
use PHPUnit\Framework\TestCase;

class GastoTest extends TestCase
{
    public function testGastoCreation(): void
    {
        $gasto = new Gasto();
        $gasto->setDescripcion('Compra de alimentos');
        $gasto->setMonto('50.00');
        $gasto->setCategoria('Alimentación');

        $this->assertEquals('Compra de alimentos', $gasto->getDescripcion());
        $this->assertEquals('50.00', $gasto->getMonto());
        $this->assertEquals('Alimentación', $gasto->getCategoria());
        $this->assertNotNull($gasto->getFecha());
        $this->assertNotNull($gasto->getCreatedAt());
    }

    public function testGastoDefaultValues(): void
    {
        $gasto = new Gasto();

        $this->assertNull($gasto->getId());
        $this->assertNull($gasto->getDescripcion());
        $this->assertNull($gasto->getMonto());
        $this->assertNull($gasto->getCategoria());
        $this->assertNotNull($gasto->getFecha());
        $this->assertNotNull($gasto->getCreatedAt());
    }
}
