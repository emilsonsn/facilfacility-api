<?php

namespace App\Services\Action;

use App\Models\Action;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ActionService
{

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $search_term = $request->search_term ?? null;
            $component_id = $request->component_id ?? null;
            $facility_id = $request->facility_id ?? null;
            $type = $request->type ?? null;

            $actions = Action::orderBy('id', 'desc');

            if(isset($search_term)){
                $actions->where('name', 'LIKE', "%{$search_term}%")
                    ->orWhere('description', 'LIKE', "%{$search_term}%");             
            }

            if(isset($type)){
                $actions->where('type', $type);                    
            }

            if(isset($component_id)){
                $actions->where('component_id', $component_id);                    
            }

            if(isset($facility_id)){
                $actions->whereHas('facility_id',function($query) use ($facility_id){
                    $query->where('facility_id',$facility_id);
                });                    
            }

            $actions = $actions->paginate($perPage);

            return $actions;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function getById($id)
    {
        try {
            $action = Action::find($id);

            if(!isset($action)){
                throw new Exception('Action not found', 400);
            }

            return $action;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function create($request)
    {
        try {
            $rules = [
                'component_id' => 'nullable|integer',
                'name' => 'nullable|string|max:255',
                'type' => 'nullable|string|max:255',
                'date' => 'nullable|date',
                'category' => 'nullable|string|max:255',
                'condition' => 'nullable|string|max:255',
                'priority' => 'nullable|string|max:255',
                'frequency' => 'nullable|string|max:255',
                'coast' => 'nullable|string|max:255',
                'curracy' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
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
            
            $action = Action::create($validatedData);
            
            return ['status' => true, 'data' => $action];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $user_id)
    {
        try {
            $rules = [
                'component_id' => 'nullable|integer',
                'name' => 'nullable|string|max:255',
                'type' => 'nullable|string|max:255',
                'date' => 'nullable|date',
                'category' => 'nullable|string|max:255',
                'condition' => 'nullable|string|max:255',
                'priority' => 'nullable|string|max:255',
                'frequency' => 'nullable|string|max:255',
                'coast' => 'nullable|string|max:255',
                'curracy' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $actionToUpdate = Action::find($user_id);

            if(!isset($actionToUpdate)) throw new Exception('Action not found');

            $validatedData = $validator->validated();

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('public/images');
                $validatedData['image'] = str_replace('public/', 'storage/', $path);
            }

            $actionToUpdate->update($validatedData);

            return ['status' => true, 'data' => $actionToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function delete($id){
        try{
            $action = Action::find($id);

            if(!$action) throw new Exception('Action not found');

            $actionName = $action->name;
            
            if ($action->image) {
                $imagePath = str_replace('storage/', 'public/', $action->image);
                Storage::delete($imagePath);
            }
            
            $actionName = $action->name;
            $action->delete();

            return ['status' => true, 'data' => $actionName];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function deleteImage($id){
        try{
            $action = Action::find($id);
    
            if(!$action) throw new Exception('Action not found');
    
            $filePath = explode('storage/', $action->image)[1];
                                
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            $action->image = null;
            $action->save();
            return ['status' => true, 'data' => $action];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}
