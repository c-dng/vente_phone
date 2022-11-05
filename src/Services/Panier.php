<?php

namespace App\Services;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Panier 
{
     private $session;
     private $productRepository;

     public function __construct(SessionInterface $sessionInterface, ProductRepository $productRepository)
     {

        $this->session = $sessionInterface;
        $this->productRepository = $productRepository;
     }

    /** 1ère méthode - Cette méthode va nous permettre de récupère le panier
     * récup le tableau de la session
     * 
     * @return array
     */

    public function getPanier(){
        return $this->session->get('panier', []);
    }
    
    /** 2e méthode - Cette méthode va nous permettre d'ajouter un nouvel élément au panier
     * ajoute un produit au panier si le produit n'existe pas
     *  sinon elle rajouter 1 à la quantité
     * @param integer $id
     * @return void
     */

    public function addProduitPanier($id) {
        $panier = $this->getPanier();
        // si il y a l'id
        if(!empty($panier[$id])){
            // si le produit se trouve dans le panier je rajoute 1 à la quantité
            $panier[$id] ++;
            // deux autres syntaxes possibles à la ligne précédente
            // $panier[$id] = $panier[$id] + 1;
            // $panier[$id] = $panier[$id] + 1;
        } else {
            // sinon je rajoute le produit avec 1 comme quantité
            $panier[$id] = 1;
        }
        // j'update le panier dans la session
        $this->session->set('panier', $panier);
    }
    
    //  2e méthode - Cette méthode va nous permettre de supprimer la totalité du panier
    public function deletePanier(){
        $this->session->remove('panier');
    }

    /** 3e méthode - Cette méthode va nous permettre de supprimer un élément du panier
     * supprime un produit du panier
     *
     *  @param integer $id
     *  @return void
     */ 

    public function deleteProduitPanier($id){
        // on fait appel à la première méthode
        $panier = $this->getPanier();
        // si l'id s'y trouve alors tu vas me l'effacer
        if(!empty($panier[$id])){
            unset($panier[$id]);
        }
        // j'update le panier dans la session
        $this->session->set('panier', $panier);
    }

    /**
     *  3e méthode - Cette méthode va nous permettre de soustraire 1 à la quantité si elle est supérieure à 1 sinon
     *  je supprime le produit du panier
     * @param integer $id
     * @return void
     * 
     */

    public function deleteQuantityProductPanier($id){
        $panier = $this->getPanier();
        // je vérifie que l'id produit existe
        if(!empty($panier[$id])){
            if($panier[$id] > 1){
                $panier[$id] = $panier[$id] -1 ;
            } else{
                unset($panier[$id]);
            }
        }
        // j'update le panier dans la session
        $this->session->set('panier', $panier);
    }

    public function getDetailPanier(){
        // je récupère le panier
        $panier = $this->getPanier();
        // je crée un tableau vide
        $panier_detail = [];

        foreach ($panier as $id => $quantity) {

            // je récupère l'objet product par son id
            $product = $this->productRepository->find($id);
            // j'ajoute le product et la quantité au nouveau tableau
            
            if($product) {
                $panier_detail [] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'total' => $quantity * $product->getPrixUnit()
            ];
        }
        // dd($panier_detail);
        return $panier_detail;
    }
}
        /**
         * calcul le total du panier
         * 
         * @return float
         * 
         */

         public function getTotalPanier()
         {
            $panier = $this->getDetailPanier();
            $totalTTC = 0;

            if (!empty($panier)) {
                foreach ($panier as $row) {
                    $totalTTC = $totalTTC + $row['total'];
                } 
            } else {
                $totalTTC = 0;
            }

            return $totalTTC;
         }
    }


