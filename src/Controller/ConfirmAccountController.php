<?php

namespace App\Controller;

use App\Entity\User\EmailVerifierToken;
use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Spatie\UrlSigner\Sha256UrlSigner;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;

class ConfirmAccountController extends AbstractController
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    #[Route('/confirm-email/{uuid}', name: 'app_confirm_email', defaults: ['_signed' => true], methods: ['GET'])]
    public function confirmEmail(EmailVerifierToken $emailVerifierToken, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if ($this->security->getUser()) {
            $this->security->logout(false);
        }

        $urlSigner = new Sha256UrlSigner('A15EZEQS257854EZASDZZNJK');
        $absoluteUrl = $request->getUri();
        $user = $emailVerifierToken->getUser();

        if (!$urlSigner->validate($absoluteUrl) || !$user instanceof User
            || $user->isVerifiedEmail() || $user->getEmail() !== $emailVerifierToken->getEmail()
        ) {
            $em->remove($emailVerifierToken);
            $em->flush();

            throw new HttpException(Response::HTTP_BAD_REQUEST);
        } else {
            $user->setVerifiedEmail(true);

            $em->remove($emailVerifierToken);
            $em->flush();

            $data = [
                'status' => 200,
                'message' => 'Email verified.',
            ];

            return new JsonResponse($data);
        }
    }
}
