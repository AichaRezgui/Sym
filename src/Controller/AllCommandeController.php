<?php
namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AllCommandeController extends AbstractController
{
    #[Route('/admin/commandes', name: 'all_commandes')]
    public function index(CommandeRepository $commandeRepository): Response
    {
        $commandes = $commandeRepository->findAll();

        return $this->render('all_commande/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/admin/commandes/delete/{id}', name: 'delete_commande')]
    public function delete(Commande $commande, EntityManagerInterface $entityManager): Response
    {
   
        foreach ($commande->getLigneCommandes() as $ligneCommande) {
            $entityManager->remove($ligneCommande);
        }

        $entityManager->remove($commande);
        $entityManager->flush();

        $this->addFlash('success', 'La commande a été supprimée avec succès.');

        return $this->redirectToRoute('all_commandes');
    }
}
