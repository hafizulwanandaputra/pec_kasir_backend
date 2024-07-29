<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class jwt
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header("Authorization");
        $gettoken = null;

        // Extract the token from the header
        if (!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                $gettoken = $matches[1];
            }
        }

        // Check if token is null or empty
        if (is_null($gettoken) || empty($gettoken)) {
            return response('Akses Ditolak', 401);
        }

        try {
            $token = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($token)->toArray();
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            try {
                // Get the refresh token from headers
                $refreshToken = $request->header('X-Refresh-Token');

                if (is_null($refreshToken) || empty($refreshToken)) {
                    $refreshToken = JWTAuth::refresh($token);
                    // return response()->json([
                    //     'status' => 'error',
                    //     'message' => 'Refresh token is missing.',
                    // ], 401);
                }

                $client = new Client();
                $response = $client->post('http://localhost:8081/api/refresh', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $refreshToken,
                        'Accept' => 'application/json',
                    ],
                ]);

                $responseBody = json_decode((string) $response->getBody(), true);
                $newToken = $responseBody['access_token'];
                $newRefreshToken = $responseBody['refresh_token'];

                // Add the new refresh token to the response headers
                $response = $next($request);
                $response->headers->set('Authorization', 'Bearer ' . $newToken);
                $response->headers->set('X-Refresh-Token', $newRefreshToken);
                // JWTAuth::invalidate($token);
                return $response;
            } catch (\Exception $e) {
                // Failed to refresh token
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token tidak bisa di-refresh, silakan login kembali. Error: ' . $e
                ], 401);
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response('Akses Ditolak! Token tidak betul!', 401);
        }

        return $next($request);
    }
}
