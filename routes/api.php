<?php

use App\Http\Controllers\UserController;
use App\Http\Middleware\IsUserToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    Route::post("/logout",[UserController::class,"cerrarSesionUsuario"])->middleware('auth:api')->middleware(IsUserToken::class);
    Route::post("/match",[UserController::class,"crearPartido"])->middleware('auth:api')->middleware(IsUserToken::class);
    Route::get("/match",[UserController::class,"listarPartidosCreados"])->middleware('auth:api')->middleware(IsUserToken::class);
    Route::get("/match/{d}",[UserController::class,"listarPartidoCreado"])->middleware('auth:api')->middleware(IsUserToken::class);
    Route::post("/user",[UserController::class,"registrarUsuario"]);
    Route::prefix('oauth')->group(function () {
        Route::get("/user/data",[UserController::class,"validarTokenUsuario"])->middleware('auth:api');
    });
    /* Route::put('/user',[UserController::class, 'modificarUsuario'])->middleware('auth:api')->middleware(IsUserToken::class); */
});
