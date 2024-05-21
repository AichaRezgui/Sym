<?php

namespace App\Repository;

use App\Entity\Livres;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livres>
 *
 * @method Livres|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livres|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livres[]    findAll()
 * @method Livres[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livres::class);
    }

   /**
    * @return Livres[] Returns an array of Livres objects
    */
   public function findGreaterThan($prix): array
   {
       return $this->createQueryBuilder('l')
           ->andWhere('l.prix > :val')
            ->setParameter('val', $prix)
            ->orderBy('l.id', 'ASC')
           ->setMaxResults(10)
           ->getQuery()
           ->getResult()
       ;
    }
    public function searchBooks($query)
    {
        return $this->createQueryBuilder('l')
            ->where('l.titre LIKE :query')
            ->orWhere('l.autheur LIKE :query')
        
            ->setParameter('query', '%'.$query.'%')
            ->getQuery()
            ->getResult();
    }
    public function findLivresPaginated(int $page, int $limit, ?string $categorie, ?float $prixMin, ?float $prixMax)
    {
        $query = $this->createQueryBuilder('l')
            ->orderBy('l.titre', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);
    
        if ($categorie) {
            $query->andWhere('l.Categorie = :categorie')
                ->setParameter('categorie', $categorie);
        }
    
        if ($prixMin) {
            $query->andWhere('l.prix >= :prixMin')
                ->setParameter('prixMin', $prixMin);
        }
    
        if ($prixMax) {
            $query->andWhere('l.prix <= :prixMax')
                ->setParameter('prixMax', $prixMax);
        }
    
        $paginator = new Paginator($query->getQuery(), true);
        $totalItems = count($paginator);
        $pages = ceil($totalItems / $limit);
    
        return [
            'data' => $paginator,
            'page' => $page,
            'pages' => $pages,
        ];
    }
    



//    public function findOneBySomeField($value): ?Livres
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

//    /**
//     * @return Livres[] Returns an array of Livres objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }
}
