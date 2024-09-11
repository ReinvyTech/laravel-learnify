<?php

namespace App\Services;

class NameService
{

    public function changeName($name)
    {
        $user = auth()->user();

        $user->name = $name;
        $user->save();

        return response()->json([
            'message' => 'Name updated successfully',
            'data' => $user
        ]);
    }
}
