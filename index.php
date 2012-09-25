<?php 

  require_once 'sdk/facebook.php';

  $facebook = new Facebook(array(
    'appId'  => '104236416398654',
    'secret' => 'SECRET_KEY',
  ));
  $access_token = $facebook->getAccessToken();
  $params = array('access_token' => $access_token);
  $events = array();
  $i = 0;

  $objs = $facebook->api('139968802813349/events', 'GET', $params);

  foreach($objs['data'] as $data) {

      $events[$i]['id'] = $data['id'];
      $events[$i]['name'] = $data['name'];
      $events[$i]['start_time'] = $data['start_time'];
      $events[$i]['location'] = $data['location'];

      $objs2 = $facebook->api($data['id'].'/invited', 'GET', $params);

      foreach ($objs2['data'] as $attendee) {
        if ($attendee['rsvp_status'] == 'attending' || $attendee['rsvp_status'] == 'unsure') {
          $attending[] = array('id'=>$attendee['id'],'name'=>$attendee['name']);
        }
      }

      $events[$i]['attending'] = $attending;
      $i++;

  }

?>
<!--

If you're reading this,
then you should contact me
to help get this thing going...

nathan@nathanwaters.com

-->
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Hackagong</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Hackagong is Wollongong's monthly hackathon competition and host of Startup Weekend Wollongong">
	<meta property="og:title" content="Hackagong"/>
	<meta property="og:url" content="http://hackagong.com/"/>
    <meta property="og:image" content="http://hackagong.com/img/hfb.jpg"/>
    <meta property="og:description" content="Hackagong is Wollongong's monthly hackathon competition and host of Startup Weekend Wollongong"/>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="css/bootstrap-responsive.min.css" />
	<link href="/favicon.ico" type="image/x-icon" rel="icon" />
	<link href="/favicon.ico" type="image/x-icon" rel="shortcut icon" />
    <style>
    html,body {
      height:100%;
    }
    body {
      background-color:#fff9f9;
    }
    .container {
      padding:40px 40px 200px 40px;
      background-color:#fff;
      height:100%;
    }
    .header {
      margin-bottom:40px;
    }
    .header .top {
      text-align:center;
      margin-bottom:20px;
    }
    .header .right {
      text-align:right;
    }
    .header .right img {
    	height:60px;
    	width:60px
    }
    .input-append {
       padding-top: 20px;
    }
    </style>
</head>
<body>
  <div class="container">
    <div class="row header">
      <div class="span12 top">
        <a href="/"><img src="img/hackagong.jpg"></a>
      </div>
      <div class="span6 left">
        <form action="/mail.php" method="post" id="subscribe">
          <div class="input-append">
            <input class="span3" id="email" size="16" type="text" placeholder="Email address"><button class="btn" type="submit">Subscribe to Updates</button>
          </div>
          <p class="signup"></p>
        </form>
      </div>
      <div class="span6 right">
        <a href="https://facebook.com/hackagong"><img src="img/facebook.png"></a>
        <a href="https://twitter.com/hackagong"><img src="img/twitter.png"></a>
      </div>
    </div>
    <?php 
    foreach ($events as $event) {
      echo '<div class="row"><div class="span4">';
      echo '<h2><a href="https://facebook.com/events/'.$event['id'].'">'.$event['name'].'</a></h2>';
      echo '<h4>'.date("j, M Y",strtotime($event['start_time'])).'</h4>';
      echo '</div><div class="span8" style="text-align:right">';

      foreach ($event['attending'] as $attending) {
        echo '<a href="https://facebook.com/'.$attending['id'].'" title="'.$attending['name'].'"><img src="/img/timthumb.php?src=http://graph.facebook.com/'.$attending['id'].'/picture?type=large&w=100&h=100">';
      }
      echo '</div></div>';
    }
    ?>
  </div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js"></script>
<script src="./js/jquery.slabtext.min.js"></script>

<script>
 $(document).ready(function () {

  $('#subscribe').submit(function () {
      var action = $(this).attr('action');
      $.ajax({
          url: action,
          type: 'POST',
          dataType: 'json',
          data: {
              email: $('#email').attr('value')
          },
          cache: false,
          success: function (data) {
              if (data.error) {
                  $('.signup').html(data.error).css('color', 'red');
              } else {
                  $('.signup').html(data.success).css('color', 'green');
              }
          }
      });
      return false;
  });

});
</script>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-34860069-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
</html>