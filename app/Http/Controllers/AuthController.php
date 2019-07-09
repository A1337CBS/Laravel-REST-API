<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Auth\Events\Verified;
use Validator;

class AuthController extends Controller
{
   use VerifiesEmails;
   public $successStatus = 200;

   public function register(Request $request)
   {
      $validator = Validator::make($request->all(), [
         'first_name' => ['required', 'string', 'max:255'],
         'last_name' => ['required', 'string', 'max:255'],
         'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
         'password' => ['required', 'string', 'min:8'],
      ]);
      if ($validator->fails()) {
         return response()->json(['error' => $validator->errors()], 422);
      }
      $input = $request->all();
      $input['password'] = bcrypt($input['password']);
      $user = User::create($input);
      $user->sendApiEmailVerificationNotification();
      $success['message'] = 'Please confirm yourself by clicking on verify user button sent to you on your email';
      $success['token'] =  $user->createToken('AppName')->accessToken;
      return response()->json(['success' => $success], $this->successStatus);
   }


   public function login(Request $request)
   {
      $validator = Validator::make($request->all(), [
         'email' => ['required'],
         'password' => ['required', 'min:8'],
      ]);
      if ($validator->fails()) {
         return response()->json(['error' => $validator->errors()], 422);
      }

      if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
         $user = Auth::user();
         $success['token'] =  $user->createToken('AppName')->accessToken;

         if ($user->email_verified_at !== NULL) {
            $success['message'] = "Login successfull";
            return response()->json(['success' => $success], $this->successStatus);
         } else {
            return response()->json(['error' => 'Please Verify Email'], 401);
         }

         //   return response()->json(['success' => $success], $this-> successStatus); 
      } else {
         return response()->json(['error' => 'Unauthorised'], 401);
      }
   }

   public function getUser()
   {
      $user = Auth::user();
      return response()->json(['success' => $user], $this->successStatus);
   }

   public function logout()
   { 
      // Check the currently authenticated user...
      if (Auth::check()) {
         $user = Auth::user()->AauthAccessToken()->delete();
         $response['message'] = 'Your user logged out successfully';
         $response['success'] = $user;
         return response()->json(['success' => $response], $this->successStatus);
      }
   }

}
