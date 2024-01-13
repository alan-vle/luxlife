<?php

namespace App\EventSubscriber\Login;

use App\Entity\User\User;
use App\Service\User\TokenValidator\UserEmailVerifierTokenValidator;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class VerifiedStateAccountSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly UserEmailVerifierTokenValidator $emailTokenValidator
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccessEvent',
        ];
    }

    public function onLoginSuccessEvent(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        if ($user->isVerifiedEmail() || $user->isVerifiedPhoneNumber()) {
            return;
        }

        $event->stopPropagation();
        $this->security->logout(false);

        // Check if a user email token has been generated
        $this->emailTokenValidator->isAlreadyGenerated($user);

        $event->setResponse(new JsonResponse($this->normalizeBadAccountStateData($user)));
    }

    /**
     * Normalize data depending on state of account.
     *
     * @return array<string, string>
     */
    private function normalizeBadAccountStateData(User $user): array
    {
        return [
            'status' => 'error',
            'message' => match (false) {
                $user->isActive() => 'Your account is deactivate.',
                $user->isVerifiedEmail() => 'Your email is not verified.',
                $user->isVerifiedPhoneNumber() => 'Your phone number is not verified.',
                default => 'Something is wrong, try again later.'
            },
        ];
    }
}
