<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Car\Car;
use App\Entity\Rental\Rental;
use App\Entity\User\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(
        private Security $security,
    ) {
    }

    /**
     * @param array<string> $context
     */
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    /**
     * @param array<string> $identifiers
     * @param array<string> $context
     */
    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        $user = $this->security->getUser();

        $rootAlias = $queryBuilder->getRootAliases()[0];

        if (Car::class === $resourceClass && (null === $user || $this->security->isGranted('ROLE_CUSTOMER'))) {
            $queryBuilder->andWhere(sprintf('%s.status != :problem_status', $rootAlias));
            $queryBuilder->setParameter('problem_status', 3);
        } elseif (Rental::class === $resourceClass && $user instanceof User) {
            $queryBuilder->andWhere(sprintf('%s.employee = :current_user or %s.customer = :current_user', $rootAlias, $rootAlias));
            $queryBuilder->setParameter('current_user', $user->getId());
        }
    }
}
