<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MobileControlPanelController extends Controller
{
    /**
     * Get user profile by ID
     */
    public function getProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id_user" => "required|integer",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Data tidak valid",
                "errors" => $validator->errors()
            ], 422);
        }

        $user = DB::table("users")
            ->where("id_user", $request->id_user)
            ->first();

        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "User tidak ditemukan"
            ], 404);
        }

        $staff = DB::table("staff")->where("id_user", $user->id_user)->first();

        $userProfile = [
            "id_user" => (int) $user->id_user,
            "username" => $user->username,
            "nama" => $user->nama,
            "email" => $user->email,
            "akses_level" => $user->akses_level,
            "gambar" => $user->gambar ? asset("assets/upload/staff/" . $user->gambar) : null,
        ];

        $staffProfile = [];
        if ($staff) {
            $staffProfile = [
                "id_staff" => (int) $staff->id_staff,
                "nama_staff" => $staff->nama_staff,
                "telepon_staff" => $staff->telepon_staff,
                "status_staff" => $staff->status_staff,
                "sisa_kuota_iklan" => (int) $staff->sisa_kuota_iklan,
                "total_kuota_iklan" => (int) $staff->total_kuota_iklan,
                "gambar_staff" => $staff->gambar_staff ? asset("assets/upload/staff/" . $staff->gambar_staff) : null,
            ];
        }

        return response()->json([
            "success" => true,
            "message" => "Profil berhasil diambil",
            "data" => [
                "user" => $userProfile,
                "staff" => $staffProfile
            ]
        ], 200);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id_user" => "required|integer",
            "nama" => "nullable|string|max:255",
            "telepon" => "nullable|string|max:20",
            "email" => "nullable|email|unique:users,email," . $request->id_user,
            "gambar" => "nullable|image|mimes:jpeg,png,jpg|max:2048",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Data tidak valid",
                "errors" => $validator->errors()
            ], 422);
        }

        // Update user data
        $updateData = [];
        if ($request->has("nama")) {
            $updateData["nama"] = $request->nama;
        }
        if ($request->has("telepon")) {
            $updateData["telepon"] = $request->telepon;
        }
        if ($request->has("email")) {
            $updateData["email"] = $request->email;
        }

        DB::table("users")
            ->where("id_user", $request->id_user)
            ->update($updateData);

        // Handle image upload if exists
        if ($request->hasFile("gambar")) {
            $file = $request->file("gambar");
            $filename = time() . "." . $file->getClientOriginalExtension();
            $file->move(public_path("assets/upload/staff"), $filename);

            DB::table("users")
                ->where("id_user", $request->id_user)
                ->update(["gambar" => $filename]);
        }

        return response()->json([
            "success" => true,
            "message" => "Profil berhasil diperbarui"
        ], 200);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id_user" => "required|integer",
            "current_password" => "required|string",
            "new_password" => "required|string|min:6",
            "password_confirmation" => "required|string|same:new_password",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Data tidak valid",
                "errors" => $validator->errors()
            ], 422);
        }

        $user = DB::table("users")
            ->where("id_user", $request->id_user)
            ->first();

        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "User tidak ditemukan"
            ], 404);
        }

        // Check current password
        if ($user->password !== sha1($request->current_password)) {
            return response()->json([
                "success" => false,
                "message" => "Password saat ini salah"
            ], 401);
        }

        // Update new password
        DB::table("users")
            ->where("id_user", $request->id_user)
            ->update(["password" => sha1($request->new_password)]);

        return response()->json([
            "success" => true,
            "message" => "Password berhasil diubah"
        ], 200);
    }
}
