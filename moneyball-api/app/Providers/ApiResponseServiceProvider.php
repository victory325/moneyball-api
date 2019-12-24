<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class ApiResponseServiceProvider extends ServiceProvider
{
    /**
     * Register the application's response macros.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Return a new success API response from the application.
         *
         * @param array|object $payload
         * @param string       $message
         * @param int          $code
         * @param array        $headers
         * @param int          $options
         *
         * @return \Illuminate\Http\JsonResponse
         * @instantiated
         */
        Response::macro('success', function (
            $payload = [],
            string $message = '',
            int $code = 200,
            array $headers = [],
            int $options = 0
        ) {
            /** @var \Illuminate\Support\Facades\Response $this */
            return Response::json(
                [
                    'success' => true,
                    'code'    => $code,
                    'message' => $message,
                    'payload' => $payload,
                ],
                $code,
                $headers,
                $options
            );
        });

        /**
         * Return a new failed API response from the application.
         *
         * @param array  $payload
         * @param string $message
         * @param int    $code
         * @param array  $headers
         * @param int    $options
         *
         * @return \Illuminate\Http\JsonResponse
         * @instantiated
         */
        Response::macro('error', function (
            $payload,
            string $message = '',
            int $code = 500,
            array $headers = [],
            int $options = 0
        ) {
            /** @var \Illuminate\Support\Facades\Response $this */
            return Response::json(
                [
                    'success' => false,
                    'code'    => $code,
                    'message' => $message,
                    'payload' => $payload,
                ],
                $code,
                $headers,
                $options
            );
        });
    }
}