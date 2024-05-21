<?php
namespace App\Controller;

use App\Entity\Livres;
use App\Form\LivreType;
use App\Repository\LivresRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\LigneCommandeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LivresController extends AbstractController
{
    #[Route('/admin/livres', name: 'admin_livres')]
    public function index(LivresRepository $rep): Response
    {
        $livres = $rep->findAll();
        return $this->render('livres/index.html.twig', [
            'livres' => $livres,
        ]);
    }

    #[Route('/admin/livres/show/{id}', name: 'admin_livres_show')]
    public function show(Livres $livre): Response
    {
        return $this->render('livres/show.html.twig', [
            'livre' => $livre,
        ]);
    }

    #[Route('/admin/livres/create', name: 'admin_livres_create')]
    public function create(EntityManagerInterface $em): Response
    {
        $livre = new Livres();
        $livre->setImage('https://fastly.picsum.photos/id/695/300/300.jpg?hmac=6Sh_ZQoCQx4j-pNIfzKgLhZhNegGY3XK2PkI5MMeGHY')
              ->setTitre('Titre du livre 9')
              ->setEditeur('Editeur 1')
              ->setISBN('111.222.335.445')
              ->setPrix(150)
              ->setEditedAt(new \DateTimeImmutable('01-01-2024'))
              ->setSlug('titre-du-livre-5')
              ->setResumer('kjhgfdsdfghjkllkjhgfdsdfghjklkjhgfdfghjk');
        $em->persist($livre);
        $em->flush();

        // Ajouter un flash message de succès
        $this->addFlash('success', 'Le livre a été créé avec succès.');

        return $this->redirectToRoute('admin_livres');
    }

    #[Route('/admin/livres/delete/{id}', name: 'admin_livres_delete')]
    public function delete(EntityManagerInterface $em, Livres $livre, LigneCommandeRepository $ligneCommandeRepository): Response
    {
        $ligneCommandes = $ligneCommandeRepository->findByLivre($livre->getId());

        foreach ($ligneCommandes as $ligneCommande) {
            $em->remove($ligneCommande);
        }

        $em->remove($livre);
        $em->flush();

        $this->addFlash('success', 'Le livre a été supprimé avec succès.');

        return $this->redirectToRoute('admin_livres');
    }
    #[Route('/admin/livres/add', name: 'admin_livre_add')]
    public function add(EntityManagerInterface $em, Request $request): Response
    {
        $livre = new Livres();
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($livre);
            $em->flush();

            // Ajouter un flash message de succès
            $this->addFlash('success', 'Le livre a été ajouté avec succès.');

            return $this->redirectToRoute('admin_livres');
        }

        return $this->render('livres/add.html.twig', [
            'f' => $form,
        ]);
    }
}
