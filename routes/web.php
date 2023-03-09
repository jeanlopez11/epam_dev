<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



#RUTAS CON AUTENTIACION USUARIO
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

#RUTAS CON AUTENTIACION DE USUARIOS Y A SU VEZ DEBEN ESTAR VERIFICADAS
Route::group(["middleware" => ['verified', 'auth']], function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    //aca puedes segir colocando las paginas o recursos que quieres cargar mientras en usuario este autenticado y verificado...
    Route::get('posts', [PostController::class,'index'])->name('posts.index');
});

#RUTAS DE AUTENTICACIÓN BREEZE LOGIN, REGISTRO, VALADACIONES CORREO Y CONTRASEÑA
require __DIR__.'/auth.php';










// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');
