<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * Class ApiUserControllerTest
 *
 * @package AppBundle\Tests\Controller
 */
class ApiUserControllerTest extends WebTestCase
{
    const RUTA_API = \AppBundle\Controller\ApiUserController::RUTA_API;

    /**
     * Client
     *
     * @var Client $_client
     */
    private static $_client;

    /**
     * This method is called before the first test of this test class is run.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        self::$_client = static::createClient();
    }

    /**
     * This method is called after the last test of this test class is run.
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
    }

    /**
     * Test OPTIONS /users[/userId] 200 Ok
     *
     * @return void
     *
     * @covers \AppBundle\Controller\ApiUserController::optionsUserAction()
     */
    public function testOptionsUserAction200()
    {
        self::$_client->request(Request::METHOD_OPTIONS, self::RUTA_API);
        $response = self::$_client->getResponse();

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertNotEmpty($response->headers->get('Allow'));

        self::$_client->request(
            Request::METHOD_OPTIONS,
            self::RUTA_API . '/' . mt_rand(0, 1000000)
        );

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertNotEmpty($response->headers->get('Allow'));
    }

    /**
     * Test GET /users 200 Ok
     *
     * @return void
     *
     * @covers \AppBundle\Controller\ApiUserController::cgetUserAction()
     */
    public function testCGetAction200()
    {
        self::$_client->request(Request::METHOD_GET, self::RUTA_API);
        $response = self::$_client->getResponse();
        self::assertTrue($response->isSuccessful());
        self::assertJson($response->getContent());
        $users = json_decode($response->getContent(), true);
        self::assertArrayHasKey('users', $users);
    }

    /**
     * Test POST /users 201 Created
     *
     * @return array user data
     *
     * @covers \AppBundle\Controller\ApiUserController::postUserAction()
     */
    public function testPostUserAction201()
    {
        $rand_num = mt_rand(0, 1000000);
        $nombre = 'Nuevo UsEr POST * ' . $rand_num;
        $p_data = [
            'username' => $nombre,
            'email'    => 'email' . $rand_num . '@example.com',
            'password' => 'P4ssW0r4 Us3r P0ST * ñ?¿ áËì·' . $rand_num,
            'enabled'  => mt_rand(0, 2),
            'isAdmin'  => mt_rand(0, 2)
        ];

        // 201
        self::$_client->request(
            Request::METHOD_POST, self::RUTA_API,
            [], [], [], json_encode($p_data)
        );
        $response = self::$_client->getResponse();
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        self::assertTrue($response->isSuccessful());
        self::assertJson($response->getContent());
        $user = json_decode($response->getContent(), true);

        return $user['user'];
    }

    /**
     * Test POST /users 422 Unprocessable Entity
     *
     * @return void
     *
     * @covers \AppBundle\Controller\ApiUserController::postUserAction()
     */
    public function testPostUserAction422()
    {
        $rand_num = mt_rand(0, 1000000);
        $nombre = 'Nuevo UsEr POST * ' . $rand_num;
        $p_data = [
            // 'username' => $nombre,
            'email' => 'email' . $rand_num . '@example.com',
            'password' => 'PassW0r4 UsEr POST * ñ?¿' . $rand_num
        ];

        self::$_client->request(
            Request::METHOD_POST, self::RUTA_API,
            [], [], [], json_encode($p_data)
        );
        $response = self::$_client->getResponse();

        self::assertEquals(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $response->getStatusCode()
        );
        $r_body = (string) $response->getContent();
        self::assertJson($r_body);
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $r_data['code']);
        self::assertEquals(
            Response::$statusTexts[422],
            $r_data['message']
        );

        $p_data = [
            'username' => $nombre,
            // 'email' => 'email' . $rand_num . '@example.com',
            'password' => 'PassW0r4 UsEr POST * ñ?¿' . $rand_num
        ];
        self::$_client->request(
            Request::METHOD_POST, self::RUTA_API,
            [], [], [], json_encode($p_data)
        );
        $response = self::$_client->getResponse();

        self::assertEquals(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $response->getStatusCode()
        );
        $r_body = (string) $response->getContent();
        self::assertJson($r_body);
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $r_data['code']);
        self::assertEquals(
            Response::$statusTexts[422],
            $r_data['message']
        );

        $p_data = [
            'username' => $nombre,
            'email' => 'email' . $rand_num . '@example.com',
            // 'password' => 'PassW0r4 UsEr POST * ñ?¿' . $rand_num
        ];
        self::$_client->request(
            Request::METHOD_POST, self::RUTA_API,
            [], [], [], json_encode($p_data)
        );
        $response = self::$_client->getResponse();

        self::assertEquals(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $response->getStatusCode()
        );
        $r_body = (string) $response->getContent();
        self::assertJson($r_body);
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $r_data['code']);
        self::assertEquals(
            Response::$statusTexts[422],
            $r_data['message']
        );

        $p_data = [
            'username' => $nombre,
            // 'email' => 'email' . $rand_num . '@example.com',
            // 'password' => 'PassW0r4 UsEr POST * ñ?¿' . $rand_num
        ];
        self::$_client->request(
            Request::METHOD_POST, self::RUTA_API,
            [], [], [], json_encode($p_data)
        );
        $response = self::$_client->getResponse();

        self::assertEquals(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $response->getStatusCode()
        );
        $r_body = (string) $response->getContent();
        self::assertJson($r_body);
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $r_data['code']);
        self::assertEquals(
            Response::$statusTexts[422],
            $r_data['message']
        );
    }

    /**
     * Test POST /users 400 Bad Request
     *
     * @param array $user user returned by testPostUserAction201()
     *
     * @return void
     *
     * @covers  \AppBundle\Controller\ApiUserController::postUserAction()
     * @depends testPostUserAction201
     */
    public function testPostUserAction400(array $user)
    {
        $rand_num = mt_rand(0, 1000000);
        $nombre = 'Nuevo UsEr POST * ' . $rand_num;
        $p_data = [
            'username' => $user['username'],    // mismo nombre
            'email' => 'emailX' . $rand_num . '@example.com',
            'password' => 'PassW0r4 UsEr POST * ñ?¿' . $rand_num
        ];
        self::$_client->request(
            Request::METHOD_POST, self::RUTA_API,
            [], [], [], json_encode($p_data)
        );
        $response = self::$_client->getResponse();

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $r_body = (string) $response->getContent();
        self::assertJson($r_body);
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $r_data['code']);
        self::assertEquals(
            Response::$statusTexts[400],
            $r_data['message']
        );

        $p_data = [
            'username' => $nombre,
            'email' => $user['email'],
            'password' => 'PassW0r4 UsEr POST * ñ?¿' . $rand_num
        ];
        self::$_client->request(
            Request::METHOD_POST, self::RUTA_API,
            [], [], [], json_encode($p_data)
        );
        $response = self::$_client->getResponse();

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $r_body = (string) $response->getContent();
        self::assertJson($r_body);
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $r_data['code']);
        self::assertEquals(
            Response::$statusTexts[400],
            $r_data['message']
        );
    }

    /**
     * Test GET /users/userId 200 Ok
     *
     * @param array $user user returned by testPostUserAction201()
     *
     * @return void
     *
     * @covers  \AppBundle\Controller\ApiUserController::getUserAction()
     * @depends testPostUserAction201
     */
    public function testGetUserAction200(array $user)
    {
        self::$_client->request(
            Request::METHOD_GET,
            self::RUTA_API . '/' . $user['id']
        );
        $response = self::$_client->getResponse();

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertJson((string) $response->getContent());
        $user_aux = json_decode((string) $response->getContent(), true);
        self::assertEquals($user, $user_aux['user']);
    }

    /**
     * Test PUT /users/userId 209 Content Returned
     *
     * @param array $user user returned by testPostUserAction201()
     *
     * @return array modified user data
     *
     * @covers  \AppBundle\Controller\ApiUserController::putUserAction()
     * @depends testPostUserAction201
     */
    public function testPutUserAction209(array $user)
    {
        $rand_num = mt_rand(0, 1000000);
        $p_data = [
            'username' => 'Nuevo UsEr PUT * ' . $rand_num,
            'email' => 'emailXPUT-' . $rand_num . '@example.com',
            'password' => 'PassW0r4 UsEr PUT * ñ?¿' . $rand_num,
            'enabled' => mt_rand(0, 2),
            'isAdmin' => mt_rand(0, 2)
        ];

        self::$_client->request(
            Request::METHOD_PUT, self::RUTA_API . '/' . $user['id'],
            [], [], [], json_encode($p_data)
        );
        $response = self::$_client->getResponse();

        self::assertEquals(209, $response->getStatusCode());
        self::assertJson((string) $response->getContent());
        $user_aux = json_decode((string) $response->getContent(), true);
        self::assertEquals($user['id'],         $user_aux['user']['id']);
        self::assertEquals($p_data['username'], $user_aux['user']['username']);
        self::assertEquals($p_data['email'],    $user_aux['user']['email']);
        self::assertEquals($p_data['enabled'],  $user_aux['user']['enabled']);
        self::assertEquals($p_data['isAdmin'],  $user_aux['user']['admin']);

        return $user_aux['user'];
    }

    /**
     * Test PUT /users/userId 400 Bad Request
     *
     * @param array $user user returned by testPutUserAction209()
     *
     * @return void
     *
     * @covers  \AppBundle\Controller\ApiUserController::putUserAction()
     * @depends testPutUserAction209
     */
    public function testPutUserAction400(array $user)
    {
        // username already exists
        $p_data = [
            'username' => $user['username']
        ];

        self::$_client->request(
            Request::METHOD_PUT, self::RUTA_API . '/' . $user['id'],
            [], [], [], json_encode($p_data)
        );
        $response = self::$_client->getResponse();

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $r_body = (string) $response->getContent();
        self::assertJson($r_body);
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $r_data['code']);
        self::assertEquals(
            Response::$statusTexts[400],
            $r_data['message']
        );

        // e-mail already exists
        $p_data = [
            'email' => $user['email']
        ];
        self::$_client->request(
            Request::METHOD_PUT, self::RUTA_API . '/' . $user['id'],
            [], [], [], json_encode($p_data)
        );
        $response = self::$_client->getResponse();

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $r_body = (string) $response->getContent();
        self::assertJson($r_body);
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $r_data['code']);
        self::assertEquals(
            Response::$statusTexts[400],
            $r_data['message']
        );
    }

    /**
     * Test DELETE /users/userId 204 No Content
     *
     * @param array $user user returned by testPostUserAction201()
     *
     * @return int userId
     *
     * @covers  \AppBundle\Controller\ApiUserController::deleteUserAction()
     * @depends testPostUserAction201
     * @depends testPostUserAction400
     * @depends testGetUserAction200
     * @depends testPutUserAction400
     */
    public function testDeleteUserAction204(array $user)
    {
        self::$_client->request(
            Request::METHOD_DELETE,
            self::RUTA_API . '/' . $user['id']
        );
        $response = self::$_client->getResponse();

        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        self::assertEmpty((string) $response->getContent());

        return $user['id'];
    }
    /**
     * Test DELETE /users/userId 404 Not Found
     *
     * @param int $userId user id. returned by testDeleteUserAction204()
     *
     * @return void
     *
     * @covers  \AppBundle\Controller\ApiUserController::deleteUserAction()
     * @depends testDeleteUserAction204
     */
    public function testDeleteUserAction404(int $userId)
    {
        self::$_client->request(
            Request::METHOD_DELETE,
            self::RUTA_API . '/' . $userId
        );
        $response = self::$_client->getResponse();

        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $r_body = (string) $response->getContent();
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertEquals(Response::HTTP_NOT_FOUND, $r_data['code']);
        self::assertEquals(Response::$statusTexts[404], $r_data['message']);
    }

    /**
     * Test GET /users/userId 404 Not Found
     *
     * @param int $userId user id. returned by testDeleteUserAction204()
     *
     * @return void
     *
     * @covers  \AppBundle\Controller\ApiUserController::getUserAction()
     * @depends testDeleteUserAction204
     */
    public function testGetUserAction404(int $userId)
    {
        self::$_client->request(Request::METHOD_GET, self::RUTA_API . '/' . $userId);
        $response = self::$_client->getResponse();

        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $r_body = (string) $response->getContent();
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertEquals(Response::HTTP_NOT_FOUND, $r_data['code']);
        self::assertEquals(Response::$statusTexts[404], $r_data['message']);
    }

    /**
     * Test PUT /users/userId 404 Not Found
     *
     * @param int $userId user id. returned by testDeleteUserAction204()
     *
     * @return void
     *
     * @covers  \AppBundle\Controller\ApiUserController::putUserAction()
     * @depends testDeleteUserAction204
     */
    public function testPutUserAction404(int $userId)
    {
        self::$_client->request(Request::METHOD_PUT, self::RUTA_API . '/' . $userId);
        $response = self::$_client->getResponse();

        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $r_body = (string) $response->getContent();
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertEquals(Response::HTTP_NOT_FOUND, $r_data['code']);
        self::assertEquals(Response::$statusTexts[404], $r_data['message']);
    }
}
