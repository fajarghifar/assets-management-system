<?php

namespace App\Services;

use App\Models\Location;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Support\Facades\Log;

class LocationService
{
    /**
     * Create a new location.
     *
     * @param array<string, mixed> $data
     * @return Location
     * @throws Exception
     */
    public function createLocation(array $data): Location
    {
        return DB::transaction(function () use ($data) {
            try {
                return Location::create($data);
            } catch (QueryException $e) {
                Log::error("Database error creating location: " . $e->getMessage());
                throw new Exception("Failed to create location. Database error occurred.");
            } catch (Exception $e) {
                Log::error("Error creating location: " . $e->getMessage());
                throw new Exception("An unexpected error occurred while creating the location.");
            }
        });
    }

    /**
     * Update an existing location.
     *
     * @param Location $location
     * @param array<string, mixed> $data
     * @return Location
     * @throws Exception
     */
    public function updateLocation(Location $location, array $data): Location
    {
        return DB::transaction(function () use ($location, $data) {
            try {
                $location->update($data);
                return $location->refresh();
            } catch (QueryException $e) {
                Log::error("Database error updating location ID {$location->id}: " . $e->getMessage());
                throw new Exception("Failed to update location. Database error occurred.");
            } catch (Exception $e) {
                Log::error("Error updating location ID {$location->id}: " . $e->getMessage());
                throw new Exception("An unexpected error occurred while updating the location.");
            }
        });
    }

    /**
     * Delete a location.
     *
     * @param Location $location
     * @return void
     * @throws Exception
     */
    public function deleteLocation(Location $location): void
    {
        DB::transaction(function () use ($location) {
            try {
                $location->delete();
            } catch (QueryException $e) {
                Log::error("Database error deleting location ID {$location->id}: " . $e->getMessage());
                throw new Exception("Failed to delete location. It might be referenced by other records.");
            } catch (Exception $e) {
                Log::error("Error deleting location ID {$location->id}: " . $e->getMessage());
                throw new Exception("An unexpected error occurred while deleting the location.");
            }
        });
    }

    /**
     * Get all locations.
     *
     * @return Collection<int, Location>
     */
    public function getAllLocations(): Collection
    {
        return Location::orderBy('code', 'asc')->get();
    }
}
