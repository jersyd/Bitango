<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use App\Jobs\SendRegisterSMSJob;


class RegisterController extends Controller
{
    /**
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function store(RegisterRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $user = new User([
                'name' => $request->get('full_name'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
            ]);

            $user->save();

            $user->country()->create([
                'user_id' => $user->id,
                'country' => $request->get('country'),
            ]);

            $user->phone()->create([
                'user_id' => $user->id,
                'phone' => $request->get('phone_number'),
            ]);

            SendRegisterSMSJob::dispatch($user);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $user->toArray()
        ], Response::HTTP_CREATED);
    }
}
