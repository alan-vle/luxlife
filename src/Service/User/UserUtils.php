<?php

namespace App\Service\User;

use App\Entity\User\User;
use App\Service\HttpUtils;
use Symfony\Bundle\SecurityBundle\Security;

class UserUtils
{
    public function __construct(
        private readonly Security $security
    ) {
    }

    /**
     * Check roles submitted by the user.
     */
    public function defineRoleAccordingToCase(User $user): void
    {
        if (!$this->security->getUser()) {
            $user->setRoles(['customer']);
        } elseif ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        } elseif ($this->security->isGranted('ROLE_DIRECTOR')) {
            $user->setRoles(['agent']);
        } else {
            HttpUtils::throw400HTTPError();
        }
    }

    public function updateRoleAccordingToCase(User $updatedUser): void
    {
        $loggedUser = $this->security->getUser();

        if (!$loggedUser) {
            HttpUtils::throw400HTTPError();
        }

        // If logged user is admin, continue updating
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        if ($this->security->isGranted('ROLE_DIRECTOR')) {
            $loggedUserUuid = $loggedUser->getUuid(); /** @phpstan-ignore-line */
            $updatedUserRoles = $updatedUser->getRoles();

            // Check if the logged director has tried to assign the administrator role,
            // or if he has tried to assign the director role (to someone other than himself).
            if (in_array('ROLE_ADMIN', $updatedUserRoles)
                || ($loggedUserUuid !== $updatedUser && in_array('ROLE_DIRECTOR', $updatedUserRoles))
            ) {
                HttpUtils::throw400HTTPError();
            }
        }
    }

    /**
     * Check if the user is an admin or if he is director of this agency.
     */
    public function isAdminOrAgencyDirector(User $user): void
    {
        if (!$user->getAgency()) {
            return;
        }

        $loggedUser = $this->security->getUser();

        if (!$loggedUser) {
            HttpUtils::throw400HTTPError();
        }
        $agencyOfDirector = $user->getAgency()->getDirector();

        // If agency is not null, and logged user is admin
        // or logged user is director and user's agency director is himself, continue processing
        if ($this->security->isGranted('ROLE_ADMIN')
            || (
                $this->security->isGranted('ROLE_DIRECTOR') && $agencyOfDirector
                && $agencyOfDirector->getUuid() === $loggedUser->getUuid() /* @phpstan-ignore-line */
            )
        ) {
            return;
        } else {
            HttpUtils::throw400HTTPError();
        }
    }
}
