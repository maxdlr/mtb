<?php

namespace App\Repository;

use App\Entity\Report;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Report>
 *
 * @method Report|null find($id, $lockMode = null, $lockVersion = null)
 * @method Report|null findOneBy(array $criteria, array $orderBy = null)
 * @method Report[]    findAll()
 * @method Report[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }

    public function findOneByPostId(int $postId): ?array
    {
        $results = $this->createQueryBuilder('report')
            ->select(
                'report.id as id,
                report.reportedOn as reportedOn,
                report.isResolved as isResolved,
                post.id as postId,
                post.fileName as postFileName,
                reporter.username as reporterUsername,
                violation.name as violationName,
                violation.description as violationDescription,
                resolution.name as resolutionName,
                resolution.description as resolutionDescription'
            )
            ->join('report.reporter', 'reporter')
            ->join('report.violation', 'violation')
            ->join('report.resolution', 'resolution')
            ->join('report.post', 'post')
            ->andWhere('post.id = :val')
            ->setParameter('val', $postId)
            ->getQuery()
            ->getResult();

        $collectedResults = [];
        foreach ($results as $result) {
            $collectedResults[] = new ArrayCollection($result);
        }

        return $collectedResults;
    }
}
