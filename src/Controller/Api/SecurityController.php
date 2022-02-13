<?php

namespace CoralMedia\Bundle\SecurityBundle\Controller\Api;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Throwable;

/**
 * Class SecurityController
 * @Route("/api/security")
 * @package CoralMedia\Bundle\SecurityBundle\Controller\Api
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/token/check", name="cm_api_security_check_auth_token")
     * @param Request $request
     * @param JWTEncoderInterface $jwtEncoder
     * @return JsonResponse
     */
    public function checkAuthToken(Request $request, JWTEncoderInterface $jwtEncoder): JsonResponse
    {
        try {
            $jwtEncoder->decode($request->toArray()['token']);
            return new JsonResponse(
                ['token' => $request->toArray()['token']],
                Response::HTTP_ACCEPTED
            );
        } catch (Throwable $e) {
            return new JsonResponse(
                ['message' => $e->getMessage()],
                Response::HTTP_UNAUTHORIZED
            );
        }
    }

    /**
     * @Route("/logout", name="cm_api_security_logout")
     * @param Request $request
     * @param EventDispatcherInterface $dispatcher
     * @param TokenStorageInterface $tokenStorage
     * @return JsonResponse
     */
    public function apiLogout(
        Request $request,
        EventDispatcherInterface $dispatcher,
        TokenStorageInterface $tokenStorage
    ): JsonResponse {
        $logoutEvent = new LogoutEvent($request, $tokenStorage->getToken());
        $dispatcher->dispatch($logoutEvent, LogoutEvent::class);

        return new JsonResponse(['message' => 'User has been logged out successfully']);
    }
}
