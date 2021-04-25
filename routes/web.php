<?php

use App\Events\sendmes;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatController;
use Illuminate\Console\Scheduling\Event;
use App\Http\Controllers\AccessTokenController;
use function PHPUnit\Framework\returnSelf;

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

Route::get('/',[UserController::class,'login'])->middleware("Login");
Route::post('/createUser', [UserController::class,'createUser'])->name('auth.createUser');
Route::post('/doLogin', [UserController::class,'doLogin'])->name('auth.doLogin');
Route::get('/chat',[ChatController::class,'index'])->middleware("isLogin");
Route::get('/logout', [ChatController::class,'logout'])->middleware("isLogin");
Route::get('/cn',[ChatController::class,'te']);
Route::post('/sendMes',function(){
    $s=request()->data;
    $chanel=request()->chanel;
    event(new sendmes($s,$chanel));
    return redirect("cn");
 });

Route::post('/updateUser', [UserController::class,'updateUser'])->name('updateDataU');
Route::get('/getrecommend',[UserController::class,'RecomentUser']);
Route::post('/auth', [UserController::class,'auth']);
Route::post('/searchFriend', [UserController::class,'searchFriend']);
Route::post('/filetrans', [ChatController::class,'filetrans'])->middleware("isLogin");
Route::post('/addFriend', [UserController::class,'addFriend']);
Route::get('/getFriendRequest',[UserController::class,'getFriendRequest']);
Route::post('sendMes', [ChatController::class,'sendMes']);
Route::post('answerFriendRequest', [UserController::class,'answerFriendRequest']);
Route::get('/loadMessage',[ChatController::class,'loadMessage']);
Route::get('access_token', [AccessTokenController::class,'generate_token']);