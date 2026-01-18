<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\DTOs\AssetData;
use App\Models\Product;
use App\Models\Location;
use Illuminate\View\View;
use App\Enums\AssetStatus;
use App\Services\AssetService;
use App\Exceptions\AssetException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Assets\StoreAssetRequest;
use App\Http\Requests\Assets\UpdateAssetRequest;

class AssetController extends Controller
{
    public function __construct(
        protected AssetService $assetService
    ) {}

    public function index(): View
    {
        return view('assets.index');
    }

    public function create(): View
    {
        $products = Product::orderBy('name')->get(['id', 'name', 'code']);
        $locations = Location::orderBy('name')->get();

        return view('assets.create', [
            'products' => $products,
            'locations' => $locations,
            'statuses' => AssetStatus::cases(),
        ]);
    }

    public function store(StoreAssetRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image_path')) {
            $data['image_path'] = $request->file('image_path')->store('assets', 'public');
        }

        try {
            $assetData = AssetData::fromArray($data);
            $asset = $this->assetService->createAsset($assetData);

            return redirect()->route('assets.show', ['asset' => $asset->id])
                ->with('success', 'Asset created successfully.');
        } catch (AssetException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function show(Asset $asset): View
    {
        $asset->load(['product', 'location']);

        return view('assets.show', [
            'asset' => $asset,
        ]);
    }

    public function edit(Asset $asset): View
    {
        $products = Product::orderBy('name')->get(['id', 'name', 'code']);
        $locations = Location::orderBy('name')->get();

        return view('assets.edit', [
            'asset' => $asset,
            'products' => $products,
            'locations' => $locations,
            'statuses' => AssetStatus::cases(),
        ]);
    }

    public function update(UpdateAssetRequest $request, Asset $asset): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image_path')) {
            // Delete old image if exists
            if ($asset->image_path) {
                Storage::disk('public')->delete($asset->image_path);
            }
            $data['image_path'] = $request->file('image_path')->store('assets', 'public');
        }

        $data['product_id'] = $data['product_id'] ?? $asset->product_id;
        $data['location_id'] = $data['location_id'] ?? $asset->location_id;

        try {
            $assetData = AssetData::fromArray($data);
            $this->assetService->updateAsset($asset, $assetData);

            return redirect()->route('assets.show', ['asset' => $asset->id])
                ->with('success', 'Asset updated successfully.');
        } catch (AssetException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function destroy(Asset $asset): RedirectResponse
    {
        try {
            if ($asset->image_path) {
                Storage::disk('public')->delete($asset->image_path);
            }

            $this->assetService->deleteAsset($asset);

            return redirect()->route('assets.index')
                ->with('success', 'Asset deleted successfully.');

        } catch (AssetException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete asset. It may have related history records.');
        }
    }
}
