<?php

namespace App\Controller;


use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{ //tab here so that the whole routes and functions could use it 
    //tab declaration
    public $authors = array(
        array('id' => 1, 'picture' => '/images/Victor-Hugo.jpg', 'username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com ', 'nb_books' => 100),
        array('id' => 2, 'picture' => '/images/william-shakespeare.jpg', 'username' => ' William Shakespeare', 'email' =>  ' william.shakespeare@gmail.com', 'nb_books' => 200),
        array('id' => 3, 'picture' => '/images/Taha_Hussein.jpg', 'username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300),
    );

    //default
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    //affiche nom
    #[Route('/showauthor/{name}', name: 'app_showauthor')]
    public function showauthor($name): Response
    {
        return $this->render('author/show.html.twig', [
            'name' => $name
        ]);
    }


    #[Route('/showtable', name: 'showtable')]
    public function showtableauthor(): Response
    {

        return $this->render('author/showtable.html.twig', [
            'authors' => $this->authors //nom tab
        ]);
    }

    #[Route('/showbyidauthor/{id}', name: 'showbyidauthor')]
    public function showtbyidauthor($id): Response
    {
        //var_dump($id).die(); //die to stop

        $author = null;
        foreach ($this->authors as $authorD) {
            if ($authorD['id'] == $id) {
                $author = $authorD;
            }
        }
        // var_dump($author) . die();

        return $this->render('author/showbyidauthor.html.twig', [
            'author' => $author
        ]);
    }


    #[Route('/showdbauthor', name: 'app_showdbauthor')]
    public function showdbauthor(AuthorRepository $authorRepository): Response
    {
        //$author = $authorRepository->findAll();
        //$author = $authorRepository->orderbyusername();
        $author = $authorRepository->orderbyemail();
        return $this->render('author/showdb.html.twig', [
            'author' => $author
        ]);
    }

    #[Route('/addauthor', name: 'addauthor')]
    public function addauthor(ManagerRegistry $managerRegistry): Response
    { //static sans formulaire
        $em = $managerRegistry->getManager();
        $author = new Author();
        $author->setUsername("3A54new");
        $author->setEmail("3A54new@esprit.tn");
        $author->setNbBooks(100);
        $em->persist($author); //add and update requette 
        $em->flush(); //pour l exexution de la requette insert
        return new Response("great add");
    }

    #[Route('/addformauthor', name: 'addformauthor')]
    public function addformauthor(ManagerRegistry $managerRegistry, Request $req): Response
    {
        $em = $managerRegistry->getManager();
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($req);
        if ($form->isSubmitted() and $form->isValid()) { //if form is not empty
            $em->persist($author);
            $em->flush();
            return $this->redirectToRoute('app_showdbauthor');
        }

        return $this->renderForm('author/addformauthor.html.twig', [
            'f' => $form

        ]);
    }
    #[Route('/editauthor/{id}', name: 'editauthor')]
    public function editauthor($id, AuthorRepository $authorRepository, ManagerRegistry $managerRegistry, Request $req): Response
    { //var_dump($id).die;//pour verifier eli l id kaaed yetaada
        $em = $managerRegistry->getManager();
        $dataid = $authorRepository->find($id); //to take the id
        //var_dump($dataid).die;
        $form = $this->createform(AuthorType::class, $dataid); //create form but keep the data of the id
        $form->handleRequest($req);
        if ($form->isSubmitted() and $form->isValid()) {
            $em->persist($dataid); //to find the id
            $em->flush();
            return $this->redirectToRoute('app_showdbauthor');
        }
        return $this->renderForm('author/editauthor.html.twig', [
            'form' => $form
        ]);
    }


    #[Route('/deleteauthor/{id}', name: 'app_deleteauthor')]
    public function deleteauthor($id, AuthorRepository $authorRepository, ManagerRegistry $managerRegistry): Response
    {
        $em = $managerRegistry->getManager();
        $dataid = $authorRepository->find($id);
        $em->remove($dataid);
        $em->flush();
        return $this->redirectToRoute('app_showdbauthor');
    }
}
