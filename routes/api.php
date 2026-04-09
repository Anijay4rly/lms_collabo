<?php
use App\Http\Controllers\Api\LeaveApiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function (){
    return response()->json([
        'message' => 'leave management system api is working',
        'status' => 'success'
    ], 200);
});

Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function(){

Route::get('/leave', [LeaveApiController::class, 'index']);
Route::post('/leaves', [LeaveApiController::class, 'store']);
Route::get('/leaves/{id}', [LeaveApiController::class, 'show']);
Route::put('/leaves/{id}', [LeaveApiController::class, 'update']);
Route::delete('/leaves/{id}', [LeaveApiController::class, 'destroy']);

});
