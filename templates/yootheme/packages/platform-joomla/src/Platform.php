<?php

namespace YOOtheme\Joomla;

use Joomla\CMS\Application\CMSApplication;
use Joomla\Input\Input;
use YOOtheme\Application;
use YOOtheme\Http\Exception;
use YOOtheme\Http\Request;
use YOOtheme\Http\Response;

class Platform
{
    /**
     * Handle application routes.
     */
    public static function handleRoute(Application $app, CMSApplication $joomla, Input $input): void
    {
        if ($input->get('option') !== 'com_ajax' || !$input->get('p')) {
            return;
        }

        $response = null;

        // disable cache
        $joomla->set('caching', 0);

        // default format
        $input->def('format', 'raw');

        // get response
        $joomla->registerEvent('onAfterDispatch', function () use ($app, $input, &$response) {
            // On administrator routes com_login is rendered for guest users
            if ($input->get('option') !== 'com_ajax') {
                return;
            }

            $response = $app->run(false, $input->getRaw('p'));
        });

        // send response
        $joomla->registerEvent('onAfterRender', function () use ($joomla, &$response) {
            if (!$response) {
                return;
            }

            $isHtml = strpos($response->getContentType(), 'html');

            if (!$isHtml) {
                // disable gzip for none html responses like binary images
                $joomla->set('gzip', false);
            }

            $joomla->allowCache(true);
            $joomla->setResponse($isHtml ? $response->write($joomla->getBody()) : $response);
        });
    }

    /**
     * Handle application errors.
     *
     * @param Response  $response (event parameter, not injected)
     * @param \Exception $exception (event parameter, not injected)
     *
     * @throws \Exception
     */
    public static function handleError(Request $request, $response, $exception): ?Response
    {
        if (!($exception instanceof Exception)) {
            throw $exception;
        }

        if (str_starts_with($request->getHeaderLine('Content-Type'), 'application/json')) {
            return $response->withJson($exception->getMessage());
        }

        return $response->write($exception->getMessage())->withHeader('Content-Type', 'text/plain');
    }
}
