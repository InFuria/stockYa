<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Client as OClient;

class AuthController extends Controller
{

    public function signup(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'dni' => 'required',
                'username' => 'required|string',
                'name' => 'required|string|unique:users',
                'address' => 'required|string',
                'phone' => 'required|string',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|confirmed',
            ]);

            $user = new User([
                'dni' => $request->dni,
                'username' => $request->username,
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'status' => 1,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->save();
            DB::commit();

            $oClient = OClient::where('password_client', 1)->first();
            $response = $this->getToken($oClient, request('username'), request('password'));

            return response()->json([
                'success' => true,
                'token_type' => $response["token_type"],
                'access_token' => $response["access_token"],
                'expires' => now()->addHours(24)->format('Y-m-d H:i:s'),
                'user' => $user], 201);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('AuthController::signup - ' . $e->getMessage());
            if ($e->getMessage() == "The given data was invalid.")
                return response()->json(['origin' => 'AuthController::signup', 'message' => 'Los datos ingresados no son validos.']);
            return response()->json(['origin' => 'AuthController::signup', 'message' => $e->getMessage()], 400);
        }
    }

    public function login()
    {
        try {
            if (Auth::attempt(['username' => request('username'), 'password' => request('password')])) {
                $user = Auth::user();
                $oClient = OClient::where('password_client', 1)->first();

                $auth = $this->authorize('isAdmin', $user);
                $type = $auth == true ? 'personal' : '';

                $response = $this->getToken($oClient, request('username'), request('password'), $type);

                if ($auth && $user->token == null)
                    $user->token = $response['personal_token'];

                if ($auth && $user->token != null)
                    $response['personal_token'] = $user->token;

                $user->save();

                Auth::login($user);

                // Response para usuarios con token personal
                if (isset($user->token))
                    return response()->json([
                        'success' => true,
                        'token_type' => $response["token_type"],
                        'user' => $user
                    ]);

                // Response para clientes con token temporal
                return response()->json([
                    'success' => true,
                    'token_type' => $response["token_type"],
                    'access_token' => $response["access_token"],
                    'expires_in' => now()->addHours(1)->format('Y-m-d H:i:s'),
                    'user' => $user
                ]);
            } else {
                return response()->json(['error' => 'Error al autenticar los datos de usuario.'], 401);
            }
        } catch (\Exception $e) {
            Log::error('AuthController::login - ' . $e->getMessage());
            return response()->json(['origin' => 'AuthController::login', 'message' => $e->getMessage()], 400);
        }
    }

    public function logout()
    {
        try {
            if (Auth::user()) {
                $user = request()->user()->token();
                $user->revoked = true;
                $user->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Se ha cerrado sesion correctamente!'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Cierre de sesion no disponible.'
                ], 401);
            }

        } catch (\Exception $e) {
            Log::error('AuthController::logout - ' . $e->getMessage());
            return response()->json(['origin' => 'AuthController::logout', 'message' => $e->getMessage()], 400);
        }

    }

    public function getToken(OClient $oClient, $username, $password, $type = '')
    {
        if ($type == 'personal') {
            $user = User::where('username', $username)->first();
            $token = $user->createToken('Personal Admin Token')->accessToken;

            return [
                'personal_token' => $token,
                'token_type' => 'Bearer'
            ];
        }

        $http = new Client;
        $response = $http->request('POST', 'http://stockYa.local:92/oauth/token', [
            'headers' => [
                'cache-control' => 'no-cache',
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => $oClient->id,
                'client_secret' => $oClient->secret,
                'username' => $username,
                'password' => $password,
                'scope' => '',
            ],
        ]);

        return json_decode((string)$response->getBody(), true);
    }
}