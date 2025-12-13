<?php

namespace App\Controller\Api;

use App\Entity\Expense;
use App\Repository\ExpenseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/expenses', name: 'api_expense_')]
class ExpenseApiController extends AbstractController
{
    public function __construct(
        private ExpenseRepository $repository,
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $category = $request->query->get('category');
        $startDate = $request->query->get('startDate');
        $endDate = $request->query->get('endDate');

        if ($category) {
            $expenses = $this->repository->findByCategory($category);
        } elseif ($startDate && $endDate) {
            $expenses = $this->repository->findByDateRange(
                new \DateTime($startDate),
                new \DateTime($endDate)
            );
        } else {
            $expenses = $this->repository->findAll();
        }

        return $this->json($expenses, context: ['groups' => 'expense:read']);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Expense $expense): JsonResponse
    {
        return $this->json($expense, context: ['groups' => 'expense:read']);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $expense = new Expense();
        $expense->setDescription($data['description'] ?? '');
        $expense->setAmount($data['amount'] ?? '0');
        $expense->setCategory($data['category'] ?? null);

        if (isset($data['date'])) {
            $expense->setDate(new \DateTime($data['date']));
        }

        $this->em->persist($expense);
        $this->em->flush();

        return $this->json($expense, status: Response::HTTP_CREATED, context: ['groups' => 'expense:read']);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, Expense $expense): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['description'])) {
            $expense->setDescription($data['description']);
        }
        if (isset($data['amount'])) {
            $expense->setAmount($data['amount']);
        }
        if (isset($data['category'])) {
            $expense->setCategory($data['category']);
        }
        if (isset($data['date'])) {
            $expense->setDate(new \DateTime($data['date']));
        }

        $this->em->flush();

        return $this->json($expense, context: ['groups' => 'expense:read']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Expense $expense): JsonResponse
    {
        $this->em->remove($expense);
        $this->em->flush();

        return $this->json(null, status: Response::HTTP_NO_CONTENT);
    }

    #[Route('/stats/summary', name: 'stats_summary', methods: ['GET'])]
    public function statsSummary(): JsonResponse
    {
        $expenses = $this->repository->findAll();

        $totalAmount = 0;
        $byCategory = [];

        foreach ($expenses as $expense) {
            $totalAmount += (float) $expense->getAmount();
            $category = $expense->getCategory() ?? 'Uncategorized';
            $byCategory[$category] = ($byCategory[$category] ?? 0) + (float) $expense->getAmount();
        }

        return $this->json([
            'total' => $totalAmount,
            'count' => count($expenses),
            'byCategory' => $byCategory,
        ]);
    }
}
