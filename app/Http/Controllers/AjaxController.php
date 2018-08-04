<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ingredients;
use App\Models\ShoppingList;
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

  /**
   * @param $account_id
   *
   * @author Andrew Haswell
   */

  public function get_categories($account_id)
  {
    $categories = Category::orderBy('title')->whereIn('account_id', [0,
                                                                     $account_id])->get();
    $html = ['<option value="0">No Category</option>'];
    foreach ($categories as $category) {
      $html[] = '<option value="' . $category->id . '">' . $category->title . '</option>';
    }
    echo implode("\n", $html);
    exit;
  }

  /**
   * @param Request $request
   *
   * @author Andrew Haswell
   */

  public function save_shopping_list(Request $request)
  {
    $shopping_list = $request->list;
    $saved_list = [];

    foreach ($shopping_list as $row) {


      // dd($row);

      $saved_list[] = ['id'            => (int)$row[0],
                       'name'          => $row[3],
                       'original_name' => $row[1],
                       'price'         => (float)$row[2],
                       'checked'       => (bool)$row[4]];
    }

    $slist = new ShoppingList();
    $slist->list = json_encode($shopping_list);
    $slist->save();

    $response = ['status' => 'OK'];
    echo json_encode($response);
    exit;
  }
}
