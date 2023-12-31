<?php

namespace App\Repository;

use App\Entity\Prompt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Prompt>
 *
 * @method Prompt|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prompt|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prompt[]    findAll()
 * @method Prompt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prompt::class);
    }

    public function findByYear($year)
    {
        return $this->createQueryBuilder('prompt')
            ->select('prompt.dayNumber, prompt.name_fr as nameFr, prompt.name_en as nameEn')
            ->leftJoin('prompt.promptList', 'promptList')
            ->where('promptList.year = ' . $year)
            ->orderBy('prompt.dayNumber')
            ->getQuery()
            ->getResult();
    }
}
