<?php
namespace App\Http\Controllers;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /*public function login(Request $request)
    {
        $request->validate([
            'email'       => 'required|string|email',
            'password'    => 'required|string',
            'remember_me' => 'boolean',
        ]);
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized'], 401);
        }
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse(
                $tokenResult->token->expires_at)
                ->toDateTimeString(),
        ]);
    }*/

    public function signup(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'dni'     => 'required',
                'username'     => 'required|string',
                'name'     => 'required|string',
                'address'     => 'required|string',
                'phone'     => 'required|string',
                'email'    => 'required|string|email|unique:users',
                'password' => 'required|string|confirmed',
            ]);

            $user = new User([
                'dni'     => $request->dni,
                'username'     => $request->username,
                'name'     => $request->name,
                'address'     => $request->address,
                'phone'     => $request->phone,
                'status'     => 1,
                'email'    => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $user->save();

            $success['token'] = $user->createToken('appToken')->accessToken;

            DB::commit();

            return response()->json([
                'success' => true,
                'token' => $success,
                'user' => $user], 201);

        } catch (\Exception $e){
            DB::rollback();
            Log::error('AuthController::signup - ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => $e->getMessage()], 404);
        }
    }

    public function login()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            //$success['token'] = $user->createToken('appToken')->accessToken;
            //After successfull authentication, notice how I return json parameters
            return response()->json([
                'success' => true,
                //'token' => $success,
                'user' => $user
            ]);
        } else {
            //if authentication is unsuccessfull, notice how I return json parameters
            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ], 401);
        }
    }

    public function logout(Request $res)
    {
        if (Auth::user()) {
            $user = Auth::user()->token();
            $user->revoke();

            return response()->json([
                'success' => true,
                'message' => 'Logout successfully'
            ]);
        }else {
            return response()->json([
                'success' => false,
                'message' => 'Unable to Logout'
            ]);
        }
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
