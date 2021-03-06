<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Panier;
use App\Form\PanierType;
use App\Form\ProduitType;
use App\Entity\User;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/")
 */
class ProduitController extends AbstractController
{
    /**
     * @Route("/", name="produit_index", methods={"GET"})
     */
    public function index(ProduitRepository $produitRepository): Response // On affiche tous les produits dans l'index
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    /**
     * @Route("/produit/new", name="produit_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response // On crée un nouveau produit dans "produit/new"
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $photo = $form->get('Photo')->getData();

            if($photo){
                $nomPhoto = uniqid().'.'.$photo->guessExtension();

                try{
                    $photo->move(
                        $this->getParameter('image_directory'),
                        $nomPhoto
                    );
                }
                catch(FileException $e){
                    $this->addFlash('error', "Impossible d'uploader l'image");
                    return $this->redirectToRoute('produit');
                }

                $produit->setPhoto($nomPhoto);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($produit);
                $entityManager->flush();

            return $this->redirectToRoute('produit_index');
        }
    }
        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
        }
    

    /**
     * @Route("/produit/{id}", name="produit_show")
     */
    public function show(Produit $produit=null, UserInterface $user, Request $request ): Response // On affiche le produit selectionner par l'user et on traite l'ajout au panier
    {
    
            $em = $this->getDoctrine()->getManager();
            $paniers = $em->getRepository(Panier::class)->findAll();
            
            $panier = new Panier();
            
            $form = $this->createForm(PanierType::class, $panier);
            $form->handleRequest($request);
    
            if($form->isSubmitted() && $form->isValid()){
                $panier->setDateAjout(new \DateTime()); // On set la date d'ajout au panier
                $panier->setProduit($produit); // On set le produit ajouté au panier
                $panier->setUser($user); // on recupere l'id de l'user
                $panier->setEtat(false); 
                $this->addFlash('success', 'Produit ajouté');
                $em->persist($panier); 
                $em->flush();
            }

        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
            'panier_add' => $form->createView(),
        ]);
    }

    /**
     * @Route("/produit/{id}/edit", name="produit_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Produit $produit): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Produit modifié');
            return $this->redirectToRoute('produit_index');
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/produit/{id}", name="produit_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Produit $produit): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('produit_index');
    }
}
