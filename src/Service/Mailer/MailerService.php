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

class MailerService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly EntityManagerInterface $em,
        #[Autowire('%react_app_url%')] private readonly string $reactAppUrl,
        #[Autowire('%api_url%')] private readonly string $apiUrl,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendConfirmationEmail(User $user): void
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

        $this->em->persist($emailVerifierToken);
        $this->em->flush();

        $url = $this->apiUrl.'/confirm-email/'.$emailVerifierToken->getUuid();

        // Create a signed url for allow access to confirm email controller
        $signedUrl = UrlSignedCreator::getSignedUrlBySpatieBundle($url, $expirationDate, $emailVerifierToken->getUuid());

        // Replace prefix of signed url for react
        $reactUrl = str_replace($this->apiUrl, $this->reactAppUrl, $signedUrl);

        $emailParams['to'] = $userEmail;
        $emailParams['subject'] = 'Thanks for signing up!';
        $emailParams['html_template'] = 'emails/users/confirm-email.html.twig';
        $emailParams['context'] = [
            'expiration_date' => $expirationDate,
            'name_new_user' => $user->getFullName(),
            'url' => $reactUrl,
        ];

        $this->sendEmail($emailParams);
    }

    /**
     * @param array<string, array<string, \DateTime|string|null>|string> $emailParams
     */
    public function sendEmail(array $emailParams): void
    {
        // Send an email for new user
        $email = (new NotificationEmail())
            ->to(new Address($emailParams['to'])) /* @phpstan-ignore-line */
            ->subject($emailParams['subject']) /* @phpstan-ignore-line */
            ->htmlTemplate($emailParams['html_template']) /* @phpstan-ignore-line */
            ->context($emailParams['context']) /* @phpstan-ignore-line */
        ;

        // Set transport conf for this mail
        $email
            ->getHeaders()
            ->addTextHeader('X-Transport', 'no_reply')
        ;

        $this->mailer->send($email);
    }
}
