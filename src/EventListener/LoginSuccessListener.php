<?php

namespace App\EventListener;

use App\Entity\User;
use App\Service\AccessLogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

#[AsEventListener(event: LoginSuccessEvent::class)]
class LoginSuccessListener
{
    public function __construct(
        private readonly AccessLogService $logService,
        private readonly EntityManagerInterface $em
    ) {}

    public function __invoke(LoginSuccessEvent $event): void
    {
        $user = $event->getAuthenticatedToken()->getUser();

        if ($user instanceof User) {
            // Registrar login exitoso
            $this->logService->log(
                user: $user,
                action: 'LOGIN_SUCCESS',
                resource: 'auth',
                success: true,
                details: 'Login exitoso'
            );

            // Actualizar último login
            $user->setLastLogin(new \DateTimeImmutable());
            $this->em->flush();
        }
    }
}

