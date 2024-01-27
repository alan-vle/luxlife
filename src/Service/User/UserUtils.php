<?php

namespace App\Service\User;

use App\Entity\User\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class UserUtils
{
    public function __construct(
        private readonly Security $security
    ) {
    }

    /**
     * Check roles submitted by the user (only admin or director but for security).
     */
    public function defineRoleAccordingToCase(User $user): void
    {
        // User not logged === new customer
        if (!$this->security->getUser()) {
            $user->setRoles(['customer']);
        } elseif ($this->security->isGranted('ROLE_ADMIN')) { // User logged as admin, return; bc he can do anything
            return;
        } elseif ($this->security->isGranted('ROLE_DIRECTOR')) { // User logged as director, set roles agent
            $user->setRoles(['agent']);
        } else {
            throw new BadRequestException();
        }
    }

    public function updateRoleAccordingToCase(User $updatedUser): void
    {
        $loggedUser = $this->security->getUser();

        if (!$loggedUser instanceof User) {
            throw new BadRequestException();
        }

        // If logged user is admin, continue updating
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        if ($this->security->isGranted('ROLE_DIRECTOR')) {
            $loggedUserUuid = $loggedUser->getUuid();
            $updatedUserRoles = $updatedUser->getRoles();

            // Check if the logged director has tried to assign the administrator role,
            // or if he has tried to assign the director role (to someone other than himself).
            if (in_array('ROLE_ADMIN', $updatedUserRoles)
                || ($loggedUserUuid !== $updatedUser && in_array('ROLE_DIRECTOR', $updatedUserRoles))
            ) {
                throw new BadRequestException();
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

        if (!$loggedUser instanceof User) {
            throw new BadRequestException();
        }

        $agencyOfDirector = $user->getAgency()->getDirector();

        // If agency is not null, and logged user is admin
        // or logged user is director and user's agency director is himself, continue processing
        if ($this->security->isGranted('ROLE_ADMIN')
            || (
                $this->security->isGranted('ROLE_DIRECTOR') && $agencyOfDirector
                && $agencyOfDirector->getUuid() === $loggedUser->getUuid()
            )
        ) {
            return;
        } else {
            throw new BadRequestException();
        }
    }
}
