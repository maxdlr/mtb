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
}
