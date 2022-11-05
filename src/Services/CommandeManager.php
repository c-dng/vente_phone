<?php

namespace App\Services;

use App\Entity\Commande;
use App\Entity\DetailCommande;
use App\Services\Tools;
use DateTimeZone;

// use App\Services\Panier;

class CommandeManager
{

    private $tools;
    private $panier;

    public function __construct(Tools $tools, Panier $panier)
    {
        $this->tools = $tools;
        $this->panier = $panier;
    }

    /**
     * Créer un objet commande à partir du panier
     *  @return Commande
     */

    public function getCommande(){
        // je récupère l'utilisateur connecté
        $user = $this->tools->getUser();
        $commande = new Commande();
        //je rajoute l'utilisateur connecté
        $commande->setUser($user);
        // je rajoute le nom + prénom de cet utilisateur connecté
        $commande->setNom($user->getNom() . ' ' . $user->getPrenom());
        // ajouter l'adresse complète
        $commande->setAdresse($user->getAdresseComplete());
        // récupérer la date du jour
        // anti slash pour enlever le Use.
        $date_com = new \DateTime('', new DateTimeZone('Europe/Paris'));
        $commande->setDateCom($date_com);
        // J'ajoute le total du panier à la commande
        $commande->setTotal($this->panier->getTotalPanier());
        $commande->setStatus(false);
        
        return $commande;
    }

    /**
     * Créer un objet detailCommande prêt à être enregistré
     *  
     */

    public function getDetailCommande(Commande $commande, $ligne_panier){
        // création d'un objet detailCommande
        $detail_commande = new DetailCommande();
        $detail_commande->setCommande($commande);

        $detail_commande->setName($ligne_panier['product']->getName());
        $detail_commande->setRef($ligne_panier['product']->getRef());
        $detail_commande->setPrixUnit($ligne_panier['product']->getPrixUnit());
        $detail_commande->setQuantity($ligne_panier['quantity']);
        $detail_commande->setTotal($ligne_panier['total']);
    
        return $detail_commande;

    }

}