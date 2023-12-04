<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Collection;

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
    private string $fileName = 'fileName';
    private string $owner = 'owner';
    private string $dayNumber = 'dayNumber';
    private string $promptNameFr = 'promptNameFr';
    private string $promptNameEn = 'promptNameEn';
    private string $promptListYear = 'promptListYear';
    private string $id = 'id';
    private string $date = 'date';

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
    public function findByQuery($query, $limit, $orderBy, string $ascDesc = 'ASC'): array
    {
        return $this->createQueryBuilder('post')
            ->select(
                'post.id as ' . $this->id . ',
                post.fileName as ' . $this->fileName . ', 
                post.uploadedOn as ' . $this->date . ',
                user.username as ' . $this->owner . ', 
                prompt.dayNumber as ' . $this->dayNumber . ', 
                prompt.name_fr as ' . $this->promptNameFr . ', 
                prompt.name_en as ' . $this->promptNameEn . ', 
                promptList.year as ' . $this->promptListYear
            )
            ->leftJoin('post.prompt', 'prompt')
            ->leftJoin('post.user', 'user')
            ->innerJoin('prompt.promptList', 'promptList')
            ->where('prompt.name_fr LIKE :query')
            ->orWhere('prompt.name_en LIKE :query')
            ->orWhere('prompt.dayNumber LIKE :query')
            ->orWhere('user.username LIKE :query')
            ->orWhere('promptList.year LIKE :query')
            ->andWhere('post.prompt IS NOT NULL')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy($orderBy, $ascDesc)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Post[] Returns an array of Post objects
     */
    public function findByQueryByUser(string $query, User $owner, int $limit, string $orderBy, string $ascDesc = 'ASC'): array
    {
        return $this->createQueryBuilder('post')
            ->select(
                'post.id as ' . $this->id . ',
                post.fileName as ' . $this->fileName . ', 
                post.uploadedOn as ' . $this->date . ',
                user.username as ' . $this->owner . ', 
                prompt.dayNumber as ' . $this->dayNumber . ', 
                prompt.name_fr as ' . $this->promptNameFr . ', 
                prompt.name_en as ' . $this->promptNameEn . ', 
                promptList.year as ' . $this->promptListYear
            )
            ->leftJoin('post.prompt', 'prompt')
            ->leftJoin('post.user', 'user')
            ->innerJoin('prompt.promptList', 'promptList')
            ->where('prompt.name_fr LIKE :query')
            ->orWhere('prompt.name_en LIKE :query')
            ->orWhere('prompt.dayNumber LIKE :query')
            ->orWhere('promptList.year LIKE :query')
            ->andWhere('user.username = :thisOwner')
            ->andWhere('post.prompt IS NOT NULL')
            ->setParameter('query', '%' . $query . '%')
            ->setParameter('thisOwner', $owner->getUsername())
            ->orderBy($orderBy, $ascDesc)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findAllBy(string $where, string $value, string $orderBy): array
    {
        $now = new \DateTimeImmutable();

        return $this->createQueryBuilder('post')
            ->select(
                'post.id as ' . $this->id . ',
                post.fileName as ' . $this->fileName . ', 
                post.uploadedOn as ' . $this->date . ',
                user.username as ' . $this->owner . ', 
                prompt.dayNumber as ' . $this->dayNumber . ', 
                prompt.name_fr as ' . $this->promptNameFr . ', 
                prompt.name_en as ' . $this->promptNameEn . ', 
                promptList.year as ' . $this->promptListYear
            )
            ->leftJoin('post.prompt', 'prompt')
            ->leftJoin('prompt.promptList', 'promptList')
            ->leftJoin('post.user', 'user')
            ->where($where . ' = :val')
            ->andWhere('post.prompt IS NOT NULL')
            ->setParameter('val', $value)
            ->orderBy($orderBy)
            ->getQuery()
            ->getResult();
    }

    public function findAllByUserAndYear($userUsername, $year, $orderBy)
    {
        return $this->createQueryBuilder('post')
            ->select(
                'post.id as ' . $this->id . ',
                post.fileName as ' . $this->fileName . ', 
                post.uploadedOn as ' . $this->date . ',
                user.username as ' . $this->owner . ', 
                prompt.dayNumber as ' . $this->dayNumber . ', 
                prompt.name_fr as ' . $this->promptNameFr . ', 
                prompt.name_en as ' . $this->promptNameEn . ', 
                promptList.year as ' . $this->promptListYear
            )
            ->leftJoin('post.prompt', 'prompt')
            ->leftJoin('prompt.promptList', 'promptList')
            ->leftJoin('post.user', 'user')
            ->where('user.username = :val')
            ->andWhere('post.prompt IS NOT NULL')
            ->andWhere('promptList.year = :val2')
            ->setParameter('val', $userUsername)
            ->setParameter('val2', $year)
            ->orderBy($orderBy)
            ->getQuery()
            ->getResult();
    }

    public function findOneById($id): ArrayCollection
    {
        $result = $this->createQueryBuilder('post')
            ->select(
                'post.id as ' . $this->id . ',
                post.fileName as ' . $this->fileName . ', 
                post.uploadedOn as ' . $this->date . ',
                user.username as ' . $this->owner . ', 
                prompt.dayNumber as ' . $this->dayNumber . ', 
                prompt.name_fr as ' . $this->promptNameFr . ', 
                prompt.name_en as ' . $this->promptNameEn . ', 
                promptList.year as ' . $this->promptListYear
            )
            ->leftJoin('post.prompt', 'prompt')
            ->leftJoin('prompt.promptList', 'promptList')
            ->leftJoin('post.user', 'user')
            ->where('post.id = :val')
            ->andWhere('post.prompt IS NOT NULL')
            ->setParameter('val', $id)
            ->getQuery()
            ->getResult();

        return new ArrayCollection($result[0]);
    }

    public function findLatestPostOfUser(User $user): ArrayCollection
    {
        $result = $this->createQueryBuilder('post')
            ->select(
                'post.id as ' . $this->id . ',
                post.fileName as ' . $this->fileName . ', 
                post.uploadedOn as ' . $this->date . ',
                user.username as ' . $this->owner . ', 
                prompt.dayNumber as ' . $this->dayNumber . ', 
                prompt.name_fr as ' . $this->promptNameFr . ', 
                prompt.name_en as ' . $this->promptNameEn . ', 
                promptList.year as ' . $this->promptListYear
            )
            ->leftJoin('post.prompt', 'prompt')
            ->leftJoin('prompt.promptList', 'promptList')
            ->leftJoin('post.user', 'user')
            ->where('user.username = :val')
            ->andWhere('post.prompt IS NOT NULL')
            ->setParameter('val', $user->getUsername())
            ->orderBy('post.uploadedOn', 'DESC')
            ->getQuery()
            ->getResult();

        return new ArrayCollection($result[0]);
    }
}
