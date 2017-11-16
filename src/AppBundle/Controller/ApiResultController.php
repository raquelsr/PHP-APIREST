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
                    'Usuario no encontrado'
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


}