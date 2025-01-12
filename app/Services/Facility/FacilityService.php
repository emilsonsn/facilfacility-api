<?php

namespace App\Services\Facility;

use App\Models\Facility;
use App\Models\FacilityImage;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FacilityService
{

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $search_term = $request->search_term ?? null;
            $user_id = $request->user_id ?? null;

            $facilities = Facility::orderBy('id', 'desc')
                ->with('images');

            if(isset($search_term)){
                $facilities->where('name', 'LIKE', "%{$search_term}%")
                    ->orWhere('number', 'LIKE', "%{$search_term}%")               
                    ->orWhere('description', 'LIKE', "%{$search_term}%");
            }

            if(isset($user_id)){
                $facilities->where('user_id', $user_id);                    
            }

            $facilities = $facilities->paginate($perPage);

            return $facilities;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function getById($id)
    {
        try {
            $facility = Facility::with('images')->find($id);

            if(!isset($facility)){
                throw new Exception('Facility not found', 400);
            }

            return $facility;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function create($request)
    {
        try {
            $rules = [
                'name' => 'nullable|string|max:255',
                'user_id' => 'nullable|integer',
                'number' => 'nullable|string|max:255',
                'used' => 'nullable|string|max:255',
                'size' => 'nullable|string|max:255',
                'unity' => 'nullable|string|max:255',
                'report_last_update' => 'nullable|string|max:255',
                'consultant_name' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'region' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'zip_code' => 'nullable|string|max:255',
                'year_installed' => 'nullable|string|max:255',
                'replacement_cost' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'images' => 'nullable|array'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw new Exception($validator->errors(), 400);
            }

            $facility = Facility::create($validator->validated());          

            return ['status' => true, 'data' => $facility];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }


    public function update($request, $user_id)
    {
        try {
            $rules = [
                'name' => 'nullable|string|max:255',
                'user_id' => 'nullable|integer',
                'number' => 'nullable|string|max:255',
                'used' => 'nullable|string|max:255',
                'size' => 'nullable|string|max:255',
                'unity' => 'nullable|string|max:255',
                'report_last_update' => 'nullable|string|max:255',
                'consultant_name' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'region' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'zip_code' => 'nullable|string|max:255',
                'year_installed' => 'nullable|string|max:255',
                'replacement_cost' => 'nullable|string|max:255',
                'description' => 'nullable|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $facilityToUpdate = Facility::find($user_id);

            if(!isset($facilityToUpdate)) throw new Exception('Facility not found');

            $facilityToUpdate->update($validator->validated());

            return ['status' => true, 'data' => $facilityToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function delete($id){
        try{
            $facility = Facility::find($id);

            if(!$facility) throw new Exception('Facility not found');

            $facilityName = $facility->name;
            $facility->delete();

            return ['status' => true, 'data' => $facilityName];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function uploadImage($request, $facility_id){
        try{
            $facility = Facility::find($facility_id);
    
            if(!isset($facility)) throw new Exception("Facility not found");
    
            if (!$request->hasFile('image')) throw new Exception("Image not found");

            $image = $request->file('image');
            $path = $image->store('public/images');

            $facilityImage = FacilityImage::create([
                'filename' => $image->getClientOriginalName(),
                'path' => str_replace('public/', '', $path),
                'facility_id' => $facility->id
            ]);            

            return [
                'status' => true,
                'data' => $facilityImage
            ];
            
        } catch(Exception $error){
            return [
                'status' => false,
                'error' => $error->getMessage(),
               'statusCode' => 400
            ];
        }
    }

    public function deleteImage($id){
        try{
            $facilityImage = FacilityImage::find($id);
    
            if(!$facilityImage) throw new Exception('Image not found');
    
            $facilityImageFilename = $facilityImage->filename;
                
            $filePath = explode('storage/', $facilityImage->path)[1];
                
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
        
            $facilityImage->delete();
    
            return ['status' => true, 'data' => $facilityImageFilename];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

}
