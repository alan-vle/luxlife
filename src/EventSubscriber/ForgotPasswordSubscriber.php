<?php

namespace App\EventSubscriber;

use App\Exception\CustomException;
use App\Service\Mailer\MailerService;
use CoopTilleuls\ForgotPasswordBundle\Event\CreateTokenEvent;
use CoopTilleuls\ForgotPasswordBundle\Event\UpdatePasswordEvent;
use CoopTilleuls\ForgotPasswordBundle\Event\UserNotFoundEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class ForgotPasswordSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Security $security,
        #[Autowire('%react_app_url%')] private readonly string $reactAppUrl,
        private readonly MailerService $mailerService,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly EntityManagerInterface $em
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Symfony 4.3 and inferior, use 'kernel.request' event name
            KernelEvents::REQUEST => 'onKernelRequest',
            CreateTokenEvent::class => 'onCreateToken',
            UpdatePasswordEvent::class => 'onUpdatePassword',
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
            throw new AccessDeniedHttpException();
        }
    }

    public function onCreateToken(CreateTokenEvent $event): void
    {
        $passwordToken = $event->getPasswordToken();
        $user = $passwordToken->getUser();

        $emailParams['to'] = $user->getEmail();
        $emailParams['subject'] = 'Demande de réinitialisation du mot de passe';
        $emailParams['html_template'] = 'emails/users/reset-password.html.twig';
        $emailParams['context'] = [
            'name_user' => $user->getFullName(),
            'reset_password_url' => sprintf($this->reactAppUrl.'/reset-password/%s', $passwordToken->getToken()),
        ];

        $this->mailerService->sendEmail($emailParams);

        self::generalizedResponse();
    }

    public function onUpdatePassword(UpdatePasswordEvent $event): void
    {
        $passwordToken = $event->getPasswordToken();
        $user = $passwordToken->getUser();
        $newHashedPassword = $this->hasher->hashPassword($user, $event->getPassword());

        $user->setPassword($newHashedPassword);
        $this->em->flush();

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
