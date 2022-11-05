<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function PHPUnit\Framework\isNull;

#[Route('/admin/commande')]
class CommandeController extends AbstractController
{
    // ? pour default null si !null mettre $param = 'paris'
    #[Route('/liste-commande/{param?}', name: 'app_commande_index')]
    public function index($param, CommandeRepository $commandeRepository): Response
    {
        // si param n'est pas null - d'abord je test si c'est nul, si c'est oui, j'envoie toutes les commandes sans filtre
        // si c'est no, je filtre entre les trues et les false
        if (!is_null($param)) {
            if ($param) {
                $commande = $commandeRepository->findBy(['status' => true]);
            } else {
                $commande = $commandeRepository->findBy(['status' => false]);
            }
        } else {
            $commande = $commandeRepository->findBy(['status' => false]);
        }

        return $this->render('commande/index.html.twig', [
            'commandes' => $commande
        ]);
    }

    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CommandeRepository $commandeRepository): Response
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandeRepository->save($commande, true);

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, CommandeRepository $commandeRepository): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandeRepository->save($commande, true);

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, CommandeRepository $commandeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $commandeRepository->remove($commande, true);
        }

        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('modif-status-livraison/{id}', name: 'app_modif_status_commande', methods: ['GET'])]
    public function modifStatus(Commande $commande, CommandeRepository $commandeRepository): Response
    {
        // je set (modifier la valeur de la propriété)
        $commande->setStatus(true);
        $commandeRepository->save($commande, true);
        return $this->redirectToRoute('app_commande_show', ['id' => $commande->getId()]);
    }

}
