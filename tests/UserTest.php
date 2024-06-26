<?php

namespace App\Tests;

use App\Entity\User;
use App\Factory\ApiTokenFactory;
use App\Factory\CompanyFactory;
use App\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;

class UserTest extends BaseApiTestCase
{

    /**
     * Test get user collection by super admin role - Get All Users
     *
     * @return void
     */
    public function testGetCollectionBySuperAdminRole(): void
    {
        UserFactory::new()->withCompany()->many(10)->create();
        $userToken = ApiTokenFactory::new()->superAdminRole()->create();
        $header = [
            'headers' =>
                [
                    "x-api-token" => $userToken->getToken()
                ]
        ];
        $this->browser()->get('api/users', $header)->assertJsonMatches('"hydra:totalItems"', 11);
    }

    /**
     * Test get user collection by admin company role - Get all company users
     *
     * @return void
     */
    public function testGetCollectionByCompanyAdminRole(): void
    {
        $company = CompanyFactory::createOne();
        $company2 = CompanyFactory::createOne();
        $company3 = CompanyFactory::createOne();

        $userCompany = UserFactory::createOne(['company' => $company, 'roles' => [User::ROLE_COMPANY_ADMIN]]);
        UserFactory::createOne(['company' => $company, 'roles' => [User::ROLE_COMPANY_ADMIN]]);
        UserFactory::new()->many(2)->create(['company' => $company2]);
        UserFactory::new()->many(2)->create(['company' => $company3]);

        $userToken = ApiTokenFactory::createOne(['user' => $userCompany]);
        $header = [
            'headers' =>
                [
                    "x-api-token" => $userToken->getToken()
                ]
        ];
        $this->browser()->get('api/users', $header)->assertJsonMatches('"hydra:totalItems"', 2);
    }

    /**
     * Test get user collection by user role -Get all company users
     *
     * @return void
     */
    public function testGetCollectionByUserRole(): void
    {
        $company = CompanyFactory::createOne();
        $company2 = CompanyFactory::createOne();
        $company3 = CompanyFactory::createOne();

        $userCompany = UserFactory::createOne(['company' => $company, 'roles' => [User::ROLE_USER]]);
        UserFactory::createOne(['company' => $company, 'roles' => [User::ROLE_USER]]);
        UserFactory::new()->many(2)->create(['company' => $company2]);
        UserFactory::new()->many(2)->create(['company' => $company3]);

        $userToken = ApiTokenFactory::createOne(['user' => $userCompany]);
        $header = [
            'headers' =>
                [
                    "x-api-token" => $userToken->getToken()
                ]
        ];
        $this->browser()->get('api/users', $header)->assertJsonMatches('"hydra:totalItems"', 2);
    }

    /**
     * Test get a user by super admin role
     *
     * @return void
     */
    public function testGetItemBySuperAdminRole(): void
    {
        $user = UserFactory::new()->withCompany()->create();
        $userToken = ApiTokenFactory::new()->superAdminRole()->create();
        $header = [
            'headers' =>
                [
                    "x-api-token" => $userToken->getToken()
                ]
        ];
        $this->browser()->get('api/users/'.$user->getId(), $header)->assertJsonMatches('id', $user->getId());
    }

    /**
     * Test get a user by company role
     *
     * @return void
     */
    public function testGetItemByCompanyAdminRole(): void
    {
        $company = CompanyFactory::createOne();
        $company2 = CompanyFactory::createOne();
        $company3 = CompanyFactory::createOne();

        $userCompany1 = UserFactory::createOne(['company' => $company, 'roles' => [User::ROLE_COMPANY_ADMIN]]);
        $userCompany2 = UserFactory::createOne(['company' => $company, 'roles' => [User::ROLE_COMPANY_ADMIN]]);
        UserFactory::new()->many(2)->create(['company' => $company2]);
        UserFactory::new()->many(2)->create(['company' => $company3]);

        $userToken = ApiTokenFactory::createOne(['user' => $userCompany1]);
        $header = [
            'headers' =>
                [
                    "x-api-token" => $userToken->getToken()
                ]
        ];
        $this->browser()->get('api/users/'.$userCompany1->getId(), $header)
            ->assertJsonMatches('id', $userCompany1->getId());
        $this->browser()->get('api/users/'.$userCompany2->getId(), $header)
            ->assertJsonMatches('id', $userCompany2->getId());
    }

    /**
     * Test get a user by user role
     *
     * @return void
     */
    public function testGetItemByUserRole(): void
    {
        $company = CompanyFactory::createOne();
        $company2 = CompanyFactory::createOne();
        $company3 = CompanyFactory::createOne();

        $userCompany = UserFactory::createOne(['company' => $company, 'roles' => [User::ROLE_USER]]);
        UserFactory::createOne(['company' => $company, 'roles' => [User::ROLE_USER]]);
        UserFactory::new()->many(2)->create(['company' => $company2]);
        UserFactory::new()->many(2)->create(['company' => $company3]);

        $userToken = ApiTokenFactory::createOne(['user' => $userCompany]);
        $header = [
            'headers' =>
                [
                    "x-api-token" => $userToken->getToken()
                ]
        ];
        $this->browser()->get('api/users/'.$userCompany->getId(), $header)
            ->assertJsonMatches('id', $userCompany->getId());
    }

    /**
     * Test not allowed get other user by user role
     *
     * @return void
     */
    public function testGetItemNotAllowedGetOtherUserByUserRole(): void
    {
        $company = CompanyFactory::createOne();
        $company2 = CompanyFactory::createOne();
        $company3 = CompanyFactory::createOne();

        $userCompany = UserFactory::createOne(['company' => $company, 'roles' => [User::ROLE_USER]]);
        UserFactory::createOne(['company' => $company, 'roles' => [User::ROLE_USER]]);
        $userCompany2 = UserFactory::new()->create(['company' => $company2]);
        UserFactory::new()->many(2)->create(['company' => $company3]);

        $userToken = ApiTokenFactory::createOne(['user' => $userCompany]);
        $header = [
            'headers' =>
                [
                    "x-api-token" => $userToken->getToken()
                ]
        ];
        $this->browser()->get('api/users/'.$userCompany2->getId(), $header)->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * Create user Successfully
     *
     * @return void
     */
    public function testCreateUserSuccessfully(): void
    {
        $userToken = ApiTokenFactory::new()->superAdminRole()->create();
        $header = [
            'headers' => [
                "x-api-token" => $userToken->getToken(),
            ], 'json' => [
                'name' => "Abbas",
                'roles' => [User::ROLE_USER],
                'password' => "123456"
            ],
        ];
        $result = $this->browser()->post('api/users', $header)->assertSuccessful();
    }

    /**
     * Test Not Allowed Lower case in the start position
     *
     * @return void
     */
    public function testCreateUserNotAllowedLowerCaseInTheFirstPosition(): void
    {
        $userToken = ApiTokenFactory::new()->superAdminRole()->create();
        $options = [
            'headers' => [
                "x-api-token" => $userToken->getToken()
            ],
            'json' => [
                'name' => "abbaS",
                'roles' => [User::ROLE_USER],
                'password' => "123456"
            ],
        ];

        $this->browser()->post('api/users', $options)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Test create user now allowed all lower case
     *
     * @return void
     */
    public function testCreateUserNotAllowedLowerCase(): void
    {
        $userToken = ApiTokenFactory::new()->superAdminRole()->create();
        $header = [
            'headers' => [
                "x-api-token" => $userToken->getToken()
            ], 'json' => [
                'name' => "abbas",
                'roles' => [User::ROLE_USER],
                'password' => "123456"
            ],
        ];
        $this->browser()->post('api/users', $header)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Test Not allowed a name with less than 3 characters
     *
     * @return void
     * @throws
     */
    public function testCreateUserLessThanThreeCharactersNameNotAllowed(): void
    {
        $userToken = ApiTokenFactory::new()->superAdminRole()->create();
        $header = [
            'headers' => [
                "x-api-token" => $userToken->getToken()
            ], 'json' => [
                'name' => "Ab",
                'roles' => [User::ROLE_USER],
                'password' => "123456"
            ],
        ];
        $this->browser()->post('api/users', $header)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Test Not allowed a name with less than 3 characters
     *
     * @return void
     * @throws
     */
    public function testCreateUserMoreThanHundredCharactersNameNotAllowed(): void
    {
        $userToken = ApiTokenFactory::new()->superAdminRole()->create();
        $header = [
            'headers' => [
                "x-api-token" => $userToken->getToken()
            ], 'json' => [
                'name' => "Luctus natoque mauris dis placerat habitasse quam cursus id eu litora elementum arcu porta primis elit cubilia curae nostra mattis cras dapibus euismod felis platea est nec enim et auctor sodales sapien velit pretium pulvinar nisi suscipit letius penatibus tincidunt sollicitudin ridiculus proin nam sed pellentesque netus ultrices nulla libero porttitor tellus parturient hac ullamcorper ultricies convallis curabitur a commodo malesuada fames augue ut vulputate aenean imperdiet urna vehicula consectetur tortor hendrerit dictumst condimentum orci aptent vel sociosqu dignissim ipsum lobortis senectus non posuere facilisis nascetur eleifend quisque quis rutrum dolor praesent suspendisse at conubia torquent mollis magnis odio",
                'roles' => [User::ROLE_USER],
                'password' => "123456"
            ],
        ];
        $this->browser()->post('api/users', $header)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }


    /**
     * Check Unknown Role Not Allowed
     *
     * @return void
     */
    public function testCreateUserWithUnknownRoleNotAllowed(): void
    {
        $userToken = ApiTokenFactory::new()->superAdminRole()->create();
        $header = [
            'headers' => [
                "x-api-token" => $userToken->getToken()
            ], 'json' => [
                'name' => "Abbas",
                'roles' => ["UNKNOWN_ROLE"],
                'password' => "123456"
            ],
        ];
        $this->browser()->post('api/users', $header)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }


    /**
     * Test create user not allowed ROLE_USER
     *
     * @return void
     */
    public function testCreateUserNotAllowedUserRole(): void
    {
        $userToken = ApiTokenFactory::new()->userRole()->create();
        $header = [
            'headers' => [
                "x-api-token" => $userToken->getToken(),
            ], 'json' => [
                'name' => "Userroleuser",
                'roles' => [User::ROLE_USER],
                'password' => "123456"
            ],
        ];
        $this->browser()->post('api/users', $header)->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test create user not allowed ROLE_USER
     *
     * @return void
     */
    public function testCreateUserAllowedCompanyAdminRole(): void
    {
        $userToken = ApiTokenFactory::new()->companyAdminRole()->create();
        $header = [
            'headers' => [
                "x-api-token" => $userToken->getToken(),
            ], 'json' => [
                'name' => "Usercompanyadminrole",
                'roles' => [User::ROLE_USER],
                'password' => "123456"
            ],
        ];
        $this->browser()->post('api/users', $header)->assertSuccessful();
    }

    /**
     * Test delete user allowed only for ROLE_SUPER_ADMIN
     *
     * @return void
     */
    public function testDeleteUserAllowedSuperAdminRole(): void
    {
        $userToken = ApiTokenFactory::new()->superAdminRole()->create();
        $user = UserFactory::createOne();
        $header = [
            'headers' => [
                "x-api-token" => $userToken->getToken(),
            ]
        ];
        $this->browser()->delete('api/users/'.$user->getId(), $header)->assertSuccessful();
    }

    /**
     * Test delete user not allowed ROLE_USER
     *
     * @return void
     */
    public function testDeleteUserNotAllowedUserRole(): void
    {
        $company = CompanyFactory::createOne();
        $userCompany = UserFactory::createOne(['company' => $company, 'roles' => [User::ROLE_USER]]);
        $userToken = ApiTokenFactory::createOne(['user' => $userCompany]);
        $header = [
            'headers' => [
                "x-api-token" => $userToken->getToken(),
            ]
        ];
        $this->browser()->delete('api/users/'.$userCompany->getId(), $header)->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test delete user not allowed company ROLE_COMPANY_ADMIN
     *
     * @return void
     */
    public function testDeleteUserNotAllowedCompanyAdminRole(): void
    {
        $company = CompanyFactory::createOne();
        $userCompany = UserFactory::createOne(['company' => $company, 'roles' => [User::ROLE_COMPANY_ADMIN]]);
        $userCompany2 = UserFactory::createOne(['company' => $company, 'roles' => [User::ROLE_USER]]);
        $userToken = ApiTokenFactory::createOne(['user' => $userCompany]);

        $header = [
            'headers' => [
                "x-api-token" => $userToken->getToken(),
            ],
        ];
        $this->browser()->delete('api/users/'.$userCompany2->getId(), $header)->assertStatus(Response::HTTP_FORBIDDEN);
    }


    /**
     * Test update role not allowed by ROLE_USER
     *
     * @return void
     */
    public function testUpdateUserRoleNotAllowedByUser(): void
    {
        $userToken = ApiTokenFactory::new()->userRole()->create();
        $header = [
            'headers' => [
                "x-api-token" => $userToken->getToken(),
                "Content-Type" => "application/merge-patch+json"
            ], 'json' => [
                'name' => "Updateusernotallowedrole",
                'roles' => [User::ROLE_SUPER_ADMIN],
                'password' => "123456"
            ],
        ];
        $this->browser()->patch('api/users/'.$userToken->getUser()->getId(), $header)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test update user's name allowed by ROLE_USER
     *
     * @return void
     */
    public function testUpdateUserNameAllowedByUser(): void
    {
        $userToken = ApiTokenFactory::new()->userRole()->create();
        $header = [
            'headers' => [
                "x-api-token" => $userToken->getToken(),
                "Content-Type" => "application/merge-patch+json"
            ], 'json' => [
                'name' => "Updateusernamebyuser",
                'password' => "123456"
            ],
        ];
        $this->browser()->patch('api/users/'.$userToken->getUser()->getId(), $header)->assertSuccessful();
    }

    /**
     * Test update user's name allowed by ROLE_COMPANY_ADMIN
     *
     * @return void
     */
    public function testUpdateUserAllowedByCompanyAdmin(): void
    {
        $userToken = ApiTokenFactory::new()->companyAdminRole()->create();
        $header = [
            'headers' => [
                "x-api-token" => $userToken->getToken(),
                "Content-Type" => "application/merge-patch+json"
            ], 'json' => [
                'name' => "Updateusernamebycompanyadmin",
                'roles' => [User::ROLE_SUPER_ADMIN],
                'password' => "123456"
            ],
        ];
        $this->browser()->patch('api/users/'.$userToken->getUser()->getId(), $header)->assertSuccessful();
    }

    /**
     * Test update user's name allowed by ROLE_SUPER_ADMIN
     *
     * @return void
     */
    public function testUpdateUserAllowedBySuperAdmin(): void
    {
        $userToken = ApiTokenFactory::new()->superAdminRole()->create();
        $header = [
            'headers' => [
                "x-api-token" => $userToken->getToken(),
                "Content-Type" => "application/merge-patch+json"
            ], 'json' => [
                'name' => "Updateusernamebysuperadmin",
                'roles' => [User::ROLE_USER],
                'password' => "123456"
            ],
        ];
        $this->browser()->patch('api/users/'.$userToken->getUser()->getId(), $header)->assertSuccessful();
    }
}
