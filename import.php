<?php
echo 'hello';
exit('test');
// Step 1: Include Laravel's autoload and configuration
require __DIR__ . '/vendor/autoload.php';  // This ensures Laravel's autoload is loaded for DB and other functionality
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

// Step 2: Specify the path to your CSV file
$csvFilePath = __DIR__ . '/csv_file.csv';  // CSV file is in the same folder as import.php

// Step 3: Check if the file exists
if (!file_exists($csvFilePath)) {
    echo "CSV file not found.";
    exit;
}

// Step 4: Open the CSV file for reading
if (($handle = fopen($csvFilePath, 'r')) !== false) {
    
    // Skip the header row (optional)
    fgetcsv($handle);

    // Begin a database transaction to ensure data integrity
    DB::beginTransaction();

    try {
        // Step 5: Loop through each row of the CSV
        while (($row = fgetcsv($handle)) !== false) {
            // Step 6: Insert data into the database table
            DB::table('your_table_name')->insert([
                'name' => $row[0],
                'email' => $row[1],
                'phone' => $row[2],
                'street' => $row[3],
                'suburb' => $row[4],
                'state' => $row[5],
                'pincod' => $row[6],
                // `created_at` and `updated_at` will be automatically handled by MySQL
            ]);
        }

        // Commit the transaction if no errors occurred
        DB::commit();
        echo "CSV data imported successfully.";

    } catch (Exception $e) {
        // Rollback the transaction in case of any error
        DB::rollBack();

        // Log the error
        Log::error('CSV Import Error: ' . $e->getMessage());

        echo "An error occurred while importing the CSV data.";
    }

    // Close the CSV file handle
    fclose($handle);

} else {
    echo "Failed to open the CSV file.";
}
?>
