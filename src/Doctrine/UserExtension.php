<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class UserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        Operation $operation = null,
        array $context = []
    ): void {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (User::class !== $resourceClass || $this->security->isGranted(User::ROLE_SUPER_ADMIN) || null === $user = $this->security->getUser()) {
            return;
        }

        if(!$user->getCompany()){
            return;
        }
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $user = $this->security->getUser();

        $queryBuilder->andWhere(sprintf('%s.company = :company_id', $rootAlias))
            ->setParameter('company_id', $user->getCompany()->getId());
    }
}
