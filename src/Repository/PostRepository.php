<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function save(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Post[] Returns an array of Post objects
     */
    public function findByQuery($value, $limit): array
    {
        return $this->createQueryBuilder('p')
            ->select('pr.name_fr as promptNameFr, u.username as owner, p.fileName, p.id')
            ->leftJoin('p.prompt', 'pr')
            ->leftJoin('p.user', 'u')
            ->innerJoin('pr.promptList', 'prl')
            ->where('pr.name_fr LIKE :val')
            ->orWhere('pr.name_en LIKE :val')
            ->orWhere('pr.dayNumber LIKE :val')
            ->orWhere('u.username LIKE :val')
            ->orWhere('prl.year LIKE :val')
            ->setParameter('val', '%' . $value . '%')
            ->orderBy('pr.dayNumber', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findAllByYear($year): array
    {
        $now = new \DateTimeImmutable();

        return $this->createQueryBuilder('post')
            ->select('post.fileName, user.username as owner, prompt.dayNumber, prompt.name_fr as promptNameFr, promptList.year as promptListYear, post.id')
            ->leftJoin('post.prompt', 'prompt')
            ->leftJoin('prompt.promptList', 'promptList')
            ->leftJoin('post.user', 'user')
            ->where('promptList.year = :val')
            ->setParameter('val', $year)
            ->orderBy('prompt.dayNumber')
            ->getQuery()
            ->getResult();
    }

    public function findAllByUserAndYear($userUsername, $year)
    {
        return $this->createQueryBuilder('post')
            ->select('post.id, post.fileName, user.username as owner, prompt.dayNumber, prompt.name_fr as promptNameFr, promptList.year as promptListYear')
            ->leftJoin('post.prompt', 'prompt')
            ->leftJoin('prompt.promptList', 'promptList')
            ->leftJoin('post.user', 'user')
            ->where('user.username = :val')
            ->andWhere('promptList.year = :val2')
            ->setParameter('val', $userUsername)
            ->setParameter('val2', $year)
            ->orderBy('prompt.dayNumber')
            ->getQuery()
            ->getResult();
    }

    public function findAllByUser($userUsername)
    {
        return $this->createQueryBuilder('post')
            ->select('post.id, post.fileName, user.username as owner, prompt.dayNumber, prompt.name_fr as promptNameFr, promptList.year as promptListYear')
            ->leftJoin('post.prompt', 'prompt')
            ->leftJoin('prompt.promptList', 'promptList')
            ->leftJoin('post.user', 'user')
            ->where('user.username = :val')
            ->setParameter('val', $userUsername)
            ->orderBy('prompt.dayNumber')
            ->getQuery()
            ->getResult();
    }

    public function findAllByPrompt($promptNameEn)
    {
        return $this->createQueryBuilder('post')
            ->select('post.id, post.fileName, user.username as owner, prompt.dayNumber, prompt.name_fr as promptNameFr, promptList.year as promptListYear')
            ->leftJoin('post.prompt', 'prompt')
            ->leftJoin('prompt.promptList', 'promptList')
            ->leftJoin('post.user', 'user')
            ->where('prompt.name_en = :val')
            ->setParameter('val', $promptNameEn)
            ->orderBy('prompt.dayNumber')
            ->getQuery()
            ->getResult();
    }

    //    public function findOneBySomeField($value): ?Post
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
