<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class GoogleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }
      
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleGoogleCallback()
    {
        try {
    
            $user = Socialite::driver('google')->stateless()->user();
     
            $finduser = User::where('email', $user->email)->first();
     
            if($finduser){
                if((config('app.app_maintenance') && $finduser->tester) || !config('app.app_maintenance')) {
                    Auth::login($finduser);
                    $token = $finduser->createToken('sample-token-name')->plainTextToken;
                    return redirect( config('app.sanctum_stateful_domains').'/auth/callback?token='.$token);
                } else {
                    return redirect( config('app.sanctum_stateful_domains').'/auth/callback?error=on_maintenance');
                }
     
            }else{
                return redirect( config('app.sanctum_stateful_domains').'/auth/callback?error=not_found');
            }
    
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
    public function user(){
        // return Auth::user()->email;
        $user = User::where('email',Auth::user()->email)->with('roles')->first();
        return response()->json(
            [
             'user' => $user,
            ], 200
         );
    }
    public function login(Request $request)
    { 
        return response()->json(
           [
            'token' => $request->input('token'),
           ], 200
        );
    }

    public function logout(Request $request)
    { 
        auth()->user()->tokens()->delete();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}