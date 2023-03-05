<?php

namespace App\Repository;

use App\Entity\Depenses;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Depenses>
 *
 * @method Depenses|null find($id, $lockMode = null, $lockVersion = null)
 * @method Depenses|null findOneBy(array $criteria, array $orderBy = null)
 * @method Depenses[]    findAll()
 * @method Depenses[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepensesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Depenses::class);
    }

    public function save(Depenses $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Depenses $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function sommedepense() {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT SUM(d.montant) FROM App\Entity\Depenses d');
        return $query->getResult();
    }
    public function affichetout() {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT * FROM App\Entity\Depenses d');
        return $query->getResult();
    }
    public function statisticsbymethod($userName){
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT COUNT(d.type_depense) as count FROM App\Entity\Depenses d WHERE d.type_depense LIKE :username')
                    ->setParameter('username',$userName);
        return $query->getResult();
    }
    public function studentSearchByUserNameDQL($userName){
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT SUM(d.montant) as count FROM App\Entity\Depenses d WHERE d.categorie_depense LIKE :username')
                    ->setParameter('username',$userName);
        return $query->getResult();
    }
    
    public function depensesSearchBytitle($userName) {
        return $this->createQueryBuilder('d')
                ->where(':titre IS NULL or d.title LIKE :titre ')
                ->andwhere(':prenom IS NULL or d.prenom_destinataire LIKE :prenom  ')
                ->andwhere(':rib IS NULL or d.rib_destinataire LIKE :rib  ')
                ->andwhere(':montant IS NULL or d.montant LIKE :montant  ')
                ->andwhere(':datedebut IS NULL or d.datedebut LIKE :datedebut  ')
                ->andwhere(':categorie_depense IS NULL or d.categorie_depense LIKE :categorie_depense  ')
                ->andwhere(':type_depense IS NULL or d.type_depense LIKE :type_depense')
                ->setParameter('titre',$userName['titre'])
                ->setParameter('prenom',$userName['prenom'])
                ->setParameter('rib',$userName['rib'])
                ->setParameter('montant',$userName['montant'])
                ->setParameter('datedebut',$userName['datedebut'])
                ->setParameter('categorie_depense',$userName['categorie_depense'])
                ->setParameter('type_depense',$userName['type_depense'])
                ->getQuery()
                ->getResult();
                
    }

//    /**
//     * @return Depenses[] Returns an array of Depenses objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Depenses
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
