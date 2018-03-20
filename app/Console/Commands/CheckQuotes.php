<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckQuotes extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'quotes:check';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Looks for short SLAs on currently open quotes';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {
    $client = new Client();
    $response = $client->get(env('QUOTE_URL', '') . '?assignee_id=220&status_ids=2', ['auth' => [env('TICKET_USERNAME', ''),
                                                                                                       env('TICKET_PASSWORD', '')]]);
    $result = json_decode((string)$response->getBody());
    $data = [];
    $times = [];
    $sla = $this->sla_list();

    foreach ($result->quotes as $quote) {

      $status_codes[$quote->status_id] = $quote->status;

      $sla_days = !empty($sla[$quote->account_ref])
        ? $sla[$quote->account_ref]
        : 3;
      $sla_date = Carbon::createFromTimestamp($quote->created_at)->addWeekDays($sla_days);
      $difference = Carbon::now()->diffInHours($sla_date, false);
      $difference = $difference < 0
        ? 0
        : $difference;

      if (!empty($difference)) {
        $times[$quote->id] = ['client'  => $quote->client,
                              'subject' => $quote->subject,
                              'sla'     => $difference];
      }
    }

    if (!empty($times)) {

      $content = '<h3>Quote SLAs</h3>';
      $content .= '<table cellpadding="5" style="border: 1px solid black; border-collapse: collapse"><tr><th style="border: 1px solid black">Quote ID</th><th style="border: 1px solid black">Client</th><th style="border: 1px solid black">Title</th><th style="border: 1px solid black">SLA Hours Left</th></tr>';

      foreach ($times as $quote_id => $time) {
        $content .= '<tr><td style="border: 1px solid black"><a href="https://support.visualsoft.co.uk/bespoke/view/' . $quote_id . '">' . $quote_id . '</a></td><td style="border: 1px solid black">' . $time['client'] . '</td><td style="border: 1px solid black">' . $time['subject'] . '</td><td style="border: 1px solid black">' . $time['sla'] . '</td></tr>';
      }

      $content .= '</table>';

      // Email the db backup
      Mail::send([], [], function ($message) use ($content) {
        $message->from('andy@snowmanx.com', 'SnowmanX');
        $message->to('andrew.haswell@visualsoft.co.uk');
        $message->subject('Outstanding Quote SLAs - ' . date('jS F Y - H:i:s'));
        $message->setBody($content, 'text/html');
      });
    }
  }

  public function sla_list()
  {
    $sla_list = ['COUNTRYH' => 3,
                 'CANTERBU' => 7,
                 'CANTERNZ' => 7,
                 'MITRESPO' => 7,
                 'AIRBORNE' => 7,
                 'BOXFRESH' => 7,
                 'BEAUTYBA' => 3,
                 'CRAFTYAR' => 3,
                 'IMSFLOOR' => 3,
                 'MODAINP'  => 3,
                 'BLUEINC'  => 3];
    return $sla_list;
  }
}
