<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\UserKeypair;

class E2EEKeyController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'e2ee_enabled' => (bool) $user->e2ee_enabled,
            'pgp_public_key' => $user->pgp_public_key,
            'pgp_private_key_armor' => $user->pgp_private_key_armor,
            'e2ee_pass_wrap' => $user->e2ee_pass_wrap,
            'e2ee_pass_salt' => $user->e2ee_pass_salt,
            'e2ee_rec_wrap' => $user->e2ee_rec_wrap,
            'e2ee_rec_salt' => $user->e2ee_rec_salt,
            'e2ee_kdf_params' => $user->e2ee_kdf_params ? json_decode($user->e2ee_kdf_params, true) : null,
            'e2ee_acc_wrap' => $user->e2ee_acc_wrap,
            'e2ee_acc_salt' => $user->e2ee_acc_salt,
            'key_version' => (int) ($user->key_version ?? 1),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'pgp_public_key' => ['required', 'string'],
            'pgp_private_key_armor' => ['required', 'string'],
            'e2ee_pass_wrap' => ['required', 'string'],
            'e2ee_pass_salt' => ['required', 'string', 'max:255'],
            'e2ee_rec_wrap' => ['required', 'string'],
            'e2ee_rec_salt' => ['required', 'string', 'max:255'],
            'e2ee_kdf_params' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid payload', 'errors' => $validator->errors()], 422);
        }

        $user->pgp_public_key = $request->pgp_public_key;
        $user->pgp_private_key_armor = $request->pgp_private_key_armor;
        $user->e2ee_pass_wrap = $request->e2ee_pass_wrap;
        $user->e2ee_pass_salt = $request->e2ee_pass_salt;
        $user->e2ee_rec_wrap = $request->e2ee_rec_wrap;
        $user->e2ee_rec_salt = $request->e2ee_rec_salt;
        $user->e2ee_kdf_params = $request->e2ee_kdf_params ? json_encode($request->e2ee_kdf_params) : null;
        $user->e2ee_enabled = true;
        $user->key_version = $user->key_version ?? 1;
        $user->save();

        // Upsert keypair record for version 1 (or current)
        UserKeypair::where('users_uuid', $user->uuid)->update(['active' => false]);
        UserKeypair::updateOrCreate(
            ['users_uuid' => $user->uuid, 'version' => (int) $user->key_version],
            [
                'pgp_public_key' => $request->pgp_public_key,
                'pgp_private_key_armor' => $request->pgp_private_key_armor,
                'active' => true,
            ]
        );

        return response()->json(['status' => true]);
    }

    public function setAccountWrap(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'e2ee_acc_wrap' => ['required', 'string'],
            'e2ee_acc_salt' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid payload', 'errors' => $validator->errors()], 422);
        }
        $user->e2ee_acc_wrap = $request->e2ee_acc_wrap;
        $user->e2ee_acc_salt = $request->e2ee_acc_salt;
        $user->save();
        return response()->json(['status' => true]);
    }

    public function rotatePassphrase(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'e2ee_pass_wrap' => ['required', 'string'],
            'e2ee_pass_salt' => ['required', 'string', 'max:255'],
            'e2ee_kdf_params' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid payload', 'errors' => $validator->errors()], 422);
        }

        $user->e2ee_pass_wrap = $request->e2ee_pass_wrap;
        $user->e2ee_pass_salt = $request->e2ee_pass_salt;
        $user->e2ee_kdf_params = $request->e2ee_kdf_params ? json_encode($request->e2ee_kdf_params) : $user->e2ee_kdf_params;
        $user->save();

        return response()->json(['status' => true]);
    }

    public function rotateRecovery(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'e2ee_rec_wrap' => ['required', 'string'],
            'e2ee_rec_salt' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid payload', 'errors' => $validator->errors()], 422);
        }

        $user->e2ee_rec_wrap = $request->e2ee_rec_wrap;
        $user->e2ee_rec_salt = $request->e2ee_rec_salt;
        $user->save();

        return response()->json(['status' => true]);
    }

    public function rotateKeypair(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'pgp_public_key' => ['required', 'string'],
            'pgp_private_key_armor' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid payload', 'errors' => $validator->errors()], 422);
        }

        // bump version
        $newVersion = (int) ($user->key_version ?? 1) + 1;
        $user->key_version = $newVersion;
        $user->pgp_public_key = $request->pgp_public_key;
        $user->pgp_private_key_armor = $request->pgp_private_key_armor;
        $user->save();

        // persist keypair records
        UserKeypair::where('users_uuid', $user->uuid)->update(['active' => false]);
        UserKeypair::create([
            'users_uuid' => $user->uuid,
            'version' => $newVersion,
            'pgp_public_key' => $request->pgp_public_key,
            'pgp_private_key_armor' => $request->pgp_private_key_armor,
            'active' => true,
        ]);

        return response()->json(['status' => true, 'key_version' => $newVersion]);
    }

    public function getKeypair(Request $request, int $version)
    {
        $user = $request->user();
        $kp = UserKeypair::where('users_uuid', $user->uuid)->where('version', $version)->first();
        if (!$kp) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json([
            'version' => $kp->version,
            'pgp_public_key' => $kp->pgp_public_key,
            'pgp_private_key_armor' => $kp->pgp_private_key_armor,
            'active' => (bool) $kp->active,
        ]);
    }
}
