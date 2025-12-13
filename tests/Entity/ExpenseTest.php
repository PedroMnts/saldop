<?php

namespace App\Tests\Entity;

use App\Entity\Expense;
use PHPUnit\Framework\TestCase;

class ExpenseTest extends TestCase
{
    public function testExpenseCreation(): void
    {
        $expense = new Expense();
        $expense->setDescription('Grocery shopping');
        $expense->setAmount('50.00');
        $expense->setCategory('Food');

        $this->assertEquals('Grocery shopping', $expense->getDescription());
        $this->assertEquals('50.00', $expense->getAmount());
        $this->assertEquals('Food', $expense->getCategory());
        $this->assertNotNull($expense->getDate());
        $this->assertNotNull($expense->getCreatedAt());
    }

    public function testExpenseDefaultValues(): void
    {
        $expense = new Expense();

        $this->assertNull($expense->getId());
        $this->assertNull($expense->getDescription());
        $this->assertNull($expense->getAmount());
        $this->assertNull($expense->getCategory());
        $this->assertNotNull($expense->getDate());
        $this->assertNotNull($expense->getCreatedAt());
    }
}
