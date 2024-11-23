<?php

use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/medicines/check/{name}', function ($name) {
    $medicine = Medicine::where('name', $name)->first();
    
    if ($medicine) {
        // Get all medicines with the same name
        $sameMedicines = Medicine::where('name', $name)->get();
        $totalQuantity = $sameMedicines->sum('quantity');
        
        // Get the latest expiration date
        $latestExpDate = $sameMedicines->max('expdate');
        
        $medicine->total_quantity = $totalQuantity;
        $medicine->latest_expdate = $latestExpDate;
        $medicine->existing_id = $medicine->id; // Store the ID for updating
    }
    
    return response()->json([
        'exists' => !!$medicine,
        'medicine' => $medicine
    ]);
});
