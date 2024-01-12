<?php

namespace App\Service\Mailer;

use App\Entity\User\User;
use App\Entity\User\Verifier\EmailVerifierToken;
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
    private static EntityManagerInterface $em;
    private static string $reactAppUrl;

    public function __construct(
        EntityManagerInterface $em,
        MailerInterface $mailer,
        #[Autowire('%react_app_url%')] string $reactAppUrl,
    ) {
        self::$em = $em;
        self::$mailer = $mailer;
        self::$reactAppUrl = $reactAppUrl;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public static function sendConfirmationEmail(User $user): void
    {
        $userEmail = $user->getEmail() ?: '';
        $emailVerifierToken = new EmailVerifierToken();

        $expirationDate = (new \DateTime('now'))->add(new \DateInterval('P1D'));
        // Set new email verifier token with user and email
        $emailVerifierToken
            ->setUser($user)
            ->setEmail($userEmail)
            ->setExpiresAt($expirationDate->format('Y-m-d H:i'))
        ;

        self::$em->persist($emailVerifierToken);
        self::$em->flush();

        $url = self::$reactAppUrl.'/confirm-email/'.$emailVerifierToken->getUuid();

        // Create a signed url for allow access to confirm email controller
        $signedUrl = UrlSignedCreator::getSignedUrlBySpatieBundle($url, $expirationDate);

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
