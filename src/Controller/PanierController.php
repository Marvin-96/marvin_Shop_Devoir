<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PanierRepository;
use App\Entity\Panier;
use App\Entity\Commande;
use App\Form\PanierType;
use App\Form\CommandeType;
use Symfony\Component\HttpFoundation\Request;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="panier")
     */
    public function index(Panier $Panier= null, Request $request, PanierRepository $panierRepository, $id=null  )
        {

            $em = $this->getDoctrine()->getManager();
            $commandes = $em->getRepository(Commande::class)->findAll();
            
            $commande = new Commande(); 
            $Panier = $this->getDoctrine()
                        ->getRepository(Panier::class)
                        ->findby(['etat' => false,
                                'User' => $this->getUser()->getId() 
                        ]);
    
            
            $form = $this->createForm(CommandeType::class, $commande);
            $form->handleRequest($request);
    
            if($form->isSubmitted() && $form->isValid()){
                $commande->setDateDachat(new \DateTime()); // On set la date d'ajout au panier
                $commande->setPanier($Panier); // On set le panier ajouté au commandes
                $commande->setEtat(true); // on recupere l'id de l'user
                $em->persist($commande); 
                $em->flush();
            }
    
            return $this->render('panier/index.html.twig', [
                'paniers' => $panierRepository->findAll(),
                'commande_add' => $form->createView(),
            ]);
            }


    /**
     * @Route("/panierlist", name="panier_list")
     */
    public function panierList(Panier $Panier= null, Request $request, PanierRepository $panierRepository, $id=null  )
    {

        $em = $this->getDoctrine()->getManager();

            return $this->render('user/panierlist.html.twig', [
                'paniers' => $panierRepository->findAll(),
            ]);

        }


    /**
     * @Route("/panier/delete/{id}", name="panier_delete")
     */
    public function delete(Panier $panier=null){

        if($panier != null){
            $em=$this->getDoctrine()->getManager();
            $em->remove($panier);
            $em->flush();

            $this->addFlash('success', 'Produit supprimée du panier');
        }
        return $this->redirectToRoute('panier');

    }


     /**
     * @Route("/panier/delete/{id}", name="panier_delete_super")
     */
    public function deleteSuper(Panier $panier=null){

        if($panier != null){
            $em=$this->getDoctrine()->getManager();
            $em->remove($panier);
            $em->flush();

            $this->addFlash('success', 'Produit supprimée du panier');
        }
        return $this->redirectToRoute('panier_list');

    }
}
