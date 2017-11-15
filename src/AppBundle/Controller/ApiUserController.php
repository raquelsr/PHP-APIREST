<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\Criteria;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiUserController
 *
 * @package AppBundle\Controller
 *
 * @Route(ApiUserController::RUTA_API)
 */
class ApiUserController extends Controller
{

    const RUTA_API = '/api/v1/users';

    /**
     * Summary: Returns all users
     * Notes: Returns all users from the system that the user has access to.
     *
     * @return JsonResponse
     *
     * @Route("", name="miw_cget_users")
     * @Method(Request::METHOD_GET)
     */
    public function cgetUserAction()
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:User');
        $users = $repo->findAll();

        return empty($users)
            ? new JsonResponse(
                new Message(
                    Response::HTTP_NOT_FOUND,
                    Response::$statusTexts[404]
                ),
                Response::HTTP_NOT_FOUND
            )
            : new JsonResponse(['users' => $users]);
    }

    /**
     * Summary: Returns a user based on a single ID
     * Notes: Returns the user identified by &#x60;userId&#x60;.
     *
     * @param int $userId User id
     *
     * @return JsonResponse
     *
     * @Route("/{userId}", name="miw_get_users", requirements={"userId": "\d+"})
     * @Method(Request::METHOD_GET)
     */
    public function getUserAction(int $userId)
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:User');
        $user = $repo->findOneBy(['id' => $userId]);

        return empty($user)
            ? new JsonResponse(
                new Message(Response::HTTP_NOT_FOUND, Response::$statusTexts[404]),
                Response::HTTP_NOT_FOUND
            )
            : new JsonResponse($user);
    }

    /**
     * POST action
     *
     * @param Request $request request
     *
     * @return JsonResponse
     *
     * @Route("", name="miw_post_users")
     * @Method(Request::METHOD_POST)
     */
    public function postUserAction(Request $request)
    {
        $body = $request->getContent(false);
        $postData = json_decode($body, true);

        if (!isset($postData['username'], $postData['email'], $postData['password'])) { // 422 - Unprocessable Entity Faltan datos

            return new JsonResponse(
                new Message(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[422]
                ),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $entityManager = $this->getDoctrine()->getManager();

        // hay datos -> procesarlos
        /* @var \Doctrine\Common\Collections\Criteria $criteria */
        $criteria = new Criteria();
        $criteria
            ->where($criteria::expr()->eq('username', $postData['username']))
            ->orWhere($criteria::expr()->eq('email', $postData['email']));
        $user_exist = $entityManager
            ->getRepository(User::class)
            ->matching($criteria);

        if (count($user_exist)) {    // 400 - Bad Request

            return new JsonResponse(
                new Message(
                    Response::HTTP_BAD_REQUEST,
                    Response::$statusTexts[400]
                ),
                Response::HTTP_BAD_REQUEST
            );
        }

        // 201 - Created
        $user = new User(
            $postData['username'],
            $postData['email'],
            $postData['password'],
            $postData['enabled'] ?? false,
            $postData['isAdmin'] ?? false
        );
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse($user, Response::HTTP_CREATED);
    }

    /**
     * Summary: Deletes a user
     * Notes: Deletes the user identified by &#x60;userId&#x60;.
     *
     * @param int $userId User id
     *
     * @return Response
     *
     * @Route("/{userId}", name="miw_delete_users", requirements={"userId": "\d+"})
     * @Method(Request::METHOD_DELETE)
     */
    public function deleteUserAction(int $userId)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $entityManager
            ->getRepository(User::class)
            ->findOneBy(['id' => $userId]);

        if (empty($user)) {   // 404 - Not Found
            return new JsonResponse(
                new Message(
                    Response::HTTP_NOT_FOUND,
                    Response::$statusTexts[404]
                ),
                Response::HTTP_NOT_FOUND
            );
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Summary: Updates a user
     * Notes: Updates the user identified by &#x60;userId&#x60;.
     *
     * @param Request $request request
     * @param int     $userId  User id
     *
     * @return JsonResponse
     *
     * @Route("/{userId}", name="miw_put_users", requirements={"userId": "\d+"})
     * @Method(Request::METHOD_PUT)
     */
    public function putUserAction(Request $request, int $userId)
    {
        $body = $request->getContent(false);
        $postData = json_decode($body, true);

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager
            ->getRepository(User::class)
            ->findOneBy(['id' => $userId]);

        if (null == $user) {    // 404 - Not Found
            return new JsonResponse(
                new Message(Response::HTTP_NOT_FOUND, Response::$statusTexts[404]),
                Response::HTTP_NOT_FOUND
            );
        }

        if (isset($postData['username']) || isset($postData['email'])) {
            /* @var \Doctrine\Common\Collections\Criteria $criteria */
            $criteria = new Criteria();
            if (isset($postData['username'], $postData['email'])) {
                $criteria
                    ->where($criteria::expr()->eq('username', $postData['username']))
                    ->orWhere($criteria::expr()->eq('email', $postData['email']));
            } elseif (isset($postData['username'])) {
                $criteria
                    ->where($criteria::expr()->eq('username', $postData['username']));
            } else {
                $criteria
                    ->where($criteria::expr()->eq('email', $postData['email']));
            }

            $usuarios = $entityManager
                ->getRepository(User::class)
                ->matching($criteria);

            if (count($usuarios)) {    // 400 - Bad Request
                return new JsonResponse(
                    new Message(
                        Response::HTTP_BAD_REQUEST,
                        Response::$statusTexts[400]
                    ),
                    Response::HTTP_BAD_REQUEST
                );
            }
            if (isset($postData['username'])) {
                $user->setUsername($postData['username']);
            }
            if (isset($postData['email'])) {
                $user->setEmail($postData['email']);
            }
        }

        // password
        if (isset($postData['password'])) {
            $user->setPassword($postData['password']);
        }

        // enabled
        if (isset($postData['enabled'])) {
            $user->setEnabled($postData['enabled']);
        }

        // isAdmin
        if (isset($postData['isAdmin'])) {
            $user->setAdmin($postData['isAdmin']);
        }

        $entityManager->merge($user);
        $entityManager->flush();

        return new JsonResponse($user, 209);    // 209 - Content Returned
    }

    /**
     * Summary: Provides the list of HTTP supported methods
     * Notes: Return a &#x60;Allow&#x60; header with a list of HTTP supported methods.
     *
     * @param int $userId User id
     *
     * @return JsonResponse
     *
     * @Route(
     *     "/{userId}",
     *     name = "miw_options_users",
     *     defaults = {"userId" = 0},
     *     requirements = {"userId": "\d+"}
     *     )
     * @Method(Request::METHOD_OPTIONS)
     */
    public function optionsUserAction(int $userId)
    {
        $methods = ($userId)
            ? ['GET', 'PUT', 'DELETE']
            : ['GET', 'POST'];

        return new JsonResponse(
            null,
            Response::HTTP_OK,
            ['Allow' => implode(', ', $methods)]
        );
    }
}
