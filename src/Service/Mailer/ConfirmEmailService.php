<?php

namespace App\Service\Mailer;

use App\Entity\User\EmailVerifierToken;
use App\Entity\User\User;
use App\Service\SignedUrl\UrlSignedCreator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class ConfirmEmailService
{
    private static MailerInterface $mailer;
    private static string $reactAppUrl;

    public function __construct(
        MailerInterface $mailer,
        private readonly EntityManagerInterface $em,
        #[Autowire('%react_app_url%')] string $reactAppUrl,
    ) {
        self::$mailer = $mailer;
        self::$reactAppUrl = $reactAppUrl;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendConfirmationEmail(User $user): void
    {
        $emailVerifierToken = new EmailVerifierToken();
        $userEmail = $user->getEmail() ?: '';

        $emailVerifierToken
            ->setUser($user)
            ->setEmail($userEmail)
        ;

        $this->em->persist($emailVerifierToken);
        $this->em->flush();

        $url = self::$reactAppUrl.'/confirm-email/'.$emailVerifierToken->getUuid();

        // Create a signed url for allow access to confirm email url for the new user
        $signedUrl = UrlSignedCreator::getSignedUrlBySpatieBundle($url, 'P1D');
        // Send an email for new user
        $email = (new NotificationEmail())
            ->to(new Address($userEmail))
            ->subject('Thanks for signing up!')
            ->htmlTemplate('emails/users/confirm-email.html.twig')
            ->context([
                'expiration_date' => (new \DateTime('now'))->add(new \DateInterval('P1D')),
                'name_new_user' => $user->getFirstName().' '.$user->getLastName(),
                'url' => $signedUrl,
            ])
        ;

        // Set transport conf for this mail
        $email
            ->getHeaders()
            ->addTextHeader('X-Transport', 'no_reply')
        ;

        self::$mailer->send($email);
    }
}
