<?php

use GuzzleHttp\Exception\ConnectException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function(\Throwable $e,Request $request){
            Log::error($e->getMessage(),[
                'exception'=>$e,
            ]);

            if ($e instanceof ValidationException){
                return response()->json([
                    'message' => 'Validation failed',
                    'error' =>$e->errors(),
                ],422);
            }
            if ($e instanceof AuthenticationException){
                return response()->json([
                    'message' => 'Unauthenticated',
                    'error' =>  $e->getMessage(),
                ],401);
            }
            if ($e instanceof AuthorizationException){
                return response()->json([
                    'message' => 'Forbidden',
                    'error' =>  $e->getMessage(),
                ], 403);
            }
            if ($e instanceof ModelNotFoundException){
                return response()->json([
                    'message' => 'Resource not found',
                    'error' => $e->getMessage(),
                ],404);
            }
            if ($e instanceof QueryException){
                return response()->json([
                    'message' => 'Database error',
                    'error' => $e->getMessage(),
                ],500);
            }
            if ($e instanceof ConnectException){
                return response()->json([
                    'message' => 'External service unavailable',
                    'error' =>  $e->getMessage(),
                ],503);
            }
            if ($e instanceof RuntimeException){
                return response()->json([
                    'message'=>'Service unavaliable',
                    'error' =>  $e->getMessage(),
                ],503);
            }
            $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
            return response()->json([
                'message' => $status === 500 ? 'Internal Server Error' : $e->getMessage(),
                'error' =>  []
            ], $status);
        });
    })->create();
