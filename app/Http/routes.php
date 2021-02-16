<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
  return view('welcome');
});

//Route::auth(); //Removed to prevent registration

// Authentication Routes...
Route::get('login', 'Auth\AuthController@showLoginForm');
Route::post('login', 'Auth\AuthController@login');
Route::get('logout', 'Auth\AuthController@logout');
Route::get('nutrition', 'MealsController@calculate_meal_nutrition');

// Password Reset Routes...
Route::get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');
Route::post('password/email', 'Auth\PasswordController@sendResetLinkEmail');
Route::post('password/reset', 'Auth\PasswordController@reset');

Route::get('/update', function () {
  Artisan::call('payments:update');
});

Route::get('/home/{limit?}', 'HomeController@index');
Route::get('/accounts/{id}', 'AccountsController@detail');
Route::get('/future/{id}/{month}', 'AccountsController@future');
Route::get('/transactions/{id}', 'TransactionsController@detail');
Route::get('/transactions', 'TransactionsController@index');
Route::get('/categories', 'CategoryController@index');
Route::get('/budget', 'BudgetController@index');
Route::get('/accounts', 'AccountsController@index');
Route::get('/all_accounts', 'AccountsController@all_accounts');
Route::get('/hidden_accounts', 'AccountsController@hidden_accounts');

Route::get('/add_meal', 'MealsController@add_meal');
Route::get('/shopping', 'PlannerController@shopping_list');
Route::get('/old_list', 'PlannerController@shopping_list');
//Route::get('/list', 'PlannerController@shopping_list_phone_start');
Route::get('/list', 'PlannerController@shopping_list_2');
Route::get('/phone_list', 'PlannerController@shopping_list_phone_start');
Route::get('/shopping_list_phone', 'PlannerController@shopping_list_phone');

Route::get('/test', 'PaymentsController@import');

Route::resource('transactions', 'TransactionsController');
Route::resource('category', 'CategoryController');
Route::resource('payments', 'PaymentsController');
Route::resource('schedules', 'SchedulesController');
Route::resource('meals', 'MealsController');
Route::resource('items', 'ItemsController');
Route::resource('ingredients', 'IngredientsController');
Route::resource('todo', 'TodoController');
Route::resource('planner', 'PlannerController');
Route::resource('shop', 'ShoppingController');
Route::resource('budget', 'BudgetController');
Route::resource('weight', 'WeightController');

Route::post('/ajax/update_ingredients', 'AjaxController@update_ingredients');
Route::post('/ajax/update_ingredient_prices', 'AjaxController@update_ingredient_prices');
Route::post('/ajax/update_todo', 'AjaxController@update_todo');
Route::post('/ajax/save_shopping_list', 'AjaxController@save_shopping_list');
Route::get('/ajax/get_categories/{account_id}', 'AjaxController@get_categories');