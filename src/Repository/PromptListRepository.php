<?php

namespace App\Repository;

use App\Entity\PromptList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PromptList>
 *
 * @method PromptList|null find($id, $lockMode = null, $lockVersion = null)
 * @method PromptList|null findOneBy(array $criteria, array $orderBy = null)
 * @method PromptList[]    findAll()
 * @method PromptList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromptListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PromptList::class);
    }

//    /**
//     * @return PromptList[] Returns an array of PromptList objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PromptList
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
