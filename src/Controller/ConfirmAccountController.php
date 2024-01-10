<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Spatie\UrlSigner\Sha256UrlSigner;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ConfirmAccountController extends AbstractController
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    #[Route('/confirm-email/{uuid}', name: 'app_confirm_email', defaults: ['_signed' => true], methods: ['GET'])]
    public function confirmEmail(User $user, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $urlSigner = new Sha256UrlSigner('A15EZEQS257854EZASDZZNJK');
        $absoluteUrl = $request->getUri();

        if(!$urlSigner->validate($absoluteUrl)) {
            throw new \HttpException();
        }

        if ($this->security->getUser()) {
            $this->security->logout(false);
        }

        if ($user->isVerifiedEmail()) {
            throw new \HttpException();
        }

        $user->setVerifiedEmail(true);
        $em->flush();

        $data = [
            'status' => 200,
            'message' => 'Email verified.',
        ];

        return new JsonResponse($data);
    }
}
