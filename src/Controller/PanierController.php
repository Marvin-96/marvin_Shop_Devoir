<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Produit;
use App\Repository\PanierRepository;
use App\Entity\Panier;
use App\Form\PanierType;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="panier")
     */
    public function index(PanierRepository $panierRepository )
        {
    
            return $this->render('panier/index.html.twig', [
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
}
