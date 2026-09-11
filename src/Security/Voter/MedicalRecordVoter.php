<?php
namespace App\Security\Voter;
use App\Entity\MedicalRecord;
use App\Entity\User;
use App\Service\AccessLogService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
class MedicalRecordVoter extends Voter
{
    const READ = 'MEDICAL_RECORD_READ';
    const EDIT = 'MEDICAL_RECORD_EDIT';
    const DELETE = 'MEDICAL_RECORD_DELETE';
    public function __construct(
        private readonly AccessLogService $logService
    ) {}
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::READ, self::EDIT, self::DELETE])
            && $subject instanceof MedicalRecord;
    }
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }
        /** @var MedicalRecord $record */
        $record = $subject;
        $granted = match($attribute) {
            self::READ => $this->canRead($user, $record),
            self::EDIT => $this->canEdit($user, $record),
            self::DELETE => $this->canDelete($user),
            default => false
        };
        // Punto 14 — Registrar en log todos los accesos incluyendo denegados
        $this->logService->log(
            user: $user,
            action: $granted ? $attribute : 'ACCESS_DENIED',
            resource: 'medical_record',
            resourceId: $record->getId(),
            success: $granted,
            details: $granted ? 'Acceso concedido' : "Acceso denegado a {$attribute} por rol insuficiente"
        );
        return $granted;
    }
private function canRead(User $user, MedicalRecord $record): bool
{
    // ADMIN lee todo
    if (in_array('ROLE_ADMIN', $user->getRoles())) return true;
    // DOCTOR lee sus propios pacientes
    if (in_array('ROLE_DOCTOR', $user->getRoles())) {
        return $record->getDoctor() === $user;
    }
    // NURSE lee todos los registros (sin detalles médicos sensibles - se controla en vista)
    if (in_array('ROLE_NURSE', $user->getRoles())) {
        return true;
    }
    // RECEPTIONIST ve datos básicos de todos
    if (in_array('ROLE_RECEPTIONIST', $user->getRoles())) {
        return true;
    }
    return false;
}
private function canEdit(User $user, MedicalRecord $record): bool
{
    // ADMIN edita todo
    if (in_array('ROLE_ADMIN', $user->getRoles())) return true;
    // DOCTOR edita sus propios pacientes
    if (in_array('ROLE_DOCTOR', $user->getRoles())) {
        return $record->getDoctor() === $user;
    }
    // NURSE puede editar todos (limitado - se controla en vista)
    if (in_array('ROLE_NURSE', $user->getRoles())) {
        return true;
    }
    // RECEPTIONIST no puede editar
    return false;
}

    private function canDelete(User $user): bool
    {
        // Solo ADMIN puede eliminar
        return in_array('ROLE_ADMIN', $user->getRoles());
    }
}
