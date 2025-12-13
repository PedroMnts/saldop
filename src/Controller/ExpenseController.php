<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Repository\ExpenseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/expenses', name: 'app_expense_')]
class ExpenseController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(ExpenseRepository $repository): Response
    {
        $expenses = $repository->findAll();

        return $this->render('expense/index.html.twig', [
            'expenses' => $expenses,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $expense = new Expense();

        if ($request->isMethod('POST')) {
            $expense->setDescription($request->request->get('description'));
            $expense->setAmount($request->request->get('amount'));
            $expense->setCategory($request->request->get('category'));

            if ($date = $request->request->get('date')) {
                $expense->setDate(new \DateTime($date));
            }

            $em->persist($expense);
            $em->flush();

            return $this->redirectToRoute('app_expense_index');
        }

        return $this->render('expense/new.html.twig', [
            'expense' => $expense,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Expense $expense): Response
    {
        return $this->render('expense/show.html.twig', [
            'expense' => $expense,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Expense $expense, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $expense->setDescription($request->request->get('description'));
            $expense->setAmount($request->request->get('amount'));
            $expense->setCategory($request->request->get('category'));

            if ($date = $request->request->get('date')) {
                $expense->setDate(new \DateTime($date));
            }

            $em->flush();

            return $this->redirectToRoute('app_expense_show', ['id' => $expense->getId()]);
        }

        return $this->render('expense/edit.html.twig', [
            'expense' => $expense,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Expense $expense, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $expense->getId(), $request->request->get('_token'))) {
            $em->remove($expense);
            $em->flush();
        }

        return $this->redirectToRoute('app_expense_index');
    }
}
