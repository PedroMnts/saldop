<?php

namespace App\Tests\Controller;

use App\Entity\Expense;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ExpenseControllerTest extends WebTestCase
{
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        parent::setUp();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
    }

    public function testExpenseIndexIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/expenses');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Expenses', $client->getResponse()->getContent());
    }

    public function testExpenseNewFormIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/expenses/new');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Add New Expense', $client->getResponse()->getContent());
    }

    public function testCreateExpense(): void
    {
        $client = static::createClient();
        $client->request('POST', '/expenses/new', [
            'description' => 'Test expense',
            'amount' => '25.50',
            'category' => 'Food',
            'date' => '2025-12-13',
        ]);

        $this->assertResponseRedirects('/expenses');

        $expense = $this->em->getRepository(Expense::class)->findOneBy(['description' => 'Test expense']);
        $this->assertNotNull($expense);
        $this->assertEquals('25.50', $expense->getAmount());
        $this->assertEquals('Food', $expense->getCategory());
    }

    public function testShowExpense(): void
    {
        $expense = new Expense();
        $expense->setDescription('Test expense');
        $expense->setAmount('50.00');
        $expense->setCategory('Transport');
        $this->em->persist($expense);
        $this->em->flush();

        $client = static::createClient();
        $client->request('GET', '/expenses/' . $expense->getId());

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Test expense', $client->getResponse()->getContent());
        $this->assertStringContainsString('$50.00', $client->getResponse()->getContent());
    }

    public function testEditExpense(): void
    {
        $expense = new Expense();
        $expense->setDescription('Original description');
        $expense->setAmount('30.00');
        $this->em->persist($expense);
        $this->em->flush();

        $client = static::createClient();
        $client->request('POST', '/expenses/' . $expense->getId() . '/edit', [
            'description' => 'Updated description',
            'amount' => '35.00',
            'category' => 'Food',
            'date' => '2025-12-13',
        ]);

        $this->assertResponseRedirects('/expenses/' . $expense->getId());

        $this->em->refresh($expense);
        $this->assertEquals('Updated description', $expense->getDescription());
        $this->assertEquals('35.00', $expense->getAmount());
    }

    public function testDeleteExpense(): void
    {
        $expense = new Expense();
        $expense->setDescription('To delete');
        $expense->setAmount('10.00');
        $this->em->persist($expense);
        $this->em->flush();

        $expenseId = $expense->getId();

        $client = static::createClient();
        $client->request('POST', '/expenses/' . $expenseId . '/delete', [
            '_token' => $this->generateToken('delete' . $expenseId),
        ]);

        $this->assertResponseRedirects('/expenses');

        $deletedExpense = $this->em->getRepository(Expense::class)->find($expenseId);
        $this->assertNull($deletedExpense);
    }

    private function generateToken(string $tokenId): string
    {
        return static::getContainer()->get('security.csrf.token_manager')->getToken($tokenId)->getValue();
    }
}
