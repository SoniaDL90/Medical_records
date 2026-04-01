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

        $this->logService->log(
            user: $user,
            action: $attribute,
            resource: 'medical_record',
            resourceId: $record->getId(),
            success: $granted
        );

        return $granted;
    }

    private function canRead(User $user, MedicalRecord $record): bool
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) return true;
        if (in_array('ROLE_DOCTOR', $user->getRoles())) {
            return $record->getDoctor() === $user;
        }
        return false;
    }

    private function canEdit(User $user, MedicalRecord $record): bool
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) return true;
        return in_array('ROLE_DOCTOR', $user->getRoles())
            && $record->getDoctor() === $user;
    }

    private function canDelete(User $user): bool
    {
        return in_array('ROLE_ADMIN', $user->getRoles());
    }
}
