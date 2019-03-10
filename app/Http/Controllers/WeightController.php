<?php

namespace App\Http\Controllers;

use App\models\Weight;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class WeightController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get the latest data from the DB
        $current_data = Weight::orderby('created_at', 'desc')
                              ->first();

        if (empty($current_data)) {
            return Redirect::to(url('/weight/create'));
        }

        $name = Auth::user()->name;

        $historical_data = $this->historical_data();

        // Work out BMI and description
        $bmi = $this->calculate_bmi($current_data->weight);
        $bmi_desc = $this->bmi_desc($bmi);

        // Work out body fat and description
        $bodyfat = $current_data->bodyfat;
        $bodyfat_desc = $this->bodyfat_desc($bodyfat);

        // Get height and weight in metric
        $weight_in_kg = (float)$current_data->weight;
        $height_in_cm = Auth::user()->height;
        $height_in_m = ($height_in_cm / 100);

        // Calculate height in imperial
        $height_in_in = $height_in_cm / 2.54;
        $feet = floor($height_in_in / 12);
        $inches = $height_in_in % 12;

        // Calculate weight in imperial
        $weight_in_lbs = $weight_in_kg * 2.205;
        $lbs = $weight_in_lbs % 14;
        $stone = floor($weight_in_lbs / 14);

        // Format imperial weight
        $height_imperial = $feet . "'" . $inches . '"';
        $weight_imperial = $stone . "st " . $lbs . 'lbs';

        $chart_data = $this->historical_data();

        $view_data = [
          'name',
          'bmi',
          'bmi_desc',
          'bodyfat',
          'bodyfat_desc',
          'weight_in_kg',
          'height_in_m',
          'weight_imperial',
          'height_imperial',
          'chart_data'
        ];

        return view('weight.list', compact($view_data));
    }

    public function historical_data()
    {
        $user_id = $name = Auth::user()->id;
        $weight_data = Weight::where('user', '=', $user_id)
                             ->orderby('created_at', 'asc')
                             ->get();

        // Work out average daily weight
        $daily_data = [];
        $daily_average = [];
        $weekly_data = [];
        $weekly_average = [];

        foreach ($weight_data as $weight) {
            $date_string = strtotime('today', strtotime($weight->created_at));
            $daily_data[$date_string][] = $weight->weight;
        }

        foreach ($daily_data as $date => $day) {
            $average = round(array_sum($day) / count($day), 1);
            $daily_average[$date] = $average;
        }

        foreach ($daily_average as $date => $weight) {
            $week_string = date('Y-W', $date);
            $weekly_data[$week_string][] = $weight;
        }

        foreach ($weekly_data as $date => $week) {
            $average = round(array_sum($week) / count($week), 1);
            $weekly_average[$date] = $average;
        }

        // Pad out weeks
        $year = (int)date('Y');
        $current_week = (int)date('W');
        $week_range = range(1, $current_week);
        $previous = 0;

        foreach ($week_range as $week_number) {
            $week_number = sprintf("%02u", $week_number);
            if (!empty($weekly_average[$year . '-' . $week_number])) {
                $previous = $weekly_average[$year . '-' . $week_number];
            } else {
                if (!empty($previous)) {
                    $weekly_average[$year . '-' . $week_number] = $previous;
                }
            }
        }


        ksort($weekly_average);

        $chart_data = [];

        foreach ($weekly_average as $week => $weight) {
            list($year, $week_number) = explode('-', $week);
            $actual_date = date("j M y", strtotime($year . "W" . sprintf("%02u", $week_number) . "1"));
            $chart_data[] = "['" . $actual_date . "'," . $weight . ", 65.8]";
        }

        return "[['Date','Weight in Kg', 'Target']," . implode(",", $chart_data) . "]";
    }

    /**
     * @param $bodyfat
     *
     * @return string
     * @author Andrew Haswell
     */

    public function bodyfat_desc($bodyfat)
    {
        if ($bodyfat >= 25) {
            return 'Obese';
        } else if ($bodyfat >= 18) {
            return 'Average';
        } else if ($bodyfat >= 14) {
            return 'Fitness';
        } else if ($bodyfat >= 6) {
            return 'Athletes';
        } else {
            return 'Essential Fat';
        }
    }

    /**
     * @param $bmi
     *
     * @return string
     * @author Andrew Haswell
     */

    public function bmi_desc($bmi)
    {
        if ($bmi >= 30) {
            return 'Obese';
        } else if ($bmi >= 25) {
            return 'Overweight';
        } else if ($bmi >= 18.5) {
            return 'Healthy';
        } else {
            return 'Underweight';
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('weight.create');
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
        // Get the data from the form
        $weight = $request->weight;
        $bodyfat = $request->bodyfat;

        // Get the logged in user
        $user_id = Auth::id();

        // Create a new model
        $weight_model = new Weight();

        // Save the data
        $weight_model->user = $user_id;
        $weight_model->weight = $weight;
        $weight_model->bodyfat = $bodyfat;
        $weight_model->save();

        // Redirect to home page
        return Redirect::to(url('/weight'));
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

    public function calculate_bmi($weight)
    {
        $height_in_cm = Auth::user()->height;
        $height_in_m = $height_in_cm / 100;
        return round($weight / pow($height_in_m, 2), 1);
    }
}
