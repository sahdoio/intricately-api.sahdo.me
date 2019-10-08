<?php

namespace App\Http\Controllers;

use App\Libs\MongoManager;
use Validator;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Routing\Controller as BaseController;

class AuthController extends BaseController
{
    private $request;
    private $database;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->database = new MongoManager(env('DB_HOST'), env('DB_DATABASE'));
    }

    /**
     * Create a new token.
     *
     * @param  \App\User   $user
     * @return string
     */
    protected function jwt($user)
    {
        $payload = [
            'iss' => "lumen-jwt", // Issuer of the token
            'sub' => $user->email, // Subject of the token
            'iat' => time(), // Time when JWT was issued.
            'exp' => time() + 7*24*60*60 // Expiration time - 7 days
        ];

        // As you can see we are passing `JWT_SECRET` as the second parameter that will
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_SECRET'));
    }

    /**
     * Authenticate a user and return the token if the provided credentials are correct.
     *
     * @param  \App\User   $user
     * @return mixed
     */
    public function authenticate()
    {   
        $this->validate($this->request, [
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        $database = 'admin_users';

        // mongo mode
        $user = $this->database->getDocumentByField(
            'email', 
            $this->request->email, 
            $database
        );

        if (!$user) {
            return response()->json([
                'status' => 'warning',
                'message' => 'No account found for this e-mail'
            ]);
        }

        // Verify the password and generate the token
        if (Hash::check($this->request->input('password'), $user->password)) {
            return response()->json([
                'status' => 'ok',
                'token' => $this->jwt($user)
            ]);
        }

        // Bad Request response
        return response()->json([
            'status' => 'warning',
            'message' => 'Invalid password'
        ]);
    }

    /**
     * Verify auth
     *
     * @param  \App\User   $user
     * @return mixed
     */
    public function verify()
    {
        // if gets here, it is every thing ok
        return [
            'status' => 'ok',
            'message' => "token is ok"
        ];
    }
}