<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use AppBundle\Security\TokenAuthenticator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Hoa\Exception\Exception;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use Swagger\Annotations as SWG;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

/**
 * Class UserController
 * @Route("/api")
 * @package ApiBundle\Controller
 */
class UserController extends BaseController
{
    /**
     * @Rest\Get("/user")
     * @SWG\Response(
     *     response=200,
     *     description="For stanadrt will return 10 users on 1 page,
     *          is governed by the parameters limit and page",
     *     @Model(type=User::class)
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     type="integer",
     *     description="Pagination page"
     * )
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     type="integer",
     *     description="Number of users per page"
     * )
     * @SWG\Tag(name="user")
     * @param Request $request
     * @return View|mixed
     */
    public function getAllAction(Request $request)
    {
        try {
            $page  = $request->get('page') ?: 1;
            $limit = $request->get('limit') ?: 10;

            /** @var UserRepository $userRepository */
            $userRepository = $this->getDoctrine()->getRepository(User::class);

            $restresult = $userRepository->findByPage($page, $limit);

            if ($restresult === null) {
                return $this->errorResponse("users not found", 404);
            }
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), $exception->getCode());
        }

        return $restresult;
    }

    /**
     * @Rest\get("/user/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the user with the specified id",
     *     @Model(type=User::class)
     * )
     * @SWG\Tag(name="user")
     * @param int $id
     * @return View|object[]
     */
    public function getAction(int $id)
    {
        $restresult = $this->getDoctrine()->getRepository(User::class)->find($id);
        if ($restresult === null) {
            return $this->errorResponse("there are no user exist", 404);
        }

        return $restresult;
    }

    /**
     * @Rest\Post("/user")
     * @SWG\Response(
     *     response=200,
     *     description="Object with success message",
     *     @Model(type=User::class)
     * )
     * @SWG\Parameter(
     *     name="first_name",
     *     in="query",
     *     type="string",
     *     description="User name"
     * )
     * @SWG\Parameter(
     *     name="last_name",
     *     in="query",
     *     type="string",
     *     description="User surname"
     * )
     * @SWG\Parameter(
     *     name="username",
     *     in="query",
     *     type="string",
     *     description="User login"
     * )
     * @SWG\Parameter(
     *     name="password",
     *     in="query",
     *     type="string",
     *     description="User password"
     * )
     * @SWG\Parameter(
     *     name="password_confirm",
     *     in="query",
     *     type="string",
     *     description="Confirm password"
     * )
     * @SWG\Parameter(
     *     name="email",
     *     in="query",
     *     type="string",
     *     description="User email"
     * )
     * @SWG\Tag(name="user")
     * @param Request $request
     * @return Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function postAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        if ($request->get('password') != $request->get('password_confirm')) {
            return $this->errorResponse('passwords do not match', 400);
        }

        /** @var User $user */
        $user = new User();
        $user
            ->setFirstName($request->get('first_name'))
            ->setLastName($request->get('last_name'))
            ->setUsername($request->get('username'))
            ->setPassword($request->get('password'))
            ->setEmail($request->get('email'))
            ->setEmailCanonical($request->get('email'));

        $em->persist($user);
        $em->flush();

        return $this->successResponse('User successfully registered');
    }

    /**
     * @Rest\Put("/user/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Object with a message what fields have been changed",
     *     @Model(type=User::class)
     * )
     * @SWG\Parameter(
     *     name="first_name",
     *     in="query",
     *     type="string",
     *     description="User name"
     * )
     * @SWG\Parameter(
     *     name="last_name",
     *     in="query",
     *     type="string",
     *     description="User surname"
     * )
     * @SWG\Parameter(
     *     name="username",
     *     in="query",
     *     type="string",
     *     description="User login"
     * )
     * @SWG\Parameter(
     *     name="password",
     *     in="query",
     *     type="string",
     *     description="User password"
     * )
     * @SWG\Parameter(
     *     name="email",
     *     in="query",
     *     type="string",
     *     description="User email"
     * )
     * @SWG\Tag(name="user")
     * @param int     $id
     * @param Request $request
     * @return Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function putAction(int $id, Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var UserRepository $userRepository */
        $userRepository = $this->getDoctrine()->getRepository(User::class);

        /** @var User $user */
        if (!$user = $userRepository->find($id)) {
            return $this->errorResponse('user not found', 404);
        }

        $changed = [];

        if ($request->get('first_name')) {
            $user->setFirstName($request->get('first_name'));
            $changed[] = 'first_name';
        }
        if ($request->get('last_name')) {
            $user->setLastName($request->get('last_name'));
            $changed[] = 'last_name';
        }
        if ($request->get('username')) {
            $user->setUsername($request->get('username'));
            $changed[] = 'username';
        }
        if ($request->get('password')) {
            $user->setPassword($request->get('password'));
            $changed[] = 'password';
        }
        if ($request->get('email')) {
            $user->setEmail($request->get('email'));
            $changed[] = 'email';
        }

        $em->persist($user);
        $em->flush();

        if (empty($changed)) {
            return $this->successResponse('User has not been changed');
        } else {
            $message = 'Has been changed in the user: ';

            foreach ($changed as $item) {
                $message .= "{$item} ";
            }

            return $this->successResponse($message);
        }
    }

    /**
     * @Rest\delete("/user/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Object with success message",
     *     @Model(type=User::class)
     * )
     * @SWG\Tag(name="user")
     * @param $id
     * @return Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteAction($id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var UserRepository $userRepository */
        $userRepository = $this->getDoctrine()->getRepository(User::class);

        if (!$user = $userRepository->find($id)) {
            return $this->errorResponse('user not found', 404);
        }

        $em->remove($user);
        $em->flush();

        return $this->successResponse('User successfully removed');
    }

    /**
     * @Rest\get("/get-user-token")
     * @SWG\Response(
     *     response=200,
     *     description="Return user token",
     * )
     * @SWG\Parameter(
     *     name="username",
     *     in="query",
     *     type="string",
     *     description="User login"
     * )
     *  @SWG\Parameter(
     *     name="password",
     *     in="query",
     *     type="string",
     *     description="User password"
     * )
     * @SWG\Tag(name="user")
     * @param Request $request
     * @return string|Response
     * @throws NonUniqueResultException
     */
    public function getTokenAction(Request $request)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->getDoctrine()->getRepository(User::class);

        /** @var User $user */
        if (!$user = $userRepository->findByUsername($request->get('username'))) {
            return $this->errorResponse('A user with this username does not exist', 404);
        }

        if ($user->getPassword() != password_hash($request->get('password'), PASSWORD_DEFAULT)) {
            return $this->errorResponse('Wrong password', 400);
        }

        return $user->getApiKey();
    }

    /**
     * @Rest\Get("/me")
     * @SWG\Response(
     *     response=200,
     *     description="Return your entity user"
     * )
     * @SWG\Tag(name="user")
     */
    public function getMeAction()
    {
        return $this->getUser();
    }

    /**
     * @return mixed
     */
    public function getBasketItems()
    {
        return $this->getUser()->getBasketProducts();
    }
}