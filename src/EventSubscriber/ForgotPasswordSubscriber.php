<?php
namespace App\EventSubscriber;

use App\Exception\CustomException;
use CoopTilleuls\ForgotPasswordBundle\Event\CreateTokenEvent;
use CoopTilleuls\ForgotPasswordBundle\Event\UpdatePasswordEvent;
use CoopTilleuls\ForgotPasswordBundle\Event\UserNotFoundEvent;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\User\UserInterface;

final class ForgotPasswordSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Security $security,
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Symfony 4.3 and inferior, use 'kernel.request' event name
            KernelEvents::REQUEST => 'onKernelRequest',
            CreateTokenEvent::class => 'onCreateToken',
            UpdatePasswordEvent::class => 'onUpdatePassword'
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest() || !str_starts_with($event->getRequest()->get('_route'), 'coop_tilleuls_forgot_password')) {
            return;
        }

        // User should not be authenticated on forgot password
        $token = $this->security->getToken();
        if (null !== $token && $token->getUser() instanceof UserInterface) {
            throw new AccessDeniedHttpException;
        }
    }

    public function onCreateToken(CreateTokenEvent $event): void
    {
        $passwordToken = $event->getPasswordToken();
        $user = $passwordToken->getUser();

        $message = (new Email())
            ->from('no-reply@example.com')
            ->to($user->getEmail())
            ->subject('Reset your password')
            ->html($this->twig->render(
                'App:ResetPassword:mail.html.twig',
                [
                    'reset_password_url' => sprintf('https://www.example.com/forgot-password/%s', $passwordToken->getToken()),
                ]
            ));
        $this->mailer->send($message);

        self::generalizedResponse();
    }

    public function onUpdatePassword(UpdatePasswordEvent $event): void
    {
        $passwordToken = $event->getPasswordToken();
        $user = $passwordToken->getUser();
        $user->setPlainPassword($event->getPassword());
        $this->userManager->updateUser($user);
    }

    public function onUserNotFound(UserNotFoundEvent $event): void
    {
        self::generalizedResponse();
    }

    /**
     * Return a generalized response whatever the case.
     */
    private function generalizedResponse(): void
    {
        throw new CustomException('Check your emails.', 200);
    }
}
