<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;

class TodoController extends Controller
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
    $scheduled_list = Todo::where('complete', '=', '0')->where('scheduled_time', '!=', '0000-00-00 00:00:00')->where('scheduled_time', '<=', date("Y-m-d"))->orderBy('scheduled_time')->orderBy('priority')->get();
    $todo_list = Todo::where('complete', '=', '0')->where('scheduled_time', '=', '0000-00-00 00:00:00')->orderBy('priority')->orderBy('added_time')->get();
    $done_list = Todo::where('complete', '=', '1')->orderBy('priority')->orderBy('updated_at')->limit(10)->get();


    //dd($scheduled_list,$todo_list,$done_list);

    return view('todo.list', compact(['scheduled_list',
                                      'todo_list',
                                      'done_list']));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('todo.create');
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

    $this->validate($request, ['title'    => 'required',
                               'priority' => 'required',
                               'time'     => 'required|numeric']);

    $todo = new Todo();

    $todo->title = $request->title;
    $todo->description = $request->description;
    $todo->time = $request->time;
    $todo->scheduled_time = $request->scheduled_time;
    $todo->priority = $request->priority;
    $todo->added_time = $request->added_time;
    $todo->complete = $request->complete;
    $todo->save();
    return Redirect::to(url('/todo'));
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
