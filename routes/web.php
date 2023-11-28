<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CertificateController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});


Route::get('/upload', function () {
    return Inertia::render('FileDashboard');
});


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});




//Route to complete course  (To be auth protected)
Route::post('/complete-course',[CertificateController::class,'completeCourse']);
//Route to check if course is completed
Route::get('/check-course-completion',[CertificateController::class,'checkCourseCompletion']);
//ROute to generate certificate for a user
Route::post('/generate-certificate',[CertificateController::class,'generateCertificate']);
//Route to send certificate to user's email
Route::post('/send-certificate',[CertificateController::class,'sendCertificateByEmail']);
//Route to delete certificates (To be auth protected)
Route::delete('/delete-certificates',[CertificateController::class,'deleteExpiredCertificates']);

require __DIR__.'/auth.php';
