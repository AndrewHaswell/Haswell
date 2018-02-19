<?php

namespace App\Http\Controllers;

use App\Models\Ingredients;
use App\Models\Meals;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;

class MealsController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $meals = Meals::orderBy('name')->get();
    return view('meals.list', compact('meals'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $select = $this->ingredients_select();
    return view('meals.create', compact(['select']));
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
    $meal = $request->update === 'true'
      ? Meals::findOrFail($request->meal_id)
      : new Meals();

    $meal->name = $request->meal_name;
    $meal->portion = $request->meal_portion;
    $meal->save();

    if ($request->update === 'true') {
      // Unlink current ingredients!
      $meal->ingredients()->detach();
    }
    foreach ($request->ingredient as $key => $value) {
      if ($value) {
        $meal->ingredients()->attach($value, ['quantity' => $request->quantity[$key],
                                              'unit'     => $request->unit[$key]]);
      }
    }

    $this->calculate_meal_nutrition();

    return Redirect::to(url('/meals' . ($request->update === 'true'
        ? ''
        : '/create')));
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

  public function calculate_meal_nutrition()
  {
    $meals = Meals::all();

    foreach ($meals as $meal) {

      $nutrition = ['energy'    => 0,
                    'fat'       => 0,
                    'saturates' => 0,
                    'carb'      => 0,
                    'sugars'    => 0,
                    'fibre'     => 0,
                    'protein'   => 0,
                    'salt'      => 0];

      foreach ($meal->ingredients as $ingredient) {

        // Get the ingredient size
        if ($ingredient->pivot->unit == 'none') {
          $size = (float)$ingredient->pivot->quantity * (float)$ingredient->portion;
        } else {
          $size = $ingredient->portion;
        }

        $portion_ratio = $size / 100;

        $nutrition['energy'] += $ingredient->energy * $portion_ratio;
        $nutrition['fat'] = $ingredient->fat * $portion_ratio;
        $nutrition['saturates'] = $ingredient->saturates * $portion_ratio;
        $nutrition['carb'] = $ingredient->carb * $portion_ratio;
        $nutrition['sugars'] = $ingredient->sugars * $portion_ratio;
        $nutrition['fibre'] = $ingredient->fibre * $portion_ratio;
        $nutrition['protein'] = $ingredient->protein * $portion_ratio;
        $nutrition['salt'] = $ingredient->salt * $portion_ratio;
      }

      if ($meal->portion > 1) {
        foreach ($nutrition as &$element) {
          $element = $element / (int)$meal->portion;
        }
      }

      foreach ($nutrition as $nu_key => $nu_value) {
        $meal->$nu_key = $nu_value;
      }

      $meal->save();
    }
  }

  /**
   * @return string
   * @author Andrew Haswell
   */

  protected function ingredients_select()
  {
    $ingredients = Ingredients::all();

    $ingredients_sorted = [];

    foreach ($ingredients as $ingredient) {
      $ingredients_sorted[$ingredient->category][$ingredient->id] = $ingredient->name;
    }
    $ingredients = $ingredients_sorted;
    ksort($ingredients);

    $select = "<option value='0'></option>";

    foreach ($ingredients as $category => $ingredient) {
      asort($ingredient);

      $select .= "<optgroup label='" . $category . "'>" . "\n";

      foreach ($ingredient as $id => $ingredient_name) {
        $select .= "<option value='" . $id . "'>" . $ingredient_name . "</option>" . "\n";
      }

      $select .= "</optgroup>" . "\n";
    }
    return $select;
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

    $meal = Meals::findOrFail($id);
    $ingredients = $meal->ingredients()->get();
    $select = $this->ingredients_select();
    return view('meals.edit', compact(['ingredients',
                                       'meal',
                                       'select']));
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
