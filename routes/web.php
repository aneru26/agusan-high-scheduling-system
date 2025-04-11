<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\UserController;


Route::get('/', [AuthController::class,'landing'] );
Route::get('loginfront', [AuthController::class,'login'] );
Route::post('login', [AuthController::class,'Authlogin'] );
Route::get('logout', [AuthController::class,'logout'] );
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'postRegister'])->name('post.register');



  //notification 

  Route::get('/get-notifications', [ScheduleController::class, 'getNotifications']);
  Route::post('/mark-notifications-as-read', [ScheduleController::class, 'markNotificationsAsRead']);

Route::group(['middleware' => 'admin'], function (){

     Route::get('admin/schedules/AllList',[DashboardController::class,'dashboard'] );
     Route::get('admin/schedules/upcoming',[DashboardController::class,'upcoming'] );
     Route::get('admin/schedules/ongoing',[DashboardController::class,'ongoing'] );
     Route::get('admin/schedules/completed',[DashboardController::class,'completed'] );
     Route::get('admin/schedules/declined',[DashboardController::class,'declined'] );
  

    // Route::get('admin/dashboard',[DashboardController::class,'adminDashboard'] );

    //Seller
    Route::get('admin/seller/list',[SellerController::class,'list'] );
    Route::get('admin/seller/add',[SellerController::class,'add'] );
    Route::post('admin/seller/add',[SellerController::class,'insert'] );

    //schedule
    Route::get('admin/schedule/list',[ScheduleController::class,'adminschedulelist'] );
    Route::get('admin/schedule/accept/{id}',[ScheduleController::class, 'accept'] );
    Route::post('admin/schedule/decline/{id}', [ScheduleController::class, 'decline'])->name('schedule.decline');
    Route::get('admin/schedule/delete/{id}',[ScheduleController::class, 'delete'] );    


    //room
    Route::get('admin/room/list',[RoomController::class,'list'] );
    Route::get('admin/room/add',[RoomController::class,'add'] );
    Route::post('admin/room/add',[RoomController::class, 'insert'] );
    Route::get('admin/room/edit/{id}',[RoomController::class, 'edit'] );
    Route::post('admin/room/edit/{id}',[RoomController::class, 'update'] );
    Route::get('admin/room/delete/{id}',[RoomController::class, 'delete'] );

     //Teacher
     Route::get('admin/teacher/list',[UserController::class,'list'] );
     Route::get('admin/teacher/add',[UserController::class,'add'] );
     Route::post('admin/teacher/add',[UserController::class, 'insert'] );
     Route::get('admin/teacher/edit/{id}',[UserController::class, 'edit'] );
     Route::post('admin/teacher/edit/{id}',[UserController::class, 'update'] );
     Route::get('admin/teacher/delete/{id}',[UserController::class, 'delete'] );



    Route::get('admin/account',[UserController::class, 'MyAccount'] );
    Route::post('admin/account',[UserController::class, 'UpdateMyAccount'] );
    

});


Route::group(['middleware' => 'teacher'], function (){

     Route::get('teacher/dashboard',[DashboardController::class,'dashboard'] );


     //schedule
     Route::get('teacher/schedule/list',[ScheduleController::class,'schedulelist'] );
     Route::post('teacher/schedule/store', [ScheduleController::class, 'store'])->name('teacher.schedule.store');
     Route::get('teacher/schedule/delete/{id}',[ScheduleController::class, 'delete'] ); 


     
    Route::get('teacher/account',[UserController::class, 'MyAccount'] );
    Route::post('teacher/account',[UserController::class, 'UpdateMyAccount'] );
     
  

 
 });