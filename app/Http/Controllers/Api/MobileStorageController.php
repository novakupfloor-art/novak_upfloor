<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MobileStorageController extends Controller
{
    /**
     * Create or Update storage record
     * Jika user sudah punya storage dengan token yang sama, maka update
     * Jika belum, maka create baru
     */
    public function createOrUpdate(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'id_user' => 'required|integer',
                'token' => 'required|string|max:250',
                'device_info' => 'required|string|max:250',
                'ip_address' => 'required|string|max:250',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 400);
            }

            $idUser = $request->id_user;
            $token = $request->token;
            $deviceInfo = $request->device_info;
            $ipAddress = $request->ip_address;
            $date = now();

            // Cek apakah user sudah punya storage dengan token ini
            $existingStorage = DB::table('storages')
                ->where('id_user', $idUser)
                ->where('token', $token)
                ->first();

            if ($existingStorage) {
                // Update existing storage
                DB::table('storages')
                    ->where('id_storages', $existingStorage->id_storages)
                    ->update([
                        'device_info' => $deviceInfo,
                        'ip_address' => $ipAddress,
                        'date' => $date,
                    ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Storage berhasil diupdate',
                    'data' => [
                        'id_storages' => $existingStorage->id_storages,
                        'id_user' => $idUser,
                        'token' => $token,
                        'device_info' => $deviceInfo,
                        'ip_address' => $ipAddress,
                        'date' => $date->format('Y-m-d H:i:s'),
                    ]
                ], 200);
            } else {
                // Create new storage
                $idStorages = DB::table('storages')->insertGetId([
                    'id_user' => $idUser,
                    'token' => $token,
                    'device_info' => $deviceInfo,
                    'ip_address' => $ipAddress,
                    'date' => $date,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Storage berhasil dibuat',
                    'data' => [
                        'id_storages' => $idStorages,
                        'id_user' => $idUser,
                        'token' => $token,
                        'device_info' => $deviceInfo,
                        'ip_address' => $ipAddress,
                        'date' => $date->format('Y-m-d H:i:s'),
                    ]
                ], 201);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete storage by token (untuk logout)
     */
    public function deleteByToken(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'token' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 400);
            }

            $token = $request->token;

            // Hapus storage dengan token ini
            $deleted = DB::table('storages')
                ->where('token', $token)
                ->delete();

            if ($deleted > 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Storage berhasil dihapus'
                ], 200);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Storage tidak ditemukan (sudah terhapus)'
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all storages by id_user
     */
    public function getByUserId($userId)
    {
        try {
            $storages = DB::table('storages')
                ->where('id_user', $userId)
                ->orderBy('date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data storage berhasil diambil',
                'data' => $storages
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
