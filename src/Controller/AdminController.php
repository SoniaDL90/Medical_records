<?php
namespace App\Controller;
use App\Repository\AccessLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_index')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }
    #[Route('/admin/logs', name: 'admin_logs')]
    public function logs(Request $request, AccessLogRepository $repo): Response
    {
        $action   = $request->query->get('action', '');
        $success  = $request->query->get('success', '');
        $user     = $request->query->get('user', '');
        $dateFrom = $request->query->get('date_from', '');
        $dateTo   = $request->query->get('date_to', '');
        $logs = $repo->findWithFilters($action, $success, $user, $dateFrom, $dateTo);
        $failedLogins = $repo->countFailedLoginsLast24h();
        $suspicious = $repo->findSuspiciousLast24h();
        return $this->render('admin/logs.html.twig', [
            'logs'         => $logs,
            'failedLogins' => $failedLogins,
            'suspicious'   => $suspicious,
        ]);
    }
}
