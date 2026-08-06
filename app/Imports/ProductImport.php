<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Carbon;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductImport implements ToModel
{
    protected static $downloadedImages = [];
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */



    // public function model(array $row)
    // {
    //     if (strtolower($row[0]) == 'product_name') {
    //         return null;
    //     }

    //     $adminId = Auth::id();

    //     // 🔁 Check for duplicate product_code
    //     $productCode = trim($row[3]);
    //     if (empty($productCode)) {

    //         $productCode = '_oreva' . now()->format('YmdHis') . Str::random(3);
    //     }

    //     if (Product::where('product_code', $productCode)->exists()) {
    //         Log::info("Skipped duplicate product_code: " . $productCode);
    //         return null;
    //     }

    //     // 🔹 Find or create category
    //     $category = Category::firstOrCreate(
    //         ['category_name' => trim($row[1])],
    //         ['admin_user_id' => $adminId]
    //     );

    //     // 🔹 Handle image
    //     $imageUrl = trim($row[6]);
    //     $finalImageName = null;

    //     if (!empty($imageUrl)) {
    //         try {
    //             // Remove query string from URL
    //             $parsedUrl = parse_url($imageUrl);
    //             $path = $parsedUrl['path'] ?? '';
    //             $originalFilename = basename($path); // e.g. product.webp
    //             $extension = pathinfo($originalFilename, PATHINFO_EXTENSION) ?: 'jpg';

    //             // Generate consistent image hash key for tracking
    //             $imageHash = md5($path); // or full URL for better uniqueness

    //             if (isset(self::$downloadedImages[$imageHash])) {
    //                 $finalImageName = self::$downloadedImages[$imageHash];
    //             } else {
    //                 $finalImageName = Str::uuid() . '.' . $extension;
    //                 $imagePath = public_path('images/products');

    //                 if (!file_exists($imagePath)) {
    //                     mkdir($imagePath, 0755, true);
    //                 }

    //                 // Download image
    //                 $response = Http::timeout(10)->get($imageUrl);
    //                 if ($response->ok()) {
    //                     file_put_contents($imagePath . '/' . $finalImageName, $response->body());
    //                     self::$downloadedImages[$imageHash] = $finalImageName; // cache it
    //                 } else {
    //                     Log::warning("Image fetch failed: $imageUrl");
    //                 }
    //             }
    //         } catch (\Exception $e) {
    //             Log::error("Image download failed from URL: $imageUrl | Error: " . $e->getMessage());
    //         }
    //     }

    //     // 🔹 Return Product instance
    //     return new Product([
    //         'admin_user_id'       => $adminId,
    //         'product_name'        => $row[0],
    //         'product_category'    => $category->id,
    //         'product_description' => $row[2],
    //         'product_code'        => $productCode,
    //         'product_stock'       => $row[4],
    //         'in_stock'            => $row[5] ?? 1,
    //         'product_image'       => $finalImageName,
    //         'delete_status'       => '1',
    //         'created_at'          => now(),
    //         'updated_at'          => now(),
    //     ]);
    // }

    public function model(array $row)
    {
        $productName = isset($row[0]) ? trim((string)$row[0]) : '';

        // Skip header row or empty product name rows
        $cleanHeaderCheck = str_replace([' ', '_'], '', strtolower($productName));
        if ($productName === '' || $cleanHeaderCheck === 'productname') {
            return null;
        }

        $adminId = Auth::id();

        // 🔁 Product code logic
        $inputCode = isset($row[3]) ? trim((string)$row[3]) : '';
        $productCode = null;

        if (!empty($inputCode)) {
            $productCode = $inputCode;
        } else {
            // Get max oreva_XXXX numeric part
            $lastCode = Product::where('product_code', 'like', 'oreva_%')
                ->orderByDesc('id')
                ->value('product_code');

            $lastNumber = 0;

            if ($lastCode && preg_match('/oreva_(\d+)/', $lastCode, $matches)) {
                $lastNumber = intval($matches[1]);
            }

            $productCode = 'oreva_' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        // Check for duplicates
        if (Product::where('product_code', $productCode)->exists()) {
            Log::info("Skipped duplicate product_code: " . $productCode);
            return null;
        }

        // Category creation
        $categoryName = isset($row[1]) && trim((string)$row[1]) !== '' ? trim((string)$row[1]) : 'Uncategorized';
        $category = Category::firstOrCreate(
            ['category_name' => $categoryName],
            ['admin_user_id' => $adminId]
        );

        // Image logic
        $imageUrl = isset($row[6]) ? trim((string)$row[6]) : '';
        $finalImageName = 'placeholder.jpg'; // default placeholder

        if (!empty($imageUrl)) {
            try {
                $parsedUrl = parse_url($imageUrl);
                $path = $parsedUrl['path'] ?? '';
                $originalFilename = basename($path);
                $extension = pathinfo($originalFilename, PATHINFO_EXTENSION) ?: 'jpg';

                $imageHash = md5($path);

                if (isset(self::$downloadedImages[$imageHash])) {
                    $finalImageName = self::$downloadedImages[$imageHash];
                } else {
                    $finalImageName = Str::uuid() . '.' . $extension;
                    $imagePath = public_path('images/products');

                    if (!file_exists($imagePath)) {
                        mkdir($imagePath, 0755, true);
                    }

                    $response = Http::timeout(10)->get($imageUrl);
                    if ($response->ok()) {
                        file_put_contents($imagePath . '/' . $finalImageName, $response->body());
                        self::$downloadedImages[$imageHash] = $finalImageName;
                    } else {
                        Log::warning("Image fetch failed: $imageUrl");
                    }
                }
            } catch (\Exception $e) {
                Log::error("Image download failed from URL: $imageUrl | Error: " . $e->getMessage());
            }
        }

        // 🔹 Final product return
        return new Product([
            'admin_user_id'       => $adminId,
            'product_name'        => $productName,
            'product_category'    => $category->id,
            'product_description' => isset($row[2]) ? trim((string)$row[2]) : null,
            'product_code'        => $productCode,
            'product_stock'       => isset($row[4]) && $row[4] !== '' ? $row[4] : 0,
            'in_stock'            => $row[5] ?? 1,
            'product_image'       => $finalImageName,
            'delete_status'       => '1',
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);
    }
}
