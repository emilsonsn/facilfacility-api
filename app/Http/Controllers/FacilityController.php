<?php

namespace App\Http\Controllers;

use App\Services\Facility\FacilityService;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    private $facilityService;

    public function __construct(FacilityService $facilityService) {
        $this->facilityService = $facilityService;
    }

    public function search(Request $request){
        $result = $this->facilityService->search($request);

        return $result;
    }

    public function getByid(int $id){
        $result = $this->facilityService->getById($id);

        return $result;
    }

    public function create(Request $request){
        $result = $this->facilityService->create($request);

        if($result['status']) $result['message'] = "Facility created successfully";
        return $this->response($result);
    }

    public function update(Request $request, $id){
        $result = $this->facilityService->update($request, $id);

        if($result['status']) $result['message'] = "Facility updated successfully";
        return $this->response($result);
    }

    public function delete($id){
        $result = $this->facilityService->delete($id);

        if($result['status']) $result['message'] = "Facility removed successfully";
        return $this->response($result);
    }

    public function uploadImage(Request $request, $id){
        $result = $this->facilityService->uploadImage($request, $id);

        if($result['status']) $result['message'] = "Image added successfully";
        return $this->response($result);
    }

    public function deleteImage($id){
        $result = $this->facilityService->deleteImage($id);

        if($result['status']) $result['message'] = "Image removed successfully";
        return $this->response($result);
    }

    private function response($result){
        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'] ?? null,
            'data' => $result['data'] ?? null,
            'error' => $result['error'] ?? null
        ], $result['statusCode'] ?? 200);
    }
}
