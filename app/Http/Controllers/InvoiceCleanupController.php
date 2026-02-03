<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class InvoiceCleanupController extends Controller
{
    public function deleteAll(Request $request)
    {
        // 🔐 Secret key
        $secretKey = 'jsp_delete_pdf';

        if ($request->key !== $secretKey) {
            return response('Invalid key. Access denied.', 403);
        }

        $path = public_path('invoices');

        if (!File::isDirectory($path)) {
            return response('Invoices folder not found.');
        }

        $files = File::glob($path . '/*.pdf');

        if (empty($files)) {
            return response('No PDFs found.');
        }

        $deleted = 0;

        foreach ($files as $file) {
            if (File::exists($file)) {
                File::delete($file);
                $deleted++;
            }
        }

        return response("{$deleted} PDF(s) deleted successfully.");
    }
}
