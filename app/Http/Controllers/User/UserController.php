<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\This;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        return $this->showAll($users);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|min:6|confirmed'
        ];

        $this->validate($request, $rules);

        $data = $request->all();
        $data['password'] = bcrypt($request->password);
        $data['verified'] = User::UNVERIFIED_USER;
        $data['verification_token'] = User::generateVerificationCode();
        $data['admin'] = User::REGULAR_USER;

        $user = User::create($data);

        return response()->json(['data' => $user], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->errorResponse('Does not exist any user with this specified indentificator', 404);
        }

        return $this->showOne($user);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'email' => 'required|unique:users' . $user->id,
            'password' => 'required|min:6|confirmed',
            'admin' => 'in:' . User::ADMIN_UER . ',' . User::REGULAR_USER
        ];

        $this->validate($request, $rules);

        if ($request->has('name')) {

            $user->name = $request->name;
        }

        if ($request->has('email') && $user->email != $request->email) {

            $user->verified = User::UNVERIFIED_USER;
            $user->verification_token = User::generateVerificationCode();
            $user->email = $request->email;
        }

        if ($request->has('password')) {

            $user->password = bcrypt($request->password);
        }

        if ($request->has('admin')) {

            if (!$user->isVerified) {

                $message = 'Only verified users can modify the admin field';

                return $this->errorResponse($message, 409);
            }
        }

        if (!$user->isDirty()) {
            $message = 'You need to specify different value to update';

            return $this->errorResponse($message, 422);
        }

        $user->save();

        return $this->showOne($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return $this->showOne($user);
    }


    public function verify($token)
    {
        $user = User::where('verification_token', $token)->first();
        if (!$user) {
            return response()->json(['message' => 'this account is already verified'], 201);
        }
        $user->verified = User::VERIFIED_USER;
        $user->verification_token = null;
        $user->save();
        return response()->json(['message' => 'the account has ben verified successfully'], 200);
    }
}
