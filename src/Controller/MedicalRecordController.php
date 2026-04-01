<?php

namespace App\Controller;

use App\Entity\MedicalRecord;
use App\Repository\MedicalRecordRepository;
use App\Service\AccessLogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/medical-records')]
#[IsGranted('ROLE_USER')]
class MedicalRecordController extends AbstractController
{
    public function __construct(
        private readonly MedicalRecordRepository $recordRepo,
        private readonly AccessLogService $logService
    ) {}

    #[Route('/', name: 'medical_record_index', methods: ['GET'])]
    public function index(): Response
    {
        $records = $this->recordRepo->findAll();
        return $this->render('medical_record/index.html.twig', [
            'records' => $records
        ]);
    }

    #[Route('/{id}', name: 'medical_record_show', methods: ['GET'])]
    public function show(MedicalRecord $record): Response
    {
        $this->denyAccessUnlessGranted('MEDICAL_RECORD_READ', $record);

        return $this->render('medical_record/show.html.twig', [
            'record' => $record
        ]);
    }

    #[Route('/{id}/edit', name: 'medical_record_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MedicalRecord $record, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('MEDICAL_RECORD_EDIT', $record);

        if ($request->isMethod('POST')) {
            $record->setDiagnosis($request->request->get('diagnosis'));
            $record->setTreatment($request->request->get('treatment'));
            $em->flush();

            $this->logService->log(
                user: $this->getUser(),
                action: 'EDIT',
                resource: 'medical_record',
                resourceId: $record->getId(),
                details: 'Registro actualizado'
            );

            $this->addFlash('success', 'Registro actualizado correctamente.');
            return $this->redirectToRoute('medical_record_show', ['id' => $record->getId()]);
        }

        return $this->render('medical_record/edit.html.twig', [
            'record' => $record
        ]);
    }

    #[Route('/{id}/delete', name: 'medical_record_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, MedicalRecord $record, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('MEDICAL_RECORD_DELETE', $record);

        if ($this->isCsrfTokenValid('delete'.$record->getId(), $request->request->get('_token'))) {
            $this->logService->log(
                user: $this->getUser(),
                action: 'DELETE',
                resource: 'medical_record',
                resourceId: $record->getId(),
                details: 'Registro eliminado'
            );
            $em->remove($record);
            $em->flush();
        }

        return $this->redirectToRoute('medical_record_index');
    }
}
