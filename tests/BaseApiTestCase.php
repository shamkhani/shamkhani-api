<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\ApiToken;
use App\Entity\Company;
use App\Entity\User;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Zenstruck\Browser\HttpOptions;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;


abstract class BaseApiTestCase extends ApiTestCase
{
//    use RefreshDatabaseTrait;
    use ResetDatabase;
    use Factories;

    use HasBrowser {
        browser as baseKernelBrowser;
    }
    protected function browser(array $options = [], array $server = [])
    {
        return $this->baseKernelBrowser($options, $server)
            ->setDefaultHttpOptions(
                HttpOptions::create()
                    ->withHeader('Accept', 'application/ld+json')
                    ->withHeader('Content-Type', 'application/ld+json')
            )
            ;
    }
}
