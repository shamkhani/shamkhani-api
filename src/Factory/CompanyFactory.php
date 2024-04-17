<?php

namespace App\Factory;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Company>
 *
 * @method        Company|Proxy                     create(array|callable $attributes = [])
 * @method static Company|Proxy                     createOne(array $attributes = [])
 * @method static Company|Proxy                     find(object|array|mixed $criteria)
 * @method static Company|Proxy                     findOrCreate(array $attributes)
 * @method static Company|Proxy                     first(string $sortedField = 'id')
 * @method static Company|Proxy                     last(string $sortedField = 'id')
 * @method static Company|Proxy                     random(array $attributes = [])
 * @method static Company|Proxy                     randomOrCreate(array $attributes = [])
 * @method static CompanyRepository|RepositoryProxy repository()
 * @method static Company[]|Proxy[]                 all()
 * @method static Company[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Company[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Company[]|Proxy[]                 findBy(array $attributes)
 * @method static Company[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Company[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class CompanyFactory extends ModelFactory
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
            'name' => self::faker()->text(100),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Company $company): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Company::class;
    }
}
