<?php

namespace App\Controller;

use App\Repository\LivresRepository;
use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AllLivresController extends AbstractController
{
    

#[Route('/all/livres', name: 'app_all_livres')]
public function lister(LivresRepository $rep, CategoriesRepository $categoriesRepo, Request $request): Response
{
    $page = $request->query->getInt('page', 1);
    $categorie = $request->query->get('categorie');
    $prixMin = $request->query->get('prix_min');
    $prixMax = $request->query->get('prix_max');

    $livres = $rep->findLivresPaginated($page, 8, $categorie, $prixMin, $prixMax);
    $categories = $categoriesRepo->findAll();

    return $this->render('all_livres/index.html.twig', [
        'livres' => $livres,
        'categories' => $categories,
        'currentCategorie' => $categorie,
        'currentPrixMin' => $prixMin,
        'currentPrixMax' => $prixMax,
    ]);
}


    #[Route('/all/livres/show/{id}', name: 'app_livre_details')]
    public function details(LivresRepository $rep,$id)
    {
       $livre=$rep->find($id);
       return $this->render('all_livres/detailsLivre.html.twig',[
        'livre'=>$livre,
       ]);
    }

    #[Route('/all/livres/search_books', name :"search_books")] 
    public function searchBooks(Request $request, LivresRepository $rep)
    {
       
        $query = $request->query->get('query');
        $livres = $rep->searchBooks($query);
        return $this->render('all_livres/search_results.html.twig', [
            'livres' => $livres,
        ]);
    }
    
}
