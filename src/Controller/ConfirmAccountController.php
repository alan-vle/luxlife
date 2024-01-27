<?php

namespace App\Controller;

use App\Entity\User\User;
use App\Entity\User\Verifier\EmailVerifierToken;
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
        // If user is logged so force logout
        if ($this->security->getUser()) {
            $this->security->logout(false);
        }

        $urlSigner = new Sha256UrlSigner('A15EZEQS257854EZASDZZNJK');
        $absoluteUrl = $request->getUri();
        $user = $emailVerifierToken->getUser();

        // Checks if url is invalid or user is not a user or user has already verified his email
        // or user's email is different from registered email in EmailVerifier
        if (!$urlSigner->validate($absoluteUrl) || !$user instanceof User
            || $user->isVerifiedEmail() || $user->getEmail() !== $emailVerifierToken->getEmail()
        ) {
            // Then remove the emailVerifierToken in db
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
