<?php

namespace App\Http\Controllers\Services;

use Illuminate\Support\Facades\Hash;

class PasswordService
{

    private function validateCurrentPassword($current_password)
    {
        if (!password_verify($current_password, auth()->user()->password)) {
            return response()->json([
                'message' => 'Password did not match the current password'
            ]);
        }
    }

    public function changePassword($data)
    {
        $this->validateCurrentPassword($data);
        $updatePassword = auth()->user()->update([
            'password' => Hash::make($data['password'])
        ]);

        if ($updatePassword) {
            return response()->json([
                'message' => 'Password updated successfully'
            ]);
        } else {
            return response()->json([
                'message' => 'An error occurred while updating the password'
            ]);
        }
    }
}
