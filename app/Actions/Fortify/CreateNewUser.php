<?php

namespace App\Actions\Fortify;

use App\Models\Team;
use App\Models\User;
use App\Models\registration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\Http\Controllers\WEB\usercontroller;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        // Validator::make($input, [
        //     'name' => ['required', 'string', 'max:255'],
        //     'phone' => ['required', 'string', 'max:255', 'unique:users'],
        //     'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        //     'password' => $this->passwordRules(),
        // ])->validate();
        // $otp = rand(1000,9999);
        // $input['otp']= $otp;
        // return DB::transaction(function () use ($input) {
        //     return tap(registration::create([
        //         'fullname' => $input['name'],
        //         'email' => $input['email'],
        //         'phone' => $input['phone'],
        //         'otp' => $input['otp'],
        //         'password' => Hash::make($input['password']),
        //     ]),
        //     // function (User $user) {
        //     //     // $this->createTeam($user);
        //     // }
        //     redirect()->action(
        //                 [usercontroller::class, 'otp'], ['email' => $input['email']]
        //             )
        // );
        // // return redirect()->action(
        // //         [usercontroller::class, 'otp'], ['email' => $input['email']]
        // //     );
        // });
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
        ])->validate();

        return User::create([
            'full_name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'id_role' => '1',
            'password' => Hash::make($input['password']),
        ]);
    }

    /**
     * Create a personal team for the user.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    protected function createTeam(User $user)
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0]."'s Team",
            'personal_team' => true,
        ]));
    }
}
