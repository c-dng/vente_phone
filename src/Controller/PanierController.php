<?php

namespace App\Controller;

use App\Services\Panier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(Panier $panier): Response
    {
        // tester si l'ajout d'un article panier fonctionne
        // $panier->addProduitPanier(6);

        // tester si le delete panier fonctionne
        // $panier->deletePanier();

        //tester la méthode deleteProduitPanier permettant de supprimer l'id 3
        // $panier->deleteProduitPanier(3);

        //tester la méthode deleteQuantityProductPanier
        // $panier->deleteQuantityProductPanier(6);

        return $this->render('panier/index.html.twig', [
            'panier' => $panier->getDetailPanier(),
            'totalTTC' => $panier->getTotalPanier()
        ]);
    }

    // cette méthode va ajouter un produit au panier
    #[Route('/ajoute-panier/{id}', name: 'app_add_produit_panier')]
    public function addProduit($id, Panier $panier): Response
    {
       $panier->addProduitPanier($id);
       return $this->redirectToRoute('app_panier');
    }

    // cette méthode va supprimer TOUT le panier
    #[Route('/effacer-panier', name: 'app_effacer_panier')]
    public function deletePanier(Panier $panier): Response
    {
       $panier->deletePanier();
       return $this->redirectToRoute('app_panier');
    }

    // cette méthode va supprimer 1 produit
    #[Route('/supprimer-produit-panier/{id}', name: 'app_delete_produit_panier')]
    public function deleteProduit($id, Panier $panier): Response
    {
        $panier->deleteProduitPanier($id);
        return $this->redirectToRoute('app_panier');
    }

     // cette méthode va supprimer 1 produit
    #[Route('/supprimer-quantite-produit-panier/{id}', name: 'app_delete_quantity_produit')]
    public function deleteQuantityProduit($id, Panier $panier): Response
    {
        $panier->deleteQuantityProductPanier($id);
        return $this->redirectToRoute('app_panier');
    }


}
