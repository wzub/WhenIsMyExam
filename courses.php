<?php

$course_code = strtoupper($_REQUEST['code']);
$course_day = ucwords($_REQUEST['day']);
$course_date = str_replace('-', ' ', ucwords($_REQUEST['date']));
$course_time = str_replace('-', ':', $_REQUEST['time']);

if (isset($_GET['fb_action_types'])) {
	header( 'Location: https://apps.facebook.com/whenismyexam/' ) ;
}

?>
<!doctype html>
<html lang="en" xmlns:fb="http://ogp.me/ns/fb#">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# whenismyexam: http://ogp.me/ns/fb/whenismyexam#">
	<meta property="fb:app_id"      content="374368595928882" /> 
	<meta property="og:type"        content="whenismyexam:course" /> 
	<meta property="og:url"         content="http://whenismyexam.herokuapp.com/course/<?php echo $course_code . '/' . $course_day . '/' . $course_date ?>" /> 
	<meta property="og:title"       content="<?php echo $course_code ?>" /> 
	<meta property="og:image"       content="http://whenismyexam.herokuapp.com/style/img/Book-icon.png" /> 
	<meta property="og:description" content="Exam is on <?php echo $course_day ?>, <?php echo $course_date ?> @ <?php echo $course_time ?>." />
<title>When is my Exam!?</title>
<link rel="stylesheet" media="all" type="text/css" href="http://whenismyexam.herokuapp.com/style/bootstrap.min.css">
<link rel="stylesheet" media="all" type="text/css" href="http://whenismyexam.herokuapp.com/style/main.min.css">
<style type="text/css">#againLink {margin:10px 0;}</style>
</head>

<body>
<div id="content" class="container">
<div id="note-container">
<div id="note-top"></div>

<div id="note" class="">
<h1 class="ir center" id="title"><a href="http://apps.facebook.com/whenismyexam/" title="quick, simple timetable search">When is my Exam!?</a></h1>

<div id="note-inner">
<div class="alert alert-success">
	<h1>Voila!</h1>
	The exam for <b><?php echo $course_code ?></b> is on
	<p class='spaced'>
		<b class='big'><?php echo $course_date ?></b><br><?php echo $course_day ?> @ <?php echo $course_time ?>.
	</p>
</div>

<a class="btn-large btn btn-primary" id="againLink" href="https://apps.facebook.com/whenismyexam/" title="search another course"><i class="icon-refresh icon-white"></i>Search for a course</a>
</div>

<div id="doubleu">
<a class="ir" id="footer-doubleu" href="http://doubleudesigns.com/" title="created and designed by Double U Designs">Double U Designs</a>
</div>
</div>

<div id="note-bottom"></div>
</div>
</div>
</body>
</html>