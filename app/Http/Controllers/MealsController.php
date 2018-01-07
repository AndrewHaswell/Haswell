<?php

namespace App\Http\Controllers;

use App\Models\Ingredients;
use App\Models\Meals;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;

class MealsController extends Controller
{
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
      $meal->ingredients()->attach($value, ['quantity' => $request->quantity[$key],
                                            'unit'     => $request->unit[$key]]);
    }

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
