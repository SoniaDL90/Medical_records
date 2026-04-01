<?php

namespace App\Service;

use App\Entity\AccessLog;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class AccessLogService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly RequestStack $requestStack
    ) {}

    public function log(
        ?User $user,
        string $action,
        string $resource,
        ?int $resourceId = null,
        bool $success = true,
        ?string $details = null
    ): void {
        $request = $this->requestStack->getCurrentRequest();

        $log = new AccessLog();
        $log->setUser($user);
        $log->setAction($action);
        $log->setResource($resource);
        $log->setResourceId($resourceId);
        $log->setIpAddress($request?->getClientIp() ?? 'unknown');
        $log->setSuccess($success);
        $log->setDetails($details);
        $log->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($log);
        $this->em->flush();
    }

    public function logLogin(User $user, bool $success): void
    {
        $this->log(
            user: $user,
            action: $success ? 'LOGIN_SUCCESS' : 'LOGIN_FAILED',
            resource: 'auth',
            success: $success
        );
    }

    public function getLogsByUser(User $user, int $limit = 50): array
    {
        return $this->em->getRepository(AccessLog::class)
            ->findBy(['user' => $user], ['createdAt' => 'DESC'], $limit);
    }

    public function getRecentFailedAccess(int $hours = 24): array
    {
        $since = new \DateTimeImmutable("-{$hours} hours");
        return $this->em->getRepository(AccessLog::class)
            ->createQueryBuilder('l')
            ->where('l.success = false')
            ->andWhere('l.createdAt >= :since')
            ->setParameter('since', $since)
            ->orderBy('l.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
