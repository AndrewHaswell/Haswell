<?php

namespace App\Http\Controllers;

use App\Models\MealPlan;
use App\Models\Meals;
use App\Models\ShoppingList;
use App\owned_ingredients;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;

class PlannerController extends Controller
{

  private $days;
  private $meal_types;
  private $ingredient_list;

  public function __construct()
  {
    $this->days = ['sunday',
                   'monday',
                   'tuesday',
                   'wednesday',
                   'thursday',
                   'friday',
                   'saturday'];
    $this->meal_types = ['breakfast',
                         'lunch',
                         'dinner',];
    $this->middleware('auth');
    $this->ingredient_list = [];
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
        } else {
          MealPlan::where('day', $days)->where('meal', $mt)->delete();
        }
      }
    }

    return Redirect::to(url('/planner'));
  }

  public function shopping_list()
  {

    $meal_plan = MealPlan::all();

    foreach ($meal_plan as $plan) {

      $meal = Meals::findOrFail($plan->meal_id);
      $ingredients = $meal->ingredients()->get();
      $this->format_ingredients($ingredients);
    }

    $ingredient_list = $this->ingredient_list;

    return view('shopping.shop', compact(['ingredient_list']));
  }

  public function format_ingredients($ingredients, $unwanted_ingredient_list = [])
  {
    $errors = [];
    if (!empty($ingredients)) {
      foreach ($ingredients as $ingredient) {

        if (in_array($ingredient->id, $unwanted_ingredient_list)) {
          continue;
        }

        if (empty($this->ingredient_list[$ingredient->shop][$ingredient->category][$ingredient->id])) {
          $this->ingredient_list[$ingredient->shop][$ingredient->category][$ingredient->id] = ['id'            => $ingredient->id,
                                                                                               'quantity'      => $ingredient->pivot->quantity,
                                                                                               'unit'          => $ingredient->pivot->unit,
                                                                                               'original_name' => $ingredient->name,
                                                                                               'pack_size'     => $ingredient->pack,
                                                                                               'portion_size'  => $ingredient->portion,
                                                                                               'portion_count' => 1,
                                                                                               'price'         => $ingredient->price];
        } else {

          // Work out if we have a weight or quantity set

          $existing_unit = $this->ingredient_list[$ingredient->shop][$ingredient->category][$ingredient->id]['unit'];
          $this_unit = $ingredient->pivot->unit;

          if ($existing_unit != $this_unit) {

            if ($existing_unit == 'none') {

              // We're working on quantities - need to convert to weight

              // What's the portion size
              $portion_size = (int)$ingredient->portion;
              $current_quantity = (int)$this->ingredient_list[$ingredient->shop][$ingredient->category][$ingredient->id]['quantity'];

              if (!empty($portion_size) && !empty($current_quantity)) {
                $this->ingredient_list[$ingredient->shop][$ingredient->category][$ingredient->id]['quantity'] = ($portion_size * $current_quantity);
                $this->ingredient_list[$ingredient->shop][$ingredient->category][$ingredient->id]['unit'] = 'weight';
              } else {
                $errors[] = $ingredient->name;
              }
              $this->ingredient_list[$ingredient->shop][$ingredient->category][$ingredient->id]['quantity'] += (int)$ingredient->pivot->quantity;
              $this->ingredient_list[$ingredient->shop][$ingredient->category][$ingredient->id]['portion_count']++;
            } else {
              $portion_size = (int)$ingredient->portion;
              if (!empty($portion_size)) {
                $quantity = (int)$ingredient->pivot->quantity;
                $this->ingredient_list[$ingredient->shop][$ingredient->category][$ingredient->id]['quantity'] += (int)($quantity * $portion_size);
                $this->ingredient_list[$ingredient->shop][$ingredient->category][$ingredient->id]['portion_count']++;
              } else {
                $errors[] = $ingredient->name;
              }
            }
          } else {
            $this->ingredient_list[$ingredient->shop][$ingredient->category][$ingredient->id]['quantity'] += $ingredient->pivot->quantity;
            $this->ingredient_list[$ingredient->shop][$ingredient->category][$ingredient->id]['portion_count']++;
          }
        }

        // TODO: Update quantity against pack size
        // So if we have 3 apples and the pack size is 6, we need 6
        // But do we show as 6 or 1 pack??

        $name = $ingredient->name;
        $unit = $this->ingredient_list[$ingredient->shop][$ingredient->category][$ingredient->id]['unit'];
        $qty = $this->ingredient_list[$ingredient->shop][$ingredient->category][$ingredient->id]['quantity'];

        $name .= in_array($unit, ['volume',
                                  'weight']) ?
          ' (' . $qty . 'g)' :
          ' x ' . $qty;

        $this->ingredient_list[$ingredient->shop][$ingredient->category][$ingredient->id]['name'] = $name;
      }
    }

    if (!empty($errors)) {
      dd('No portion sizes set for: ' . implode(',', $errors));
    }
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
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   * @author Andrew Haswell
   */

  public function shopping_list_phone_start()
  {
    $meal_plan = MealPlan::all();

    $unwanted_ingredient = owned_ingredients::all();
    $unwanted_ingredient_list = [];

    foreach ($unwanted_ingredient as $this_ingredient) {
      $unwanted_ingredient_list[] = $this_ingredient->ingredient_id;
    }

    foreach ($meal_plan as $plan) {

      $meal = Meals::findOrFail($plan->meal_id);
      $ingredients = $meal->ingredients()->get();
      $this->format_ingredients($ingredients, $unwanted_ingredient_list);
    }

    $ingredient_list = $this->ingredient_list;

    $shopping_list = [];

    foreach ($ingredient_list as $store => $store_list) {
      foreach ($store_list as $category => $items) {
        foreach ($items as $item) {
          $shopping_list[] = ['id'            => $item['id'],
                              'name'          => $item['name'],
                              'original_name' => $item['original_name'],
                              'price'         => $item['price'],
                              'checked'       => false];
        }
      }
    }
    owned_ingredients::truncate();
    $shopping_list = json_encode($shopping_list);

    $slist = new ShoppingList();
    $slist->list = $shopping_list;
    $slist->save();
    return Redirect::to(url('/shopping_list_phone'));
  }

  /**
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   * @author Andrew Haswell
   */

  public function shopping_list_phone()
  {
    $shopping_list = ShoppingList::orderBy('created_at', 'desc')->first();

    if (!empty($shopping_list)) {

      $shopping_list = $shopping_list->list;

      dump(json_decode($shopping_list));

      return view('shopping.phone_check', compact(['shopping_list']));
    }

    return Redirect::to(url('/shopping'));
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
