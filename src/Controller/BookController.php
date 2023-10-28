<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\SeatchbookType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }


    #[Route('/showBook', name: 'showBook')]
    public function showBook(BookRepository $BookRepository, Request $req): Response
    { // $book = $BookRepository->findall();
        $form = $this->createForm(SeatchbookType::class);
        $form->handleRequest($req);
        if ($form->isSubmitted()) {
            $datainput = $form->get('ref')->getData();
            $book = $BookRepository->SearchBook($datainput);
        }
        //$book = $BookRepository->orderbyauthor();
        //$book = $BookRepository->daterelease();
        //$book = $BookRepository->updatecategory(romance)
        return $this->renderForm('book/showbook.html.twig', [
            'book' => $book,
            'f' => $form
        ]);
    }



    #[Route('/addformBook', name: 'addformBook')]
    public function addformBook(ManagerRegistry $ManagerRegistry, Request $Req): Response
    {
        $em = $ManagerRegistry->getManager();
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($Req);
        if ($form->isSubmitted() and $form->isValid()) {
            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute('showBook');
        }
        return $this->renderForm('book/addformBook.html.twig', [
            'f' => $form
        ]);
    }


    #[Route('/showBookp', name: 'showBookp')]
    public function showBookp(BookRepository $BookRepository): Response
    {
        $book = $BookRepository->findby((['published' => true]));
        $numpublishedbooks = count($BookRepository->findBy(['published' => true]));
        $numpumublishedbooks = count($BookRepository->findBy(['published' => false]));

        return $this->render('book/showBookp.html.twig', [
            'numofpublishedbooks' => $numpublishedbooks,
            'numunpublishedbooks' => $numpumublishedbooks,
            'book' => $book


        ]);
    }



    #[Route('/editformBook /{ref}', name: 'editformBook')]
    public function editformBook(ManagerRegistry $ManagerRegistry, BookRepository $bookRepository, Request $Req, $ref): Response
    {
        $em = $ManagerRegistry->getManager();
        $dataid = $bookRepository->find($ref);
        $form = $this->createForm(BookType::class, $dataid);
        $form->handleRequest($Req);
        if ($form->isSubmitted() and $form->isValid()) {
            $em->persist($dataid);
            $em->flush();
            return $this->redirectToRoute('showBook');
        }
        return $this->renderForm('book/editformBook.html.twig', [
            'f' => $form
        ]);
    }


    #[Route('/removebook/{ref}', name: 'removebook')]
    public function REMOVEDBauthor($ref, BookRepository $BookRepository, ManagerRegistry $ManagerRegistry): Response
    {
        $em = $ManagerRegistry->getManager();
        $dataid = $BookRepository->find($ref);
        $em->remove($dataid);
        $em->flush();
        return $this->redirectToRoute('showBook');
    }



    #[Route('/ showbyrefbook/{ref}', name: 'showbyrefbook')]


    public function showbyrefbook($ref, BookRepository $repository)
    {
        $book = $repository->find($ref);


        return $this->render('book/showref.html.twig', ['b' => $book]);
    }


    #[Route('/showrefbook/{ref}', name: 'showrefbook')]


    public function showrefbook($ref, BookRepository $bookrepository): Response
    {
        $book = $bookrepository->showbyrefbook($ref);
        return $this->render('book/showrefbook.html.twig', [
            'book' => $book
        ]);
    }
}
