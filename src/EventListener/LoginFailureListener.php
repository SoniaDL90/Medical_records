<?php

namespace App\EventListener;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\AccessLogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

#[AsEventListener(event: LoginFailureEvent::class)]
class LoginFailureListener
{
    public function __construct(
        private readonly AccessLogService $logService,
        private readonly UserRepository $userRepo,
        private readonly EntityManagerInterface $em
    ) {}

    public function __invoke(LoginFailureEvent $event): void
    {
        $request = $event->getRequest();
        $email = $request->request->get('_username') ?? $request->get('_username');

        $user = $email ? $this->userRepo->findOneBy(['email' => $email]) : null;

        // Registrar el intento fallido
        $this->logService->log(
            user: $user,
            action: 'LOGIN_FAILED',
            resource: 'auth',
            success: false,
            details: 'Intento de login fallido para: ' . $email
        );

        // Bloquear cuenta tras 5 intentos fallidos
        if ($user) {
            $recentFailed = $this->logService->getRecentFailedAccess(1);
            $userFailed = array_filter($recentFailed, fn($log) => $log->getUser()?->getId() === $user->getId());

            if (count($userFailed) >= 5) {
                $user->setIsActive(false);
                $this->em->flush();
            }
        }
    }
}
