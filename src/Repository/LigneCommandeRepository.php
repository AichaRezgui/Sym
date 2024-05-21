<?php
namespace App\Repository;

use App\Entity\LigneCommande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LigneCommande>
 *
 * @method LigneCommande|null find($id, $lockMode = null, $lockVersion = null)
 * @method LigneCommande|null findOneBy(array $criteria, array $orderBy = null)
 * @method LigneCommande[]    findAll()
 * @method LigneCommande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LigneCommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LigneCommande::class);
    }

    /**
     * @return LigneCommande[] Returns an array of LigneCommande objects
     */
    public function findByLivre($livreId): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.livre = :livreId')
            ->setParameter('livreId', $livreId)
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
