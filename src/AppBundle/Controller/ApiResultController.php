<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Entity\Result;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiResultController
 *
 * @package AppBundle\Controller
 *
 * @Route(ApiResultController::RUTA_API_RESULT)
 */
class ApiResultController extends Controller
{

    const RUTA_API_RESULT = '/api/v1/results';

    /**
     *
     * @return JsonResponse
     *
     * @Route("", name="miw_cget_results")
     * @Method(Request::METHOD_GET)
     */
    public function cgetResultAction()
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Result');
        $results = $repo->findAll();

        return empty($results)
            ? new JsonResponse(
                new Message(
                    Response::HTTP_NOT_FOUND,
                    Response::$statusTexts[404]
                ),
                Response::HTTP_NOT_FOUND
            )
            : new JsonResponse(['results' => $results]);
    }

    /**
     *
     * @param int $resultId Result id
     *
     * @return JsonResponse
     *
     * @Route("/{resultId}", name="miw_get_results", requirements={"resultId": "\d+"})
     * @Method(Request::METHOD_GET)
     */
    public function getResultAction(int $resultId)
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Result');
        $result = $repo->find($resultId);

        return empty($result)
            ? new JsonResponse(
                new Message(Response::HTTP_NOT_FOUND, Response::$statusTexts[404]),
                Response::HTTP_NOT_FOUND
            )
            : new JsonResponse($result);
    }

    /**
     * POST action
     *
     * @param Request $request request
     *
     * @return JsonResponse
     *
     * @Route("", name="miw_post_results")
     * @Method(Request::METHOD_POST)
     */
    public function postResultAction(Request $request)
    {
        $body = $request->getContent(false);
        $postData = json_decode($body, true);

        if (!isset($postData['result'], $postData['user'])) { // 422 - Unprocessable Entity Faltan datos

            return new JsonResponse(
                new Message(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[422]
                ),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $entityManager = $this->getDoctrine()->getManager();

        $userId = $postData['user'];
        // 201 - Created
        $userRepository = $entityManager->getRepository(User::class);
        /* @var User $user */
        $user = $userRepository->find($userId);

        if (empty($user)){
            return new JsonResponse(
                new Message(
                    Response::HTTP_NOT_FOUND,
                    'El usuario no existe'
                ),
                Response::HTTP_NOT_FOUND
            );
        } else {
            $result = new Result(
                $postData['result'],
                $user
            );
        }


        $entityManager->persist($result);
        $entityManager->flush();

        return new JsonResponse($result, Response::HTTP_CREATED);
    }

    /**
     *
     * @param int $resultId Result id
     *
     * @return Response
     *
     * @Route("/{resultId}", name="miw_delete_results", requirements={"resultId": "\d+"})
     * @Method(Request::METHOD_DELETE)
     */
    public function deleteResultAction(int $resultId)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $result = $entityManager->getRepository(Result::class)->find($resultId);

        if (empty($result)) {   // 404 - Not Found
            return new JsonResponse(
                new Message(
                    Response::HTTP_NOT_FOUND,
                    Response::$statusTexts[404]
                ),
                Response::HTTP_NOT_FOUND
            );
        }

        $entityManager->remove($result);
        $entityManager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }


    /**
     *
     * @param Request $request request
     * @param int     $resultId  Result id
     *
     * @return JsonResponse
     *
     * @Route("/{resultId}", name="miw_put_results", requirements={"resultId": "\d+"})
     * @Method(Request::METHOD_PUT)
     */
    public function putResultAction(Request $request, int $resultId)
    {
        $body = $request->getContent(false);
        $postData = json_decode($body, true);

        $entityManager = $this->getDoctrine()->getManager();
        /** @var Result $result */
        $result = $entityManager->getRepository(Result::class)->find($resultId);

        if (empty($result)) {    // 404 - Not Found
            return new JsonResponse(
                new Message(Response::HTTP_NOT_FOUND, Response::$statusTexts[404]),
                Response::HTTP_NOT_FOUND
            );
        }

        if (isset($postData['result'])) {
            $result->setResult($postData['result']);
        }

        if (isset($postData['user'])) {
            $userReposity = $entityManager->getRepository(User::class);
            /** @var User $user */
            $user = $userReposity->find($postData['user']);
            if (empty($user)){
                return new JsonResponse(
                    new Message(
                        Response::HTTP_NOT_FOUND,
                        'El usuario no existe'
                    ),
                    Response::HTTP_NOT_FOUND
                );
            } else {
                $result->setUser($user);
            }
        }

        $result->setTime(new \DateTime('now'));

        $entityManager->merge($result);
        $entityManager->flush();

        return new JsonResponse($result, 209);    // 209 - Content Returned
    }


    /**
     * @param int $resultId Result id
     *
     * @return JsonResponse
     *
     * @Route(
     *     "/{resultId}",
     *     name = "miw_options_results",
     *     defaults = {"resultId" = 0},
     *     requirements = {"resultId": "\d+"}
     *     )
     * @Method(Request::METHOD_OPTIONS)
     */
    public function optionsResultAction(int $resultId)
    {
        $methods = ($resultId)
            ? ['GET', 'PUT', 'DELETE']
            : ['GET', 'POST'];

        return new JsonResponse(
            null,
            Response::HTTP_OK,
            ['Allow' => implode(', ', $methods)]
        );
    }

}