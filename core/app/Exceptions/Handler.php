<?php

namespace App\Exceptions;

use Illuminate\{
    Auth\AuthenticationException,
    Foundation\Exceptions\Handler as ExceptionHandler
};

use Exception;
use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // #region agent log
        if ($exception instanceof NotFoundHttpException) {
            try {
                $payload = [
                    'sessionId' => 'e443e7',
                    'runId' => 'pre-fix',
                    'hypothesisId' => 'H1_H2_H3',
                    'location' => 'app/Exceptions/Handler.php:render',
                    'message' => '404 NotFoundHttpException',
                    'data' => [
                        'method' => $request->method(),
                        'path' => $request->path(),
                        'url' => $request->fullUrl(),
                        'root' => $request->root(),
                        'host' => $request->getHost(),
                        'app_url' => config('app.url'),
                    ],
                    'timestamp' => (int) round(microtime(true) * 1000),
                ];
                @file_put_contents(base_path('debug-e443e7.log'), json_encode($payload) . PHP_EOL, FILE_APPEND);
            } catch (\Throwable $e) {
                // ignore logging failures
            }
        }
        // #endregion agent log

        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->is('admin') || $request->is('admin/*')) {
            return redirect()->guest('/admin/login');
        }
        if ($request->is('user') || $request->is('user/*')) {
            return redirect()->guest('/user/login');
        }
        return redirect()->guest(route('user.login'));
    }

}
