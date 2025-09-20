<?php 

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AuthService
{

    public function createUser(array $data): User
    {

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);


        return $user;
    }

    public function verifyCredentials(string $email, string $password): ?User
    {
        $user = User::where('email', $email)->first();

        if ($user && Hash::check($password, $user->password)) {
            return $user;
        }

        return null;
    }


}