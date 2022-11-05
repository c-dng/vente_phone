<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Repository\DetailCommandeRepository;
use App\Services\CommandeManager;
use App\Services\Panier;
use App\Services\Tools;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AchatController extends AbstractController
{
    #[Route('/achat', name: 'app_achat')]
    public function index(CommandeManager $commandeManager,
                          Panier $panier,
                          EntityManagerInterface $manager,
                          Tools $tools): Response
    {

        // si personne est connecté
        if (!$this->getUser()) {
        $this->redirectToRoute('app_login');
        }

        if($tools->testDonneeUser()){
            return $this->redirectToRoute('app_form_user');
        }

        // Je crée un objet Commande
        $commande = $commandeManager->getCommande();
        // je persiste l'objet commande mais j'envoie pas en BDD
        $manager->persist($commande);
        $tableau_detail_panier = $panier->getDetailPanier();


        if (!empty($tableau_detail_panier)) {
            foreach ($tableau_detail_panier as $ligne_panier) {
                $detail_commande = $commandeManager->getDetailCommande($commande, $ligne_panier);
                $manager->persist($detail_commande);
            }
        }
        
        $manager->flush();
        $panier->deletePanier();
        // commence le tunnel d'achat
        return $this->redirectToRoute('app_home');
    }
}
