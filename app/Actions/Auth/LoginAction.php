<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginAction
{
    /**
     * Verify the credentials of an active user and issue a new API token.
     *
     * @return array{token: string, user: User}
     *
     * @throws ValidationException
     */
    public function execute(string $username, string $password): array
    {
        $user = User::query()->where('username', $username)->first();

        if (! $user || ! $user->is_active || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => 'اسم المستخدم أو كلمة المرور غير صحيحة.',
            ]);
        }

        return [
            'token' => $user->createToken('api')->plainTextToken,
            'user' => $user,
        ];
    }
}
