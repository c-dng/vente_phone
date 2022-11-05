<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('admin/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //____________________________
            // début code ajouter image
            // je récupére la photo du formulaire 
            $image = $form->get('image')->getData();
            if (!is_null($image)) {
                // création d'un nom unique pour la photo
                $new_name_image = uniqid() . '.' . $image->guessExtension();
                // envoi de la photo dans le dossier "photos" sur le server 
                $image->move(
                    // premier param le chemin 
                    $this->getParameter('upload_dir'),
                    //le second param , le new name de la photo
                    $new_name_image
                );
                $product->setImage($new_name_image);
            } else {
                $product->setImage("defaultImage.jpeg");
            }
            // fin code ajouter image
            //____________________________
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $old_name_image = $product->getImage();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // debut code modif photo
            $image = $form->get('image')->getData();
            if (!is_null($image)) {
                // création d'un nom unique pour la photo
                $new_name_image = uniqid() . '.' . $image->guessExtension();
                // envoi de la photo dans le dossier "photos" sur le server 
                $image->move(
                    // premier param le chemin 
                    $this->getParameter('upload_dir'),
                    //le second param , le new name de la photo
                    $new_name_image
                );
                $product->setImage($new_name_image);
            } else {
                $product->setImage($old_name_image);
            }
            // fin code modif image

            $productRepository->save($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
