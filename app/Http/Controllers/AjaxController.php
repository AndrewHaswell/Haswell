<?php

namespace App\Http\Controllers;

use App\Models\Ingredients;
use App\Models\Todo;
use Illuminate\Http\Request;

use App\Http\Requests;

class AjaxController extends Controller
{

  public function update_todo(Request $request)
  {
    $id = (string)$request->id;
    $todo = Todo::findOrFail($id);
    $todo->complete = (int)$request->complete;
    $todo->save();
    $response = ['status' => 'OK'];
    echo json_encode($response);
    exit;
  }

  /**
   * @param Request $request
   *
   * @author Andrew Haswell
   */

  public function update_ingredients(Request $request)
  {
    $type = (string)$request->type;
    $id = (string)$request->id;
    $value = (string)$request->value;

    $ingredient = Ingredients::findOrFail($id);
    $ingredient->$type = $value;
    $ingredient->save();

    $response = ['status' => 'OK'];
    echo json_encode($response);
    exit;
  }

  /**
   * @param Request $request
   *
   * @author Andrew Haswell
   */

  public function update_ingredient_prices(Request $request)
  {
    $price = (float)$request->price;
    $id = (string)$request->id;

    $ingredient = Ingredients::findOrFail($id);
    $ingredient->price = $price;
    $ingredient->save();

    $response = ['status' => 'OK'];
    echo json_encode($response);
    exit;
  }
}
