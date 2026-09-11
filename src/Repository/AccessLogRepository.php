<?php
namespace App\Repository;
use App\Entity\AccessLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
class AccessLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessLog::class);
    }
    public function findWithFilters(?string $action, ?string $success, ?string $user, ?string $dateFrom, ?string $dateTo): array
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.User', 'u')
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults(100);
        if ($action) {
            $qb->andWhere('a.action = :action')
               ->setParameter('action', $action);
        }
        if ($success !== '' && $success !== null) {
            $qb->andWhere('a.success = :success')
               ->setParameter('success', (bool)$success);
        }
        if ($user) {
            $qb->andWhere('u.email LIKE :user')
               ->setParameter('user', '%' . $user . '%');
        }
        if ($dateFrom) {
            $qb->andWhere('a.createdAt >= :dateFrom')
               ->setParameter('dateFrom', new \DateTime($dateFrom . ' 00:00:00'));
        }
        if ($dateTo) {
            $qb->andWhere('a.createdAt <= :dateTo')
               ->setParameter('dateTo', new \DateTime($dateTo . ' 23:59:59'));
        }
        return $qb->getQuery()->getResult();
    }
    public function countFailedLoginsLast24h(): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.action = :action')
            ->andWhere('a.createdAt >= :since')
            ->setParameter('action', 'LOGIN_FAILED')
            ->setParameter('since', new \DateTime('-24 hours'))
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findSuspiciousLast24h(): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.User', 'u')
            ->where('a.success = false')
            ->andWhere('a.createdAt >= :since')
            ->setParameter('since', new \DateTime('-24 hours'))
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
