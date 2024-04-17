<?php

namespace App\Tests;

use App\Entity\User;
use App\Factory\ApiTokenFactory;
use App\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class UserTest extends BaseApiTestCase
{

    public function testGetCollection(): void
    {
        $userToken = ApiTokenFactory::new()->superAdminRole()->create();
        $header = [
            'headers' =>
                [
                    "x-api-token" => $userToken->getToken()
                ]
        ];
        $this->browser()->get('api/users', $header)->assertJson();
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
     * Test create user not allowed user role
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
     * Test create user not allowed user role
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
                'name' => "UserCompanyAdminRole",
                'roles' => [User::ROLE_USER],
                'password' => "123456"
            ],
        ];
        $this->browser()->post('api/users', $header)->assertSuccessful();
    }

    /**
     * Test delete user allowed only for super admin role
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
            ], 'json' => [
                'name' => "DeleteUserBySuperAdmin",
                'roles' => [User::ROLE_USER],
                'password' => "123456"
            ],
        ];
        $this->browser()->delete('api/users/' . $user->getId(), $header)->assertSuccessful();
    }

    /**
     * Test delete user not allowed user role
     *
     * @return void
     */
    public function testDeleteUserNotAllowedUserRole(): void
    {
        $userToken = ApiTokenFactory::new()->userRole()->create();
        $user = UserFactory::createOne();
        $header = [
            'headers' => [
                "x-api-token" => $userToken->getToken(),
            ], 'json' => [
                'name' => "UserName",
                'roles' => [User::ROLE_USER],
                'password' => "123456"
            ],
        ];
        $this->browser()->delete('api/users/' . $user->getId(), $header)->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test delete user not allowed company admin role
     *
     * @return void
     */
    public function testDeleteUserNotAllowedCompanyAdminRole(): void
    {
        $userToken = ApiTokenFactory::new()->companyAdminRole()->create();
        $user = UserFactory::createOne();
        $header = [
            'headers' => [
                "x-api-token" => $userToken->getToken(),
            ], 'json' => [
                'name' => "UserName",
                'roles' => [User::ROLE_USER],
                'password' => "123456"
            ],
        ];
        $this->browser()->delete('api/users/' . $user->getId(), $header)->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
