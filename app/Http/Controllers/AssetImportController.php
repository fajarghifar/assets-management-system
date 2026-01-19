<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Location;
use App\Enums\AssetStatus;
use App\DTOs\AssetData;
use App\Services\AssetService;
use Illuminate\Http\Request;
use OpenSpout\Reader\XLSX\Reader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AssetImportController extends Controller
{
    public function __construct(
        protected AssetService $assetService
    ) {}

    public function create()
    {
        return view('assets.import');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120', // 5MB Max
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        $reader = new Reader();
        try {
            $reader->open($path);
        } catch (\Exception $e) {
            return back()->with('error', 'Could not open file: ' . $e->getMessage());
        }

        $stats = [
            'imported' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        // DB Transaction handled by Service for individual creates, but we might want one big transaction or individual?
        // Usually imports are better as "All or Nothing" or "Best Effort".
        // The Service handles transaction per asset. Let's wrap the whole loop to be safe if requested,
        // BUT strict "All or Nothing" is annoying for large files.
        // I will do "Best Effort" (commit success, log fail) akin to other imports.

        try {
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $rowIndex => $row) {
                    if ($rowIndex === 1) continue; // Skip header

                    $cells = $row->getCells();
                    $data = [];
                    foreach ($cells as $cell) {
                        $data[] = $cell->getValue();
                    }

                    // Expected Columns:
                    // 0: Asset Tag, 1: Product Code, 2: Product Name, 3: Location Code
                    // 4: Location Name, 5: Site, 6: Serial Number, 7: Status, 8: Purchase Date, 9: Notes

                    $assetTag = $data[0] ?? null;
                    $productCode = $data[1] ?? null;
                    $locationCode = $data[3] ?? null;

                    // Skip empty rows if essential data is missing
                    if (empty($assetTag) && (empty($data[1]) || empty($data[3]))) {
                        continue;
                    }

                    // Map Status
                    $statusString = !empty($data[7]) ? $data[7] : 'In Stock';
                    $status = $this->mapStatus($statusString);

                    // Parse Date
                    $purchaseDate = null;
                    if (!empty($data[8])) {
                        if ($data[8] instanceof \DateTime) {
                            $purchaseDate = $data[8];
                        } else {
                            try {
                                $purchaseDate = Carbon::parse($data[8]);
                            } catch (\Exception $e) {
                                $purchaseDate = null;
                            }
                        }
                    }

                    $rowData = [
                        'asset_tag' => $assetTag,
                        'product_code' => $data[1] ?? null,
                        'location_code' => $data[3] ?? null,
                        'serial_number' => $data[6] ?? null,
                        'status' => $status,
                        'purchase_date' => $purchaseDate,
                        'notes' => $data[9] ?? null,
                    ];

                    // Validate
                    $rules = [
                        'serial_number' => ['nullable', 'string'],
                        'status' => ['required', Rule::enum(AssetStatus::class)],
                        'purchase_date' => ['nullable', 'date'],
                        'notes' => ['nullable', 'string'],
                    ];

                    if (empty($rowData['asset_tag'])) {
                        $rules['product_code'] = ['required', 'string'];
                        $rules['location_code'] = ['required', 'string'];
                    }

                    $validator = Validator::make($rowData, $rules);

                    if ($validator->fails()) {
                        $stats['failed']++;
                        $stats['errors'][] = "Row {$rowIndex}: " . implode(', ', $validator->errors()->all());
                        continue;
                    }

                    // Resolve Product and Location
                    $product = null;
                    if (!empty($rowData['product_code'])) {
                        $product = Product::where('code', $rowData['product_code'])->first();
                        if (!$product) {
                            $stats['failed']++;
                            $stats['errors'][] = "Row {$rowIndex}: Product code '{$rowData['product_code']}' not found.";
                            continue;
                        }
                    }

                    $location = null;
                    if (!empty($rowData['location_code'])) {
                        $location = Location::where('code', $rowData['location_code'])->first();
                        if (!$location) {
                            $stats['failed']++;
                            $stats['errors'][] = "Row {$rowIndex}: Location code '{$rowData['location_code']}' not found.";
                            continue;
                        }
                    }

                    try {
                        if (!empty($rowData['asset_tag'])) {
                            // UPDATE MATCHING ASSET
                            $asset = \App\Models\Asset::where('asset_tag', $rowData['asset_tag'])->first();

                            if ($asset) {
                                // Prepare data for update
                                // Use current values if Excel columns are empty/null? Or overwrite?
                                // Usually overwrite if column is present.

                                // DTO expects IDs. Use existing if not provided.
                                $productId = $product ? $product->id : $asset->product_id;
                                $locationId = $location ? $location->id : $asset->location_id;

                                $assetData = new AssetData(
                                    product_id: $productId,
                                    location_id: $locationId,
                                    serial_number: $rowData['serial_number'] ?? $asset->serial_number,
                                    status: $rowData['status'], // Enum is resolved, defaults to InStock
                                    purchase_date: $rowData['purchase_date'] ? Carbon::instance($rowData['purchase_date']) : ($asset->purchase_date ? Carbon::parse($asset->purchase_date)->format('Y-m-d') : null),
                                    notes: $rowData['notes'] ?? $asset->notes,
                                    asset_tag: $asset->asset_tag, // Keep same tag
                                    image_path: $asset->image_path,
                                    history_notes: "Bulk Import Update"
                                );

                                $this->assetService->updateAsset($asset, $assetData);
                                $stats['imported']++; // Count as success (maybe track updated separately?)
                            } else {
                                $stats['failed']++;
                                $stats['errors'][] = "Row {$rowIndex}: Asset Tag '{$rowData['asset_tag']}' not found for update.";
                            }

                        } else {
                            // CREATE NEW ASSET
                            if (!$product || !$location) {
                                // Should be caught by validation or earlier checks, but being safe
                                $stats['failed']++;
                                $stats['errors'][] = "Row {$rowIndex}: Product and Location required for new assets.";
                                continue;
                            }

                            $assetData = new AssetData(
                                product_id: $product->id,
                                location_id: $location->id,
                                serial_number: $rowData['serial_number'],
                                status: $rowData['status'],
                                purchase_date: $rowData['purchase_date'] ? Carbon::instance($rowData['purchase_date']) : null,
                                notes: $rowData['notes'],
                                asset_tag: null, // Auto-generated
                                image_path: null,
                                history_notes: "Initial Import"
                            );

                            $this->assetService->createAsset($assetData);
                            $stats['imported']++;
                        }

                    } catch (\Exception $e) {
                        $stats['failed']++;
                        $stats['errors'][] = "Row {$rowIndex}: Action failed - " . $e->getMessage();
                    }
                }
                break;
            }

            $reader->close();

            $message = "Import completed. Imported: {$stats['imported']}, Failed: {$stats['failed']}.";
            if (!empty($stats['errors'])) {
                Log::warning('Asset Import Errors', $stats['errors']);
                if (count($stats['errors']) > 0) {
                    $message .= " First error: " . $stats['errors'][0];
                }
            }

            return redirect()->route('assets.index')->with('success', $message);

        } catch (\Exception $e) {
            $reader->close();
            Log::error('Asset Import Failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    private function mapStatus( mixed $status): AssetStatus
    {
        if ($status instanceof AssetStatus) {
            return $status;
        }

        $string = strtolower((string)$status);

        // Map common variations
        return match ($string) {
            'loaned', 'loan' => AssetStatus::Loaned,
            'installed' => AssetStatus::Installed,
            'maintenance', 'under maintenance' => AssetStatus::Maintenance,
            'broken', 'damaged' => AssetStatus::Broken,
            'lost', 'missing' => AssetStatus::Lost,
            'disposed' => AssetStatus::Disposed,
            default => AssetStatus::InStock, // Default to InStock
        };
    }
}
