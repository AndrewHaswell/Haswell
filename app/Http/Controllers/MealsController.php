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
    //
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
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

    return view('meals.create', compact(['ingredients',
                                         'select']));
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
    $meal = new Meals();
    $meal->name = $request->meal_name;
    $meal->save();

    foreach ($request->ingredient as $key => $value) {
      $meal->ingredients()->attach($value, ['quantity' => $request->quantity[$key],
                                            'unit'     => $request->unit[$key]]);
    }

    return Redirect::to(url('/meals/create'));
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
