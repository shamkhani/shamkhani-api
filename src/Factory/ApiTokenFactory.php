<?php

namespace App\Factory;

use App\Entity\ApiToken;
use App\Entity\User;
use App\Repository\ApiTokenRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<ApiToken>
 *
 * @method        ApiToken|Proxy                     create(array|callable $attributes = [])
 * @method static ApiToken|Proxy                     createOne(array $attributes = [])
 * @method static ApiToken|Proxy                     find(object|array|mixed $criteria)
 * @method static ApiToken|Proxy                     findOrCreate(array $attributes)
 * @method static ApiToken|Proxy                     first(string $sortedField = 'id')
 * @method static ApiToken|Proxy                     last(string $sortedField = 'id')
 * @method static ApiToken|Proxy                     random(array $attributes = [])
 * @method static ApiToken|Proxy                     randomOrCreate(array $attributes = [])
 * @method static ApiTokenRepository|RepositoryProxy repository()
 * @method static ApiToken[]|Proxy[]                 all()
 * @method static ApiToken[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static ApiToken[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static ApiToken[]|Proxy[]                 findBy(array $attributes)
 * @method static ApiToken[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static ApiToken[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static ApiToken[]|Proxy[]                 companyAdminRole()
 * @method static ApiToken[]|Proxy[]                 superAdminRole()
 * @method static ApiToken[]|Proxy[]                 userRole()
 */
final class ApiTokenFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'token' =>  self::faker()->unique()->password()
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(ApiToken $apiToken): void {})
        ;
    }

    protected static function getClass(): string
    {
        return ApiToken::class;
    }

    public function companyAdminRole(): self
    {
        return $this->addState(['user' => UserFactory::new(["roles"=>[User::ROLE_COMPANY_ADMIN]])]);
    }
    public function superAdminRole(): self
    {
        return $this->addState(['user' => UserFactory::new(["roles"=>[User::ROLE_SUPER_ADMIN]])]);
    }
    public function userRole(): self
    {
        return $this->addState(['user' => UserFactory::new(["roles"=>[User::ROLE_USER]])]);
    }
}
