<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    // chercher les cards
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('home/index.html.twig', [
        'products' => $productRepository->findAll(),
        // on veut récupérer la liste des catégories
        'categorys' => $categoryRepository->findAll()
        ]);
    }

    // méthode filter categories
    #[Route('/liste-produit/{param}', name: 'app_filtre_produit')]
    public function filtre($param, ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'products' => $productRepository->findBy(['category' => $param ]),
            'categorys' => $categoryRepository->findAll()
        ]);
    }

    // methode pour afficher détail produit
    #[Route('/detail-produit/{id}', name: 'app_user_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('home/showDetail.html.twig', [
            'product' => $product,
        ]);
    }

}
