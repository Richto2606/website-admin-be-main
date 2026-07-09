<?php

namespace App\Http\Controllers\Api;

use App\Http\Constants\ErrorMessages;
use App\Http\Constants\SuccessMessages;
use App\Http\Responses\ApiResponse;
use App\Models\OriginCampus;
use App\Models\OriginCity;
use App\Models\Resident;
use App\Models\RoomNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ResidentController extends Controller
{

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', null);
        $name = $request->input('name');
        $status = $request->input('status');
        $sortBy = $request->input('sort_by', 'updated_at');

        $maxLimit = 1000;
        $limit = is_numeric($limit) ? min((int)$limit, $maxLimit) : $maxLimit;

        $query = Resident::query();

        if ($name) {
            $query->byName($name);
        }
        if (isset($status)) {
            $query->byStatus($status);
        }

        if (in_array($sortBy, ['name', 'email', 'status', 'room_number', 'created_at', 'updated_at'])) {
            $orderBy = 'desc';
            if ($sortBy == 'name') {
                $orderBy = 'asc';
            }
            $query->orderBy($sortBy, $orderBy);
        }

        $residents = $query->with(['originCampuses', 'roomNumbers', 'originCities'])->paginate($limit);

        foreach ($residents as $resident) {
            if ($resident->originCampuses) {
                $resident->origin_campus = $resident->originCampuses->name;
            }

            if ($resident->roomNumbers) {
                $resident->room_number = $resident->roomNumbers->name;
            }

            if ($resident->originCities) {
                $resident->origin_city = $resident->originCities->name;
            }
        }

        return ApiResponse::pagination(SuccessMessages::SUCCESS_GET_RESIDENT, $residents);
    }

    public function getIndex(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', null);
        $sortBy = $request->input('sort_by', 'updated_at');

        $maxLimit = 1000;
        $limit = is_numeric($limit) ? min((int)$limit, $maxLimit) : $maxLimit;

        $query = Resident::query();

        $query->select('id', 'name', 'room_number_id');

        if (in_array($sortBy, ['name', 'room_number_id'])) {
            $query->orderBy($sortBy, 'asc');
        }

        $residents = $query->with('roomNumbers')->paginate($limit);

        foreach ($residents as $resident) {
            if ($resident->roomNumbers) {
                $resident->room_number = $resident->roomNumbers->name;
                $resident->name = $resident->name . ' - ' . $resident->room_number;
            }
        }

        return ApiResponse::pagination(SuccessMessages::SUCCESS_GET_RESIDENT, $residents);
    }

    // ==============================================
    // 🔥 FUNGSI BARU UNTUK VALIDASI PENDAFTARAN
    // ==============================================

    /**
     * GET resident by user_id
     * Digunakan untuk cek apakah user sudah punya data resident
     */
    public function getByUserId($userId)
    {
        $resident = Resident::where('user_id', $userId)->first();
        
        return ApiResponse::success(SuccessMessages::SUCCESS_GET_RESIDENT, $resident);
    }

    /**
     * POST - Create resident baru dari pendaftaran
     * Digunakan saat admin menerima pendaftaran
     */
    public function storeFromPendaftaran(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'status' => 'required|string|in:Aktif,active'
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), 400);
        }

        try {
            $input = $request->all();

            // Cek apakah user sudah punya resident
            $existingResident = Resident::where('user_id', $input['user_id'])->first();
            if ($existingResident) {
                return ApiResponse::error('User sudah memiliki data resident', 400);
            }

            $resident = Resident::create([
                'user_id' => $input['user_id'],
                'name' => $input['name'],
                'phone_number' => $input['phone_number'] ?? null,
                'address' => $input['address'] ?? '',
                'status' => $input['status'] === 'Aktif' ? 'active' : 'inactive',
                'age' => 0,
                'birth_date' => now(),
                'origin_city_id' => 'city-001-uuid-semarang-jateng',
                'origin_campus_id' => 'campus-001-uuid-undip-smg',
                'room_number_id' => 'room-001-uuid-kamar-a1-asrama',
                'tanggal_masuk' => now()->toDateString()
            ]);

            if (!$resident) {
                return ApiResponse::error(sprintf(ErrorMessages::FAILED_CREATE_MODEL, 'resident'), 404);
            }

            return ApiResponse::success(SuccessMessages::SUCCESS_CREATE_RESIDENT, $resident, 201);
        } catch (\Exception $e) {
            Log::error('Resident creation from pendaftaran failed: ' . $e->getMessage());
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    /**
     * PUT - Update resident dari pendaftaran
     * Digunakan saat admin menerima pendaftaran dan user sudah punya resident
     */
    public function updateFromPendaftaran(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:Aktif,active,inactive',
            'tanggal_masuk' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), 400);
        }

        $resident = Resident::find($id);

        if (!$resident) {
            return ApiResponse::error(sprintf(ErrorMessages::MESSAGE_NOT_FOUND, 'Resident'), 404);
        }

        try {
            $input = $request->only(['name', 'phone_number', 'address', 'status', 'tanggal_masuk']);

            // Convert status jika perlu
            if (isset($input['status']) && $input['status'] === 'Aktif') {
                $input['status'] = 'active';
            }

            // Jika tanggal_masuk tidak diset, gunakan hari ini
            if (!isset($input['tanggal_masuk']) || empty($input['tanggal_masuk'])) {
                $input['tanggal_masuk'] = now()->toDateString();
            }

            $resident->update(array_filter($input, function ($value) {
                return !is_null($value);
            }));

            return ApiResponse::success(SuccessMessages::SUCCESS_UPDATE_RESIDENT, $resident);
        } catch (\Exception $e) {
            Log::error('Resident update from pendaftaran failed: ' . $e->getMessage());
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    // ==============================================
    // FUNGSI YANG SUDAH ADA (TIDAK DIUBAH)
    // ==============================================

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:0|max:150',
            'birth_date' => 'required|date|before:today|date_format:Y-m-d',
            'address' => 'required|string|max:255',
            'origin_city_id' => 'required|exists:origin_cities,id',
            'origin_campus_id' => 'required|exists:origin_campuses,id',
            'phone_number' => 'nullable|string|regex:/^\+?[0-9]{10,15}$/',
            'room_number_id' => 'required|exists:room_numbers,id',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), 400);
        }

        try {
            $input = $request->all();

            $originCampus = OriginCampus::find($input['origin_campus_id']);
            if (!$originCampus) {
                return ApiResponse::error(sprintf(ErrorMessages::MESSAGE_NOT_FOUND, 'Origin Campus'), 400);
            }

            $originCity = OriginCity::find($input['origin_city_id']);
            if (!$originCity) {
                return ApiResponse::error(sprintf(ErrorMessages::MESSAGE_NOT_FOUND, 'Origin City'), 400);
            }

            $category = RoomNumber::find($input['room_number_id']);
            if (!$category) {
                return ApiResponse::error(sprintf(ErrorMessages::MESSAGE_NOT_FOUND, 'Room Number'), 400);
            }

            $resident = Resident::create($input);

            if (!$resident) {
                return ApiResponse::error(sprintf(ErrorMessages::FAILED_CREATE_MODEL, 'resident'), 404);
            }

            return ApiResponse::success(SuccessMessages::SUCCESS_CREATE_RESIDENT, $resident, 201);
        } catch (\Exception $e) {
            Log::error('Resident creation failed: ' . $e->getMessage());

            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    public function show(string $id)
    {
        $resident = Resident::find($id);

        if (!$resident) {
            return ApiResponse::error(sprintf(ErrorMessages::MESSAGE_NOT_FOUND, 'Resident'), 404);
        }

        return ApiResponse::success(SuccessMessages::SUCCESS_GET_RESIDENT, $resident);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:0|max:150',
            'birth_date' => 'nullable|date|before:today',
            'address' => 'nullable|string|max:255',
            'origin_city_id' => 'nullable|exists:origin_cities,id',
            'origin_campus_id' => 'nullable|exists:origin_campuses,id',
            'phone_number' => 'nullable|string|regex:/^\+?[0-9]{10,15}$/',
            'room_number_id' => 'nullable|exists:room_numbers,id',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), 400);
        }

        $resident = Resident::find($id);

        if (!$resident) {
            return ApiResponse::error(sprintf(ErrorMessages::MESSAGE_NOT_FOUND, 'Resident'), 404);
        }

        try {
            $input = $request->only(['name', 'age', 'birth_date', 'address', 'origin_city_id', 'origin_campus_id', 'phone_number', 'room_number_id', 'status']);

            if (isset($input['origin_campus_id']) && $input['origin_campus_id'] !== null) {
                $originCampus = OriginCampus::find($input['origin_campus_id']);
                if (!$originCampus) {
                    return ApiResponse::error(sprintf(ErrorMessages::MESSAGE_NOT_FOUND, 'Origin Campus'), 400);
                }
            } else {
                unset($input['origin_campus_id']);
            }

            if (isset($input['origin_city_id']) && $input['origin_city_id'] !== null) {
                $originCity = OriginCity::find($input['origin_city_id']);
                if (!$originCity) {
                    return ApiResponse::error(sprintf(ErrorMessages::MESSAGE_NOT_FOUND, 'Origin City'), 400);
                }
            } else {
                unset($input['origin_city_id']);
            }

            if (isset($input['room_number_id']) && $input['room_number_id'] !== null) {
                $roomNumber = RoomNumber::find($input['room_number_id']);
                if (!$roomNumber) {
                    return ApiResponse::error(sprintf(ErrorMessages::MESSAGE_NOT_FOUND, 'Room Number'), 400);
                }
            } else {
                unset($input['room_number_id']);
            }

            $resident->update(array_filter($input, function ($value) {
                return !is_null($value);
            }));

            $resident->update($input);

            return ApiResponse::success(SuccessMessages::SUCCESS_UPDATE_RESIDENT, $resident);
        } catch (\Exception $e) {
            Log::error('Resident creation failed: ' . $e->getMessage());

            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $resident = Resident::find($id);

            if (!$resident) {
                return ApiResponse::error(sprintf(ErrorMessages::MESSAGE_NOT_FOUND, 'Resident'), 404);
            }

            $resident->status = 'inactive';
            $resident->save();

            return ApiResponse::success(SuccessMessages::SUCCESS_DELETE_RESIDENT);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }
}