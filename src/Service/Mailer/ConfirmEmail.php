<?php

namespace App\Service\Mailer;

use App\Entity\User;
use App\Service\SignedUrl\UrlSignedCreator;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class ConfirmEmail
{
    private static MailerInterface $mailer;
    private static string $reactAppUrl;
    private static string $appEnv;

    public function __construct(
        MailerInterface $mailer,
        #[Autowire('%react_app_url%')] string $reactAppUrl,
        #[Autowire('%app_env%')] string $appEnv
    )
    {
        self::$mailer = $mailer;
        self::$reactAppUrl = $reactAppUrl;
        self::$appEnv = $appEnv;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public static function sendConfirmationEmail(User $user): void
    {

        $url = self::$reactAppUrl.'/confirm-email/'.$user->getUuid();

        // Create a signed url for new user access
        $signedUrl = UrlSignedCreator::getSignedUrlBySpatieBundle($url, 'PT1H');

        // Send an email for new user
        $email = (new NotificationEmail())
            ->to(new Address($user->getEmail()))
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
            ->addTextHeader('X-Transport', 'dev' !== self::$appEnv ? 'no_reply' : 'no_reply_dev')
        ;

        self::$mailer->send($email);
    }
}