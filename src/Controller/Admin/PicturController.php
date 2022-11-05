<?php

namespace App\Controller\Admin;

use App\Entity\Pictur;
use App\Entity\Product;
use App\Form\PicturType;
use App\Repository\PicturRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('admin/pictur')]
class PicturController extends AbstractController
{
    #[Route('/', name: 'app_pictur_index', methods: ['GET'])]
    public function index(PicturRepository $picturRepository): Response
    {
        return $this->render('pictur/index.html.twig', [
            'picturs' => $picturRepository->findAll(),
        ]);
    }

    #[Route('/new/{id}', name: 'app_pictur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PicturRepository $picturRepository, Product $product): Response
    {
        $pictur = new Pictur();
        $form = $this->createForm(PicturType::class, $pictur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // code début image
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
                $pictur->setImage($new_name_image);
            } else {
                $pictur->setImage("defaultImage.jpeg");
            }
            // code fin image

            $pictur->setProduct($product);
            $picturRepository->save($pictur, true);

            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('pictur/new.html.twig', [
            'pictur' => $pictur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pictur_show', methods: ['GET'])]
    public function show(Pictur $pictur): Response
    {
        return $this->render('pictur/show.html.twig', [
            'pictur' => $pictur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_pictur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Pictur $pictur, PicturRepository $picturRepository): Response
    {
        $form = $this->createForm(PicturType::class, $pictur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $picturRepository->save($pictur, true);

            return $this->redirectToRoute('app_pictur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('pictur/edit.html.twig', [
            'pictur' => $pictur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pictur_delete', methods: ['POST'])]
    public function delete(Request $request, Pictur $pictur, PicturRepository $picturRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pictur->getId(), $request->request->get('_token'))) {
            $picturRepository->remove($pictur, true);
        }

        // ça va donner l'id du produit
        // $pictur->getProduct()->getId();
        return $this->redirectToRoute('app_product_show', ['id' => $pictur ->getProduct()->getId() ], Response::HTTP_SEE_OTHER);
    }
}
