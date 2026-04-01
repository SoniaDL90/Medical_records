<?php

namespace App\Controller\Api;

use App\Entity\MedicalRecord;
use App\Repository\MedicalRecordRepository;
use App\Service\AccessLogService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/medical-records')]
class MedicalRecordApiController extends AbstractController
{
    public function __construct(
        private readonly MedicalRecordRepository $recordRepo,
        private readonly AccessLogService $logService
    ) {}

    #[Route('/', name: 'api_medical_records', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $records = $this->recordRepo->findAll();

        $data = array_map(fn(MedicalRecord $r) => [
            'id' => $r->getId(),
            'patient' => $r->getPatient()->getFirstName() . ' ' . $r->getPatient()->getLastName(),
            'doctor' => $r->getDoctor()->getName(),
            'diagnosis' => $r->getDiagnosis(),
            'treatment' => $r->getTreatment(),
            'createdAt' => $r->getCreatedAt()?->format('Y-m-d H:i:s'),
        ], $records);

        $this->logService->log(
            user: $this->getUser(),
            action: 'API_READ',
            resource: 'medical_record',
            success: true,
            details: 'Listado via API'
        );

        return $this->json($data);
    }

    #[Route('/{id}', name: 'api_medical_record_show', methods: ['GET'])]
    public function show(MedicalRecord $record): JsonResponse
    {
        $this->denyAccessUnlessGranted('MEDICAL_RECORD_READ', $record);

        return $this->json([
            'id' => $record->getId(),
            'patient' => $record->getPatient()->getFirstName() . ' ' . $record->getPatient()->getLastName(),
            'doctor' => $record->getDoctor()->getName(),
            'diagnosis' => $record->getDiagnosis(),
            'treatment' => $record->getTreatment(),
            'medications' => $record->getMedications(),
            'createdAt' => $record->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updatedAt' => $record->getUpdatedAt()?->format('Y-m-d H:i:s'),
        ]);
    }
}
