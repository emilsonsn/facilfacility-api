<?php

namespace App\Http\Controllers;

use App\Services\Component\ComponentService;
use Illuminate\Http\Request;

class ComponentController extends Controller
{
    private $componentService;

    public function __construct(ComponentService $componentService) {
        $this->componentService = $componentService;
    }

    public function search(Request $request){
        $result = $this->componentService->search($request);

        return $result;
    }

    public function getByid(int $id){
        $result = $this->componentService->getById($id);

        return $result;
    }

    public function create(Request $request){
        $result = $this->componentService->create($request);

        if($result['status']) $result['message'] = "Component created successfully";
        return $this->response($result);
    }

    public function update(Request $request, $id){
        $result = $this->componentService->update($request, $id);

        if($result['status']) $result['message'] = "Component updated successfully";
        return $this->response($result);
    }

    public function delete($id){
        $result = $this->componentService->delete($id);

        if($result['status']) $result['message'] = "Component removed successfully";
        return $this->response($result);
    }

    public function deleteImage($id){
        $result = $this->componentService->deleteImage($id);

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
