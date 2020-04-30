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
  <link href="https://fonts.googleapis.com/css2?family=Indie+Flower&display=swap" rel="stylesheet">

  <!-- Styles -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css"
        integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}

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
      @if (!empty(Auth::user()) && Auth::user()->id == 1)
        background-color: #fae4ff;
        font-family:              'Indie Flower';
      @endif
      }

    #upcoming {
      display: none;
      }

    .fa-btn {
      margin-right: 6px;
      }

    .account_month {
      font-weight:             bold;
      color:                   white;
      background-color:        darkred;
      @if (!empty(Auth::user()) && Auth::user()->id == 1)
       background-color: #b949a6;
      @endif
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
<nav class="navbar navbar-default navbar-static-top">
  <div class="container">
    <div class="navbar-header">

      <!-- Collapsed Hamburger -->
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
        <span class="sr-only">Toggle Navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>

      <!-- Branding Image -->
      <a class="navbar-brand" href="{{ url('/') }}">
        SnowmanX
      </a>
    </div>

    <div class="collapse navbar-collapse" id="app-navbar-collapse">
      <!-- Left Side Of Navbar -->
      <ul class="nav navbar-nav">
        <li><a href="{{ url('/home') }}">Home</a></li>
        <li><a href="{{ url('/accounts') }}">Accounts</a></li>
        <li><a href="{{ url('/transactions') }}">Transactions</a></li>

        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
             aria-expanded="false">Payments<span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="{{ url('/payments') }}">Payment List</a></li>
            <li><a href="{{ url('/budget') }}">Budget</a></li>
            <li><a href="{{ url('/categories') }}">Categories</a></li>
          </ul>
        </li>

        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
             aria-expanded="false">Meals<span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="{{ url('/planner') }}">Meal Planner</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="{{ url('/meals/create') }}">Add Meal</a></li>
            <li><a href="{{ url('/meals') }}">Edit Meals</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="{{ url('/ingredients/create') }}">Add Ingredient</a></li>
            <li><a href="{{ url('/ingredients') }}">Edit Ingredients</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="{{ url('/items') }}">Add Item to List</a></li>
            <li><a href="{{ url('/shopping') }}">Shopping List</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="{{ url('/weight') }}">Show Weight</a></li>
            <li><a href="{{ url('/weight/create') }}">Log Weight</a></li>
          </ul>
        </li>

        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
             aria-expanded="false">Todo List<span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="{{ url('/todo') }}">Todo List</a></li>
            <li><a href="{{ url('/todo/create') }}">Add Item</a></li>
          </ul>
        </li>
      </ul>

      <!-- Right Side Of Navbar -->
      <ul class="nav navbar-nav navbar-right">
        <!-- Authentication Links -->
        @if (Auth::guest())
          <li><a href="{{ url('/login') }}">Login</a></li>
          <li><a href="{{ url('/register') }}">Register</a></li>
        @else
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
              {{ Auth::user()->name }} <span class="caret"></span>
            </a>

            <ul class="dropdown-menu" role="menu">
              <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
            </ul>
          </li>
        @endif
      </ul>
    </div>
  </div>
</nav>

@yield('content')

<script>
  $(function () {
    $('#show_upcoming').on('click', function () {
      $('#upcoming').fadeToggle(600);
      if ($(this).text() == 'Show Upcoming') {
        $(this).text('Hide Upcoming');
      } else {
        $(this).text('Show Upcoming');
      }
      return false;
    });

    $('.future_month').on('change', function () {
      var account_id = $(this).attr('id').split('_').pop();
      var month = $(this).val();
      var url = '/future/' + account_id + '/' + month;
      window.location.href = url;
    });

  });
  $("#datepicker").datetimepicker({dateFormat: "yy-mm-dd",  timeFormat: "HH:mm:ss", showSecond:"true" });
</script>

<!-- JavaScripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"
        integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js"
        integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS"
        crossorigin="anonymous"></script>
{{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
</body>
</html>
