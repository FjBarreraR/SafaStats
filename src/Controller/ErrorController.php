<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class ErrorController extends AbstractController
{
    public function show(FlattenException $exception, DebugLoggerInterface $logger = null): Response
    {
        // Obtienes el código de error
        $statusCode = $exception->getStatusCode();

        // Puedes decidir qué plantilla renderizar basado en el código
        if ($statusCode === 404) {
            $template = 'error/error404.html.twig';
            $data = ['mensaje' => 'Not found'];
        } elseif ($statusCode === 403) {
            $template = 'error/error403.html.twig';
            $data = ['mensaje' => 'Acceso denegado'];
        } else {
            $template = 'error/error.html.twig';
            $data = ['mensaje' => 'Unexpected error'];
        }

        return $this->render($template, $data);
    }
}
