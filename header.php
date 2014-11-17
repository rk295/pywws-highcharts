<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>

  <!-- allows removal of the mobile safari window dressing when
       added to the home screen of an iOS app. Also requires
       link.js which is included below -->
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <link rel="apple-touch-icon" href="images/touch-icon.png">

  <!-- Some includes from the cloudflare CDN -->
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js" type="text/javascript"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/highcharts/4.0.4/highcharts.js" type="text/javascript"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/highcharts/4.0.4/modules/data.js"></script>

  <!-- Bootstrap includes -->
  <link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet">
  <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.0/js/bootstrap.min.js"></script>

  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Local JS & CSS includes -->
  <script type="text/javascript" src="includes/chart.min.js" charset="utf-8"></script>
  <script type="text/javascript" src="includes/links.js"></script>

  <style type="text/css">
    body {
      padding-top: 70px;
      }

    .navbar .brand {
      max-height: 40px;
      overflow: visible;
      padding-top: 0;
      padding-bottom: 0;
    }
</style>

<title>Weather</title>

</head>
<body>
  <div id="wrap">
    <div class="navbar navbar-fixed-top navbar-default " role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div><!-- /.navbar-fixed-top -->

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
            <li><a class="brand" href="/"><img src="images/logo.png" width="50" height="50"></a></li>
            <li class="active"><a href="#">Weather</a></li>
          </ul>
        </div><!-- /.navbar-collapse -->
      </div><!-- /.container -->
    </div><!-- /.navbar-fixed-top -->
<div class="container">
