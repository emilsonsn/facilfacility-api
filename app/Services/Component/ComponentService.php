<?php

namespace App\Services\Component;

use App\Models\Component;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ComponentService
{

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $search_term = $request->search_term ?? null;
            $facility_id = $request->facility_id ?? null;

            $components = Component::orderBy('id', 'desc');

            if(isset($search_term)){
                $components->where('name', 'LIKE', "%{$search_term}%")
                    ->orWhere('description', 'LIKE', "%{$search_term}%");             
            }

            if(isset($facility_id)){
                $components->where('facility_id', $facility_id);                    
            }

            $components = $components->paginate($perPage);

            return $components;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function getById($id)
    {
        try {
            $component = Component::find($id);

            if(!isset($component)){
                throw new Exception('Component not found', 400);
            }

            return $component;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function create($request)
    {
        try {
            $rules = [
                'facility_id' => 'nullable|integer',
                'group' => 'nullable|string|max:255',
                'uniformat' => 'nullable|string|max:255',
                'name' => 'nullable|string|max:255',
                'time_left_by_condition' => 'nullable|string|max:255',
                'condition' => 'nullable|string|max:255',
                'year_installed' => 'nullable|string|max:255',
                'quantity' => 'nullable|string|max:255',
                'unity' => 'nullable|string|max:255',
                'time_left_by_lifespan' => 'nullable|string|max:255',
                'coast' => 'nullable|string|max:255',
                'currency' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw new Exception($validator->errors(), 400);
            }

            $validatedData = $validator->validated();

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('public/images');
                $validatedData['image'] = str_replace('public/', 'storage/', $path);
            }
            
            $component = Component::create($validatedData);
            
            return ['status' => true, 'data' => $component];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $user_id)
    {
        try {
            $rules = [
                'facility_id' => 'nullable|integer',
                'group' => 'nullable|string|max:255',
                'uniformat' => 'nullable|string|max:255',
                'name' => 'nullable|string|max:255',
                'time_left_by_condition' => 'nullable|string|max:255',
                'condition' => 'nullable|string|max:255',
                'year_installed' => 'nullable|string|max:255',
                'quantity' => 'nullable|string|max:255',
                'unity' => 'nullable|string|max:255',
                'time_left_by_lifespan' => 'nullable|string|max:255',
                'coast' => 'nullable|string|max:255',
                'currency' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $componentToUpdate = Component::find($user_id);

            if(!isset($componentToUpdate)) throw new Exception('Component not found');

            $validatedData = $validator->validated();

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('public/images');
                $validatedData['image'] = str_replace('public/', 'storage/', $path);
            }

            $componentToUpdate->update($validatedData);

            return ['status' => true, 'data' => $componentToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function delete($id){
        try{
            $component = Component::find($id);

            if(!$component) throw new Exception('Component not found');

            $componentName = $component->name;
            
            if ($component->image) {
                $imagePath = str_replace('storage/', 'public/', $component->image);
                Storage::delete($imagePath);
            }
            
            $componentName = $component->name;
            $component->delete();

            return ['status' => true, 'data' => $componentName];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}
