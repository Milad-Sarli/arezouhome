<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->addresses()->with(['province', 'city'])->get()->map(fn ($a) => [
            'id' => $a->id,
            'province' => $a->province?->name,
            'city' => $a->city?->name,
            'address' => $a->address,
            'postal_code' => $a->postal_code,
            'receiver_name' => $a->receiver_name,
            'receiver_mobile' => $a->receiver_mobile,
            'is_default' => (bool) $a->is_default,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'province_id' => ['required', 'exists:provinces,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'address' => ['required', 'string', 'max:1000'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'receiver_name' => ['required', 'string', 'max:255'],
            'receiver_mobile' => ['required', 'regex:/^09\d{9}$/'],
            'is_default' => ['sometimes', 'boolean'],
        ]);

        $city = City::findOrFail($data['city_id']);

        if ((int) $city->province_id !== (int) $data['province_id']) {
            return response()->json(['message' => 'شهر متعلق به این استان نیست.'], 422);
        }

        $user = $request->user();

        if (! empty($data['is_default'])) {
            $user->addresses()->update(['is_default' => false]);
        }

        $address = $user->addresses()->create($data);

        return response()->json(['id' => $address->id], 201);
    }

    public function destroy(Request $request, int $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);
        $address->delete();

        return response()->json(['message' => 'حذف شد.']);
    }

    public function update(Request $request, int $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);

        $data = $request->validate([
            'province_id' => ['sometimes', 'exists:provinces,id'],
            'city_id' => ['sometimes', 'exists:cities,id'],
            'address' => ['sometimes', 'string', 'max:1000'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'receiver_name' => ['sometimes', 'string', 'max:255'],
            'receiver_mobile' => ['sometimes', 'regex:/^09\d{9}$/'],
            'is_default' => ['sometimes', 'boolean'],
        ]);

        $provinceId = $data['province_id'] ?? $address->province_id;
        $city = City::findOrFail($data['city_id'] ?? $address->city_id);

        if ((int) $city->province_id !== (int) $provinceId) {
            return response()->json(['message' => 'شهر متعلق به این استان نیست.'], 422);
        }

        if (! empty($data['is_default'])) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $address->update($data);

        return response()->json(['message' => 'آدرس به‌روز شد.']);
    }
}
