<?php

namespace App\Infrastructure\Service\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Twig\Environment;

class AccesDeniedHandler implements AccessDeniedHandlerInterface
{
    /**
     * @var Environment
     */
    private $twigEnvironment;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * AccesDeniedHandler constructor.
     * @param Environment $twigEnvironment
     * @param AuthorizationCheckerInterface $authorizationCheckerInterface
     */
    public function __construct(Environment $twigEnvironment, AuthorizationCheckerInterface $authorizationCheckerInterface)
    {
        $this->twigEnvironment = $twigEnvironment;
        $this->authorizationChecker = $authorizationCheckerInterface;
    }

    /**
     * @inheritDoc
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        $roles = [
        ];
        $template = '_exceptions/error403.html.twig';
        if ($this->authorizationChecker->isGranted($roles)) {
            $template = 'front/_exceptions/error403.html.twig';
            if (preg_match("/^\/administration/", $request->getPathInfo())) {
                $template = 'back/_exceptions/error403.html.twig';
            }
        }

        return new Response($this->twigEnvironment->render($template), 403);
    }
}
