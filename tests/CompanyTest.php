<?php

namespace App\Tests;

use App\Factory\ApiTokenFactory;

class CompanyTest extends BaseApiTestCase
{
    public function testGetCollection(): void
    {
        $userToken = ApiTokenFactory::new()->userRole()->create();
        $options = [
            'headers' => [
                "x-api-token" => $userToken->getToken()
            ]
        ];
        $this->browser()->get('api/companies', $options)->assertJson();

        $userToken = ApiTokenFactory::new()->companyAdminRole()->create();
        $options = [
            'headers' => [
                "x-api-token" => $userToken->getToken()
            ]
        ];
        $this->browser()->get('api/companies', $options)->assertJson();

        $userToken = ApiTokenFactory::new()->superAdminRole()->create();
        $options = [
            'headers' => [
                "x-api-token" => $userToken->getToken()
            ]
        ];
        $this->browser()->get('api/companies', $options)->assertJson();
    }

    public function testGetCompany(): void
    {
        $userToken = ApiTokenFactory::new()->companyAdminRole()->create();
        $options = [
            'headers' => [
                "x-api-token" => $userToken->getToken()
            ]
        ];
        $this->browser()->get('api/companies', $options)->assertJson();

        $userToken = ApiTokenFactory::new()->superAdminRole()->create();
        $options = [
            'headers' => [
                "x-api-token" => $userToken->getToken()
            ]
        ];
        $this->browser()->get('api/companies', $options)->assertJson();

        $userToken = ApiTokenFactory::new()->userRole()->create();
        $options = [
            'headers' => [
                "x-api-token" => $userToken->getToken()
            ]
        ];
        $this->browser()->get('api/companies', $options)->assertSuccessful();
    }


    public function testCreateCompanyOnlySuperAdminRole(): void
    {
        $userToken = ApiTokenFactory::new()->superAdminRole()->create();

        $options = [
            'headers' => [
                "x-api-token" => $userToken->getToken()
            ],
            'json' => [
                'name' => "X_COMPANY"
            ],
        ];

        $this->browser()->post('api/companies', $options)->assertSuccessful();
    }

    public function testCreateCompanyNotAllowedOtherRole(): void
    {
        $userToken = ApiTokenFactory::new()->userRole()->create();

        $options = [
            'headers' => [
                "x-api-token" => $userToken->getToken()
            ],
            'json' => [
                'name' => "X_COMPANY"
            ],
        ];

        $this->browser()->post('api/companies', $options)
            ->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN);

        $userToken = ApiTokenFactory::new()->companyAdminRole()->create();

        $options = [
            'headers' => [
                "x-api-token" => $userToken->getToken()
            ],
            'json' => [
                'name' => "X_COMPANY"
            ],
        ];

        $this->browser()->post('api/companies', $options)
            ->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN);
    }
}
