<?php

namespace App\Serializer;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use App\Entity\Rental\Rental;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class RentalContextBuilder implements SerializerContextBuilderInterface
{
    private SerializerContextBuilderInterface $decorated;
    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(SerializerContextBuilderInterface $decorated, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->decorated = $decorated;
        $this->authorizationChecker = $authorizationChecker;
    }

    /* @phpstan-ignore-next-line */
    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $resourceClass = $context['resource_class'] ?? null;

        if (
            Rental::class === $resourceClass && isset($context['groups'])
            && false === $normalization && $this->authorizationChecker->isGranted('ROLE_AGENT')
        ) {
            $context['groups'][] = 'rental-agency:write';
        }

        return $context;
    }
}
