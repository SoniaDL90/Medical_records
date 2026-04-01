<?php

namespace App\Controller;

use App\Repository\AccessLogRepository;
use App\Service\AccessLogService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    public function __construct(
        private readonly AccessLogRepository $logRepo,
        private readonly AccessLogService $logService
    ) {}

    #[Route('/', name: 'admin_index')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/logs', name: 'admin_logs')]
    public function logs(Request $request): Response
    {
        $action = $request->query->get('action');
        $success = $request->query->get('success');

        $criteria = [];
        if ($action) $criteria['action'] = $action;
        if ($success !== null && $success !== '') {
            $criteria['success'] = (bool) $success;
        }

        $logs = $this->logRepo->findBy($criteria, ['createdAt' => 'DESC'], 100);
        $failedLogs = $this->logService->getRecentFailedAccess(24);

        return $this->render('admin/logs.html.twig', [
            'logs' => $logs,
            'failedLogs' => $failedLogs,
            'selectedAction' => $action,
            'selectedSuccess' => $success,
        ]);
    }
}
