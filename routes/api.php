<?php

use Illuminate\Http\Request;

/**
 * API de usuario
 */
// Retorna el usuario
Route::middleware('auth:api')->get('/user', 'UserController@user');
// Retorna el rol de usuario
Route::middleware('auth:api')->get('/user/role', 'UserController@role');
// Retorna el sistema del usuario
Route::middleware('auth:api')->get('/user/system', 'UserController@system');
// Retorna las rutas del usuario
Route::middleware('auth:api')->get('/user/routes', 'UserController@routes');
// Retorna el usuario con el rol, sus rutas(de url) y su sistema correspondiente
Route::middleware('auth:api')->get('/user/full', 'UserController@fullUser');


/**
 * API de Autenticacion
 */
// Login user
Route::post('/login', 'AuthController@login');
// Logout user
Route::middleware('auth:api')->post('/logout', 'AuthController@logout');


/**
 * API de seguros médicos
 */
// Retorna todos los seguros médicos
Route::middleware('auth:api')->get('/medical_ensurance/index', 'MedicalEnsuranceController@index');


/**
 * API de sistemas
 */
// Retorna todos los seguros sistemas
Route::middleware('auth:api')->get('/system/index', 'SystemController@index');


/**
 * API pacientes
 */
// Retorna todos los pacientes
Route::get('/patient/index', 'PatientController@index');
// Almacena un paciente
Route::middleware('auth:api')->post('/patient/store', 'PatientController@store');


/**
 * API de autenticación con google
 */
// Autorización
Route::get('/authorize/google', 'SocialAuthController@redirectToProvider')->name('api.social.redirect');
// Ruta de callback
Route::get('/authorize/google/callback', 'SocialAuthController@handleProviderCallback')->name('api.social.callback');
