<?php

namespace App\Http\Controllers\Api\Voucher;

use Carbon\Carbon;
use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VoucherController extends Controller
{
    // Menambahkan voucher baru
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:vouchers,code|max:255',
            'expiry_date' => 'required|date|after:today',
        ]);

        $voucher = Voucher::create([
            'code' => $request->code,
            'expiry_date' => $request->expiry_date,
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Voucher created successfully',
            'data' => $voucher,
        ], 201);
    }

    // Mendapatkan semua voucher
    public function index()
    {
        $vouchers = Voucher::all();

        return response()->json([
            'status' => 200,
            'message' => 'Vouchers retrieved successfully',
            'data' => $vouchers,
        ], 200);
    }

    // Mendapatkan voucher berdasarkan ID
    public function show($id)
    {
        $voucher = Voucher::find($id);

        if (!$voucher) {
            return response()->json([
                'status' => 404,
                'message' => 'Voucher not found',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Voucher retrieved successfully',
            'data' => $voucher,
        ], 200);
    }

    // Menggunakan voucher
    public function redeem(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'product_name' => 'required|string', // Change to product name
        ]);

        // Cek apakah voucher masih berlaku
        $voucher = Voucher::where('code', $request->code)
            ->where('expiry_date', '>', Carbon::now())  // Voucher belum kedaluwarsa
            ->where('is_active', true)  // Voucher sudah diaktifkan
            ->first();

        if (!$voucher) {
            return response()->json([
                'status' => 400,
                'message' => 'Voucher is either invalid, expired, or not activated',
                'data' => null,
            ], 400);
        }

        // Cek apakah voucher terhubung dengan produk yang diberikan
        $isAttached = $voucher->products()->where('name', $request->product_name)->exists(); // Use product name for lookup

        if (!$isAttached) {
            return response()->json([
                'status' => 400,
                'message' => 'Please attach the voucher to the specified product first',
                'data' => null,
            ], 400);
        }

        // Proses penggunaan voucher
        // Ubah status voucher menjadi tidak aktif
        $voucher->is_active = false; // Atau bisa juga menggunakan $voucher->is_active = 0;
        $voucher->save(); // Simpan perubahan status

        // Ambil produk terkait dengan voucher
        $product = $voucher->products()->where('name', $request->product_name)->first(); // Mengambil produk terkait

        return response()->json([
            'status' => 200,
            'message' => 'Voucher redeemed successfully',
            'data' => [
                'voucher' => $voucher,
                'product' => $product, // Kembalikan produk terkait
            ],
        ], 200);
    }




    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'is_active' => 'required|boolean',  // Status harus berupa boolean
        ]);

        try {
            $voucher = Voucher::findOrFail($id);

            // Update status aktif
            $voucher->is_active = $request->is_active;
            $voucher->save();

            return response()->json([
                'status' => 200,
                'message' => 'Voucher status updated successfully',
                'data' => $voucher,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to update voucher status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteAll()
    {
        try {
            // Retrieve all vouchers
            $vouchers = Voucher::all();
            
            // Loop through each voucher and delete
            foreach ($vouchers as $voucher) {
                $voucher->delete(); // This will automatically handle related records in product_voucher due to cascading deletes
            }

            return response()->json([
                'status' => 200,
                'message' => 'All vouchers deleted successfully.',
                'data' => null,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to delete all vouchers.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
