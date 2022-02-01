<?php

namespace CoralMedia\Bundle\SecurityBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

/**
 * Class SecurityController
 * @Route("/api/security")
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
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
    ): JsonResponse
    {
        $logoutEvent = new LogoutEvent($request, $tokenStorage->getToken());
        $dispatcher->dispatch($logoutEvent, LogoutEvent::class);

        return new JsonResponse(['message' => 'User has been logged out successfully']);
    }
}
