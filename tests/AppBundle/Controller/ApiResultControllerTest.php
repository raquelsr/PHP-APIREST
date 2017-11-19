<?php

namespace Tests\AppBundle\Controller;


use AppBundle\Entity\Result;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiResultControllerTest
 *
 * @package AppBundle\Tests\Controller
 */
class ApiResultControllerTest extends WebTestCase
{

    const RUTA_API = \AppBundle\Controller\ApiResultController::RUTA_API_RESULT;
    const RUTA_API_USER = \AppBundle\Controller\ApiUserController::RUTA_API;

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
     * Test OPTIONS /results[/resultId] 200 Ok
     *
     * @return void
     *
     * @covers \AppBundle\Controller\ApiResultController::optionsResultAction()
     */
    public function testOptionsResultAction200()
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
     * Test GET /results 200 OK
     *
     * @return void
     *
     * @covers \AppBundle\Controller\ApiResultController::cgetResultAction()
     */
    public function testCGetAction200()
    {
        self::$_client->request(Request::METHOD_GET, self::RUTA_API);
        $response = self::$_client->getResponse();
        self::assertTrue($response->isSuccessful());
        self::assertJson($response->getContent());
        $results = json_decode($response->getContent(), true);
        self::assertArrayHasKey('results', $results);
    }

    /**
     * Test POST /results 201 Created
     *
     * @return array result data
     *
     * @covers \AppBundle\Controller\ApiResultController::postResultAction()
     */
    public function testPostResultAction201()
    {

        //Añade new User
        $rand_num = mt_rand(0, 1000000);
        $nombre = 'Nuevo UsEr POST * ' . $rand_num;
        $p_data = [
            'username' => $nombre,
            'email' => 'email' . $rand_num . '@example.com',
            'password' => 'P4ssW0r4 Us3r P0ST * ñ?¿ áËì·' . $rand_num,
            'enabled' => mt_rand(0, 2),
            'isAdmin' => mt_rand(0, 2)
        ];

        self::$_client->request(
            Request::METHOD_POST, self::RUTA_API_USER,
            [], [], [], json_encode($p_data)
        );

        $response = self::$_client->getResponse();
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $user = json_decode($response->getContent(), true);

        $p_data = [
            'result' => 10,
            'user' => $user['user']['id']
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
        $r_result = json_decode($response->getContent(), true);

        return $r_result['result'];
    }

    /**
     * Test POST /results 404 Not Found User
     *
     * @return void
     *
     * @covers \AppBundle\Controller\ApiResultController::postResultAction()
     */
    public function testPostResultAction404()
    {

        $user = new User('user', 'user@mail.com', '*user*',true,true);
        $result = new Result (10, $user);
        $p_data = [
            'result' => $result->getResult(),
            'user' => $result->getUser()->getId(),
        ];

        // 201
        self::$_client->request(
            Request::METHOD_POST, self::RUTA_API,
            [], [], [], json_encode($p_data)
        );

        $response = self::$_client->getResponse();
        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        self::assertTrue($response->isNotFound());
        self::assertJson($response->getContent());
    }

    /**
     * Test POST /results 422 Unprocessable Entity
     *
     * @return void
     *
     * @covers \AppBundle\Controller\ApiResultController::postResultAction()
     */
    public function testPostResultAction422()
    {
        $p_data = [
            'result'=> 10,
            // 'user' => $user
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

        // SIN RESULT
        $user = new User('user', 'user@mail.com', '*user*',true,true);
        $p_data = [
            //'result'=> 10,
              'user' => $user->getId()
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
            //'result'=> 10,
            // 'user' => $user
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
     * Test GET /results/resultId 200 Ok
     *
     * @param array $result result returned by testPostResultAction201()
     *
     * @return void
     *
     * @covers  \AppBundle\Controller\ApiResultController::getResultAction()
     * @depends testPostResultAction201
     */
    public function testGetResultAction200(array $result)
    {
        self::$_client->request(
            Request::METHOD_GET,
            self::RUTA_API . '/' . $result['id']
        );
        $response = self::$_client->getResponse();

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertJson((string) $response->getContent());
        $result_aux = json_decode((string) $response->getContent(), true);
        self::assertEquals($result, $result_aux['result']);

    }

    /**
     * Test GET /results/result/valueResult 200 Ok
     *
     * @param array $result result returned by testPostResultAction201()
     *
     * @return void
     *
     * @covers  \AppBundle\Controller\ApiResultController::getResultByResultAction()
     * @depends testPostResultAction201
     */
    public function testGetResultByResultAction200(array $result)
    {
        self::$_client->request(
            Request::METHOD_GET,
            self::RUTA_API . '/result/' . $result['result']
        );
        $response = self::$_client->getResponse();
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertJson((string) $response->getContent());
        $result_aux = json_decode((string) $response->getContent(), true);
        self::assertEquals($result['result'], $result_aux[0]['result']['result']);

    }

    /**
     * Test GET /results/result/userId 200 Ok
     *
     * @param array $result result returned by testPostResultAction201()
     *
     * @return void
     *
     * @covers  \AppBundle\Controller\ApiResultController::getResultByUserAction()
     * @depends testPostResultAction201
     */
    public function testGetResultByUserAction200(array $result)
    {
        self::$_client->request(
            Request::METHOD_GET,
            self::RUTA_API . '/user/' . $result['user']['user']['id']
        );
        $response = self::$_client->getResponse();
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertJson((string) $response->getContent());
        $result_aux = json_decode((string) $response->getContent(), true);
        self::assertEquals($result['user'], $result_aux[0]['result']['user']);

    }


    /**
     * Test PUT /results/resutId 209 Content Returned
     *
     * @param array $result result returned by testPostResultAction201()
     *
     * @return array modified result data
     *
     * @covers  \AppBundle\Controller\ApiResultController::putResultAction()
     * @depends testPostResultAction201
     */
    public function testPutResultAction209(array $result)
    {
        $p_data = [
            'result' => 2
        ];

        self::$_client->request(
            Request::METHOD_PUT, self::RUTA_API . '/' . $result['id'],
            [], [], [], json_encode($p_data)
        );
        $response = self::$_client->getResponse();

        self::assertEquals(209, $response->getStatusCode());
        self::assertJson((string) $response->getContent());
        $result_aux = json_decode((string) $response->getContent(), true);
        self::assertEquals($p_data['result'],  $result_aux['result']['result']);

        $aux_result = $result_aux['result'];


        //Añade new User
        /*$rand_num = mt_rand(0, 1000000);
        $nombre = 'Nuevo UsEr POST * ' . $rand_num;
        $p_data = [
            'username' => $nombre,
            'email' => 'email' . $rand_num . '@example.com',
            'password' => 'P4ssW0r4 Us3r P0ST * ñ?¿ áËì·' . $rand_num,
            'enabled' => mt_rand(0, 2),
            'isAdmin' => mt_rand(0, 2)
        ];

        self::$_client->request(
            Request::METHOD_POST, self::RUTA_API_USER,
            [], [], [], json_encode($p_data)
        );

        $response = self::$_client->getResponse();
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $user = json_decode($response->getContent(), true);

        $p_data = [
            'user' => $user['user']['id']
        ];

        self::$_client->request(
            Request::METHOD_PUT, self::RUTA_API . '/' . $result['id'],
            [], [], [], json_encode($p_data)
        );
        $response = self::$_client->getResponse();

        self::assertEquals(209, $response->getStatusCode());
        self::assertJson((string) $response->getContent());
        $result_aux = json_decode((string) $response->getContent(), true);
        self::assertEquals($p_data['user'],  $result_aux['result']['user']['user']['id']);

        $p_data = [
            'result' => 8,
            'user' => $user['user']['id']
        ];

        self::$_client->request(
            Request::METHOD_PUT, self::RUTA_API . '/' . $result['id'],
            [], [], [], json_encode($p_data)
        );
        $response = self::$_client->getResponse();

        self::assertEquals(209, $response->getStatusCode());
        self::assertJson((string) $response->getContent());
        $result_aux = json_decode((string) $response->getContent(), true);
        self::assertEquals($p_data['user'],  $result_aux['result']['user']['user']['id']);
        self::assertEquals($p_data['result'], $result_aux['result']['result']);

*/
        return $aux_result;
    }


    /**
     * Test PUT /results/resutId 209 Content Returned
     *
     * @param array $result result returned by testPostResultAction201()
     *
     * @return void
     *
     * @covers  \AppBundle\Controller\ApiResultController::putResultAction()
     * @depends testPostResultAction201
     */

    public function testPutResultActionUser404(array $result)
    {
        $user = new User('user', 'user@mail.com', '*user*',true, true);
        $p_data = [
            'result' => 8,
            'user' => $user->getId()
        ];

        self::$_client->request(
            Request::METHOD_PUT, self::RUTA_API . '/' . $result['id'],
            [], [], [], json_encode($p_data)
        );
        $response = self::$_client->getResponse();

        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $r_body = (string) $response->getContent();
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertEquals(Response::HTTP_NOT_FOUND, $r_data['code']);
        self::assertEquals('El usuario no existe', $r_data['message']);

    }

    /**
     * Test DELETE /results/resultId 204 No Content
     *
     * @param array $result result returned by testPostResultAction201()
     *
     * @return int resultId
     *
     * @covers  \AppBundle\Controller\ApiResultController::deleteResultAction()
     * @depends testPostResultAction201
     * @depends testGetResultAction200
     * @depends testPutResultAction209
     */
    public function testDeleteResultAction204(array $result)
    {
        self::$_client->request(
            Request::METHOD_DELETE,
            self::RUTA_API . '/' . $result['id']
        );
        $response = self::$_client->getResponse();

        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        self::assertEmpty((string) $response->getContent());

        return $result['id'];
    }

    /**
     * Test DELETE /users/userId 204 No Content
     *
     * @param array $result result returned by testPostResultAction201()
     *
     *
     * @covers  \AppBundle\Controller\ApiUserController::deleteUserAction()
     * @depends testPostResultAction201
     * @depends testGetResultAction200
     * @depends testPutResultAction209
     */
    public function testDeleteUserAction204(array $result)
    {
        self::$_client->request(
            Request::METHOD_DELETE,
            self::RUTA_API_USER . '/' . $result['user']['user']['id']
        );
        $response = self::$_client->getResponse();

        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        self::assertEmpty((string) $response->getContent());

    }


    /**
     * Test DELETE /results/resultId 404 Not Found
     *
     * @param int $resultId result id. returned by testDeleteResultAction204()
     *
     * @return void
     *
     * @covers  \AppBundle\Controller\ApiResultController::deleteResultAction()
     * @depends testDeleteResultAction204
     */
    public function testDeleteResultAction404(int $resultId)
    {
        self::$_client->request(
            Request::METHOD_DELETE,
            self::RUTA_API . '/' . $resultId
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
     * Test DELETE /results/result/valueResult 404 Not Found
     *
     * @return void
     *
     * @covers  \AppBundle\Controller\ApiResultController::deleteResultByResultAction()
     */
    public function testDeleteResultByResultAction404()
    {
        $result = 1111111111111;
        self::$_client->request(
            Request::METHOD_DELETE,
            self::RUTA_API . '/result/' . $result
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
     * Test DELETE /results/user/userId 404 Not Found
     *
     * @return void
     *
     * @covers  \AppBundle\Controller\ApiResultController::deleteResultByUserAction()
     */
    public function testDeleteResultByUserAction404()
    {
        $user = 1111111111111;
        self::$_client->request(
            Request::METHOD_DELETE,
            self::RUTA_API . '/user/' . $user
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
     * Test GET /results/resultId 404 Not Found
     *
     * @param int $resultId result id. returned by testDeleteResultAction204()
     *
     * @return void
     *
     * @covers  \AppBundle\Controller\ApiResultController::getResultAction()
     * @depends testDeleteResultAction204
     */
    public function testGetResultAction404(int $resultId)
    {
        self::$_client->request(Request::METHOD_GET, self::RUTA_API . '/' . $resultId);
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
     * Test GET /results/result/valueResult 404 Not Found
     **
     * @return void
     *
     * @covers  \AppBundle\Controller\ApiResultController::getResultByResultAction()
     */
    public function testGetResultByResultAction404()
    {
        $result = 1111111111111;
        self::$_client->request(Request::METHOD_GET, self::RUTA_API . '/result/' . $result);
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
     * Test GET /results/user/userId 404 Not Found
     *
     *
     * @return void
     *
     * @covers  \AppBundle\Controller\ApiResultController::getResultByUserAction()
     * @depends testDeleteResultAction204
     */
    public function testGetResultByUserAction404()
    {
        $userId = 1111111111111;
        self::$_client->request(Request::METHOD_GET, self::RUTA_API . '/user/' . $userId);
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
     * Test PUT /results/resultId 404 Not Found
     *
     * @param int $userId user id. returned by testDeleteResultAction204()
     *
     * @return void
     *
     * @covers  \AppBundle\Controller\ApiResultController::putResultAction()
     * @depends testDeleteResultAction204
     */
    public function testPutResultAction404(int $resultId)
    {
        self::$_client->request(Request::METHOD_PUT, self::RUTA_API . '/' . $resultId);
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