<?php

namespace App\Http\Controllers\Api\Product;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    // Menampilkan semua produk
    public function index()
    {
        $products = Product::all();
        return response()->json([
            'status' => 200,
            'message' => 'Products retrieved successfully',
            'data' => $products,
        ], 200);
    }

    // Menambahkan produk baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        try {
            $product = Product::create($validated);

            return response()->json([
                'status' => 201,
                'message' => 'Product created successfully',
                'data' => $product,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to create product',
            ], 500);
        }
    }

    public function show($id)
    {
        $product = Product::find($id);

        if ($product) {
            return response()->json([
                'status' => 200,
                'message' => 'Product retrieved successfully',
                'data' => $product,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Product not found',
            ], 404);
        }
    }

    // Mengupdate produk berdasarkan ID
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 404,
                'message' => 'Product not found',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'string|max:255',
            'price' => 'numeric',
        ]);

        try {
            if ($request->has('name')) {
                $product->name = $validated['name'];
            }
            if ($request->has('price')) {
                $product->price = $validated['price'];
            }

            $product->save();

            return response()->json([
                'status' => 200,
                'message' => 'Product updated successfully',
                'data' => $product,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to update product',
            ], 500);
        }
    }

    // Menghapus produk berdasarkan ID
    public function destroy($id)
    {
        $product = Product::find($id);

        if ($product) {
            $product->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Product deleted successfully',
                'data' => null,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Product not found',
            ], 404);
        }
    }

    // Menambahkan voucher ke produk
    public function attachVoucher(Request $request, $productId)
    {
        $request->validate([
            'voucher_ids' => 'required|array', // Expect an array of voucher IDs
            'voucher_ids.*' => 'exists:vouchers,id', // Validate each voucher ID
        ]);

        try {
            $product = Product::findOrFail($productId);
            
            // Attach multiple voucher IDs to the product
            $product->vouchers()->attach($request->voucher_ids);

            return response()->json([
                'status' => 200,
                'message' => 'Vouchers attached to product successfully',
                'data' => null,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to attach vouchers',
                'error' => $e->getMessage(), // Include error message for debugging
            ], 500);
        }
    }


    // Mendapatkan daftar voucher dari produk
    public function getVouchers($productId)
    {
        try {
            $product = Product::with('vouchers')->findOrFail($productId); // Mengambil produk beserta vouchernya

            return response()->json([
                'status' => 200,
                'message' => 'Vouchers retrieved successfully',
                'data' => [
                    'product' => $product,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to retrieve vouchers',
            ], 500);
        }
    }

}

