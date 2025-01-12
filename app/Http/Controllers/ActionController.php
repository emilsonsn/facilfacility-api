<?php

namespace App\Http\Controllers;

use App\Services\Action\ActionService;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    private $actionService;

    public function __construct(ActionService $actionService) {
        $this->actionService = $actionService;
    }

    public function search(Request $request){
        $result = $this->actionService->search($request);

        return $result;
    }

    public function getByid(int $id){
        $result = $this->actionService->getById($id);

        return $result;
    }

    public function create(Request $request){
        $result = $this->actionService->create($request);

        if($result['status']) $result['message'] = "Action created successfully";
        return $this->response($result);
    }

    public function update(Request $request, $id){
        $result = $this->actionService->update($request, $id);

        if($result['status']) $result['message'] = "Action updated successfully";
        return $this->response($result);
    }

    public function delete($id){
        $result = $this->actionService->delete($id);

        if($result['status']) $result['message'] = "Action removed successfully";
        return $this->response($result);
    }

    public function deleteImage($id){
        $result = $this->actionService->deleteImage($id);

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
