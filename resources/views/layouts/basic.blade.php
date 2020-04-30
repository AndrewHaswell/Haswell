<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php
    date_default_timezone_set('Europe/London');
    if (empty($title))
        $title = 'SN0WMANX';
    ?>

  <title>{{$title}}</title>


  <!-- JQuery -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

  <!-- Google Charts -->
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

  <!-- Fonts -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css"
        integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">

  <!-- Styles -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css"
        rel="stylesheet"/>
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">

  <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"></script>

  <style>
    body {
      font-family: 'Lato';
      }

    #upcoming {
      display: none;
      }

    .fa-btn {
      margin-right: 6px;
      }

    .account_month {
      font-weight:      bold;
      color:            white;
      background-color: darkred;
      background-color: #b949a6;
      }

    .account_month.future {
      background-color: #007700;
      }

    .unconfirmed {
      color: red;
      }

    .credit_row {
      color: green;
      }

    .current_balance {
      clear:     both;
      float:     left;
      font-size: 12pt;
      }

    .column_headers {
      color:       #444444;
      font-weight: normal;
      font-size:   10pt;
      }

    .edit_box {
      width: 100px;
      }

    .nutrition {
      display: inline;
      }

    .nutrition input {
      width:        30px;
      margin-right: 3px;
      }

    tr.urgent_row td {
      background-color: #8b0516;
      font-weight:      bold;
      color:            white;
      }

    tr.urgent_row td a {
      color: white;
      }

    .nutrition
    .upcoming_link {
      margin-left: 15px;
      float:       right;
      text-align:  right;
      font-size:   12pt;
      }
  </style>
</head>
<body id="app-layout">

@yield('content')

<!-- JavaScripts -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

</body>
</html>
