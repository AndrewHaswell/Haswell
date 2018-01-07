<?php

namespace App\Http\Controllers;

use App\Models\Ingredients;
use Illuminate\Http\Request;

use App\Http\Requests;

class AjaxController extends Controller
{
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
}
