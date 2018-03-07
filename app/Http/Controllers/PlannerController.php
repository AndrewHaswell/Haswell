<?php

namespace App\Http\Controllers;

use App\Models\MealPlan;
use App\Models\Meals;
use App\owned_ingredients;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;

class PlannerController extends Controller
{

  private $days;
  private $meal_types;

  public function __construct()
  {
    $this->days = ['sunday',
                   'monday',
                   'tuesday',
                   'wednesday',
                   'thursday',
                   'friday',
                   'saturday'];
    $this->meal_types = ['andy breakfast',
                         'tessa breakfast',
                         'snack 1',
                         'andy lunch',
                         'tessa lunch',
                         'snack 2',
                         'dinner',];
    $this->middleware('auth');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $meal_list = Meals::all();

    $meals = [];
    $meals[0] = '-';
    foreach ($meal_list as $meal) {
      $meals[$meal->id] = $meal->name;
    }

    asort($meals);

    $meal_plan = MealPlan::all();

    $set_meals = [];

    foreach ($meal_plan as $plan) {
      $set_meals[$plan->day . '_' . $plan->meal] = $plan->meal_id;
    }

    $meal_types = $this->meal_types;
    $days = $this->days;

    return view('planner.create', compact(['days',
                                           'set_meals',
                                           'meal_types',
                                           'meals']));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $meal_types = [];

    foreach ($this->meal_types as $mt) {
      $meal_types[] = str_replace(' ', '', $mt);
    }

    foreach ($this->days as $days) {
      foreach ($meal_types as $mt) {
        $key = $days . '_' . $mt;
        $this_meal = ['day'  => $days,
                      'meal' => $mt];
        if (!empty($request->$key)) {
          $meal = MealPlan::firstorNew($this_meal);
          $meal->meal_id = $request->$key;
          $meal->save();
        }
      }
    }

    return Redirect::to(url('/planner'));
  }

  public function shopping_list()
  {

    $meal_plan = MealPlan::all();

    $ingredient_list = [];

    foreach ($meal_plan as $plan) {

      $meal = Meals::findOrFail($plan->meal_id);
      $ingredients = $meal->ingredients()->get();

      if (!empty($ingredients)) {

        foreach ($ingredients as $ingredient) {
          $ingredient_list[$ingredient->shop][$ingredient->category][$ingredient->id] = ['id'    => $ingredient->id,
                                                                                         'name'  => $ingredient->name,
                                                                                         'price' => $ingredient->price];
        }
      }
    }

    return view('shopping.shop', compact(['ingredient_list']));
  }

  public function shopping_list_2()
  {

    $meal_plan = MealPlan::all();

    $unwanted_ingredient = owned_ingredients::all();
    $unwanted_ingredient_list = [];

    foreach ($unwanted_ingredient as $this_ingredient) {
      $unwanted_ingredient_list[] = $this_ingredient->ingredient_id;
    }

    $ingredient_list = [];

    foreach ($meal_plan as $plan) {

      $meal = Meals::findOrFail($plan->meal_id);
      $ingredients = $meal->ingredients()->get();

      if (!empty($ingredients)) {

        foreach ($ingredients as $ingredient) {
          if (!in_array($ingredient->id, $unwanted_ingredient_list)) {
            $ingredient_list[$ingredient->shop][$ingredient->category][$ingredient->id] = ['id'    => $ingredient->id,
                                                                                           'name'  => $ingredient->name,
                                                                                           'price' => $ingredient->price];
          }
        }
      }
    }

    owned_ingredients::truncate();

    return view('shopping.check', compact(['ingredient_list']));
  }

  /**
   * Display the specified resource.
   *
   * @param  int $id
   *
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int $id
   *
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request $request
   * @param  int                      $id
   *
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int $id
   *
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    //
  }
}
