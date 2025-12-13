<?php

namespace App\Tests\Controller\Api;

use App\Entity\Expense;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ExpenseApiControllerTest extends WebTestCase
{
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        parent::setUp();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
    }

    public function testGetExpensesList(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/expenses');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testCreateExpense(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/expenses', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'description' => 'API Test Expense',
            'amount' => '45.99',
            'category' => 'Food',
            'date' => '2025-12-13',
        ]));

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('API Test Expense', $data['description']);
        $this->assertEquals('45.99', $data['amount']);
    }

    public function testGetExpenseById(): void
    {
        $expense = new Expense();
        $expense->setDescription('Test API Expense');
        $expense->setAmount('50.00');
        $expense->setCategory('Transport');
        $this->em->persist($expense);
        $this->em->flush();

        $client = static::createClient();
        $client->request('GET', '/api/expenses/' . $expense->getId());

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Test API Expense', $data['description']);
    }

    public function testUpdateExpense(): void
    {
        $expense = new Expense();
        $expense->setDescription('Original');
        $expense->setAmount('20.00');
        $this->em->persist($expense);
        $this->em->flush();

        $client = static::createClient();
        $client->request('PUT', '/api/expenses/' . $expense->getId(), [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'description' => 'Updated via API',
            'amount' => '25.00',
        ]));

        $this->assertResponseIsSuccessful();
        $this->em->refresh($expense);
        $this->assertEquals('Updated via API', $expense->getDescription());
        $this->assertEquals('25.00', $expense->getAmount());
    }

    public function testDeleteExpense(): void
    {
        $expense = new Expense();
        $expense->setDescription('To Delete');
        $expense->setAmount('10.00');
        $this->em->persist($expense);
        $this->em->flush();

        $expenseId = $expense->getId();

        $client = static::createClient();
        $client->request('DELETE', '/api/expenses/' . $expenseId);

        $this->assertResponseStatusCodeSame(204);

        $deletedExpense = $this->em->getRepository(Expense::class)->find($expenseId);
        $this->assertNull($deletedExpense);
    }

    public function testGetExpensesByCategory(): void
    {
        $expense1 = new Expense();
        $expense1->setDescription('Food 1');
        $expense1->setAmount('15.00');
        $expense1->setCategory('Food');
        $this->em->persist($expense1);

        $expense2 = new Expense();
        $expense2->setDescription('Transport 1');
        $expense2->setAmount('25.00');
        $expense2->setCategory('Transport');
        $this->em->persist($expense2);

        $this->em->flush();

        $client = static::createClient();
        $client->request('GET', '/api/expenses?category=Food');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(1, $data);
        $this->assertEquals('Food 1', $data[0]['description']);
    }

    public function testGetStatsSummary(): void
    {
        $expense1 = new Expense();
        $expense1->setDescription('Expense 1');
        $expense1->setAmount('30.00');
        $expense1->setCategory('Food');
        $this->em->persist($expense1);

        $expense2 = new Expense();
        $expense2->setDescription('Expense 2');
        $expense2->setAmount('20.00');
        $expense2->setCategory('Transport');
        $this->em->persist($expense2);

        $this->em->flush();

        $client = static::createClient();
        $client->request('GET', '/api/expenses/stats/summary');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('total', $data);
        $this->assertArrayHasKey('count', $data);
        $this->assertArrayHasKey('byCategory', $data);
    }
}
