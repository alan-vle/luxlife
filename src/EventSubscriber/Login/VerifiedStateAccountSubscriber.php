<?php

namespace App\EventSubscriber\Login;

use App\Entity\User\User;
use App\Service\User\TokenValidator\UserEmailTokenValidator;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class VerifiedStateAccountSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly UserEmailTokenValidator $emailTokenValidator
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

        // Check if account email and phone number(not used) are verified
        if ($user->isVerifiedEmail() || $user->isVerifiedPhoneNumber()) {
            return;
        }

        $event->stopPropagation();
        $this->security->logout(false);

        // Check if a user email token has been generated
        $this->emailTokenValidator->isAlreadyGenerated($user);

        $event->setResponse(new JsonResponse($this->normalizeBadAccountStateData($user), 400));
    }

    /**
     * Normalize data depending on state of account.
     *
     * @return array<string, string>
     */
    private function normalizeBadAccountStateData(User $user): array
    {
        return [
            'status' => 400,
            'message' => match (false) {
                $user->isActive() => 'Your account is deactivate.',
                $user->isVerifiedEmail() => 'Your email is not verified.',
                $user->isVerifiedPhoneNumber() => 'Your phone number is not verified.',
                default => 'Something is wrong, try again later.'
            },
        ];
    }
}
