<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\BudgetMain;
use App\Models\BudgetSub;

class PopulateBudgetCategories extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {

    $categories = ['Housing'                  => ['Rent'                         => 0,
                                                  'Mortgage'                     => 428,
                                                  'Secured loans / 2nd mortgage' => 66,
                                                  'Mortgage endowment premium'   => 0,
                                                  'Service charge / ground rent' => 0,
                                                  'Water'                        => 50,
                                                  'Council tax'                  => 99,
                                                  'Gas'                          => 60,
                                                  'Electricity'                  => 60,
                                                  'Other household fuels'        => 0,],
                   'Household Services'       => ['Child maintenance'             => 65,
                                                  'Telephone / mobile / internet' => 98,
                                                  'TV licence'                    => 13,
                                                  'Life insurance / pensions'     => 29,
                                                  'Medical / accident insurance'  => 14,
                                                  'Household appliance rental'    => 0,
                                                  'Childcare'                     => 0,
                                                  'Building / contents insurance' => 12,
                                                  'Fines / CCJs / decrees'        => 0,
                                                  'Satellite'                     => 26,
                                                  'Repairs / maintenance'         => 0,
                                                  'Hire purchase / logbook loan'  => 0,],
                   'Transport'                => ['Spares / servicing' => 30,
                                                  'Road tax'           => 20,
                                                  'Insurance'          => 25,
                                                  'Breakdown cover'    => 5,
                                                  'Fuel / Parking'     => 160,
                                                  'Public transport'   => 140,],
                   'Food and Housekeeping'    => ['Food / toiletries / cleaning' => 360,
                                                  'School meals / meals at work' => 87,
                                                  'Pets / pet food / insurance'  => 152,
                                                  'Tobacco'                      => 0,],
                   'Misc. goods and services' => ['School trips / activities' => 0,
                                                  'Medicine / prescriptions'  => 22,
                                                  'Dentist / opticians'       => 34,
                                                  'Hairdressing'              => 24,
                                                  'Union / professional fees' => 6,
                                                  'Laundry / cleaning	'      => 20,],
                   'Personal and leisure'     => ['Clothing / footwear'              => 60,
                                                  'Newspapers / magazines'           => 20,
                                                  'Sports / hobbies / entertainment' => 60,
                                                  'Children\'s pocket money'         => 0,
                                                  'Religious Contributions'          => 0,],
                   'Sundries and emergencies' => ['Sundries / emergencies'      => 25,
                                                  'Income Tax'                  => 0,
                                                  'National Insurance'          => 0,
                                                  'VAT'                         => 0,
                                                  'School Fees'                 => 40,
                                                  'Loan from Friends or Family' => 23,
                                                  'Other'                       => 0,],];
    foreach ($categories as $main => $subs) {
      $main_category = new BudgetMain();
      $main_category->name = $main;
      $main_category->save();
      $main_category_id = $main_category->id;
      foreach ($subs as $sub => $balance) {
        $sub_category = new BudgetSub();
        $sub_category->name = $sub;
        $sub_category->balance = $balance;
        $sub_category->budget_main_id = $main_category_id;
        $sub_category->save();
      }
    }
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {

    DB::table('budget_subs')->delete();
    DB::table('budget_mains')->delete();
  }
}
