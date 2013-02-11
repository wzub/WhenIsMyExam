<?php
// Branch: master

function getUrl($path = '/') {
	if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
			|| isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
	{
		$protocol = 'https://';
	}
	else {
		$protocol = 'http://';
	}
	return $protocol . $_SERVER['HTTP_HOST'] . $path;
}

// Enforce https
if (substr(getUrl(), 0, 8) != 'https://' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1' && $_SERVER['REMOTE_ADDR'] != '::1') {
	header('Location: https://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	exit();
}

require_once("sdk/src/facebook.php");

$config = array();
	$config['appId'] = '374368595928882';
	$config['secret'] = 'cfd4a28c4a4eb26fa3b9f7a9e94d24ff';

	// if on localhost, set development app ID
	if ( $_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '::1' ) {
		$on_localhost = true;
		$config['appId'] = '299087623498147';
	}

$facebook = new Facebook($config);

$user = $facebook->getUser();
$signed_request = $facebook->getSignedRequest();

if ($user) {
	try {
		// logged in, authenticated user
		$user = $facebook->api('/me');
	}
	catch (FacebookApiException $e) {
		// No authenticated user
		$user = null;
	}
}

// detect URL parameter or mod_rewrite
( isset($_REQUEST['iobm']) || ($_SERVER['REDIRECT_URL'] == '/iobm') ) ? $iobm = true : $iobm = false;
( isset($_REQUEST['mobile']) || ($_SERVER['REDIRECT_URL'] == '/mobile') ) ? $mobile = true : $mobile = false;

?>
<!doctype html>
<html lang="en" xmlns:fb="http://ogp.me/ns/fb#">
<head>

<meta charset="utf-8">

<meta http-equiv="imagetoolbar" content="false" />
<meta name="description" content="IoBM exam timetables made easy. Quick, painless search. Just enter your course code and instantly find out when your exam is!">
<meta name="keywords" content="">
<meta name="author" content="Waleed Zuberi">

<link rel="stylesheet" media="all" type="text/css" href="style/bootstrap.min.css">
<link rel="stylesheet" media="all" type="text/css" href="<?php if (isset($_REQUEST['mobile'])) {echo 'style/main.min.css';} else { echo 'style/main.min.css'; } ?>">

	<meta property="og:title" content="When is my Exam!?" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="https://whenismyexam.herokuapp.com/" />
	<meta property="og:image" content="http://whenismyexam.herokuapp.com/style/img/fb/icon75.png" />
	<meta property="og:site_name" content="When is my Exam!?" />
	<meta property="og:description" content="IoBM exam timetables made easy. Quick, painless search. Just enter your course code and instantly find out when your exam is!" />
	<meta property="fb:app_id" content="<?php echo $config['appId']; ?>" />

<meta name="viewport" content="width=device-width, initial-scale=1"/>

<title>When is my exam!?</title>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="style/js/jquery-1.8.2.min.js"><\/script>')</script>
<script src="style/js/scripts.js" type="text/javascript"></script>

<?php if (!$mobile && !$iobm && $on_localhost != true) { ?>
<script type="text/javascript">
if(window==top){top.location="https://apps.facebook.com/whenismyexam/";}
</script>
<?php } ?>

<script type="text/javascript">
// want to see the source code?
// get in touch: https://facebook.com/doubleudesigns
$(function(){$("body").addClass("js");$("#auth-loggedin").hide();$('input:checkbox').checkbox();$("<div/>",{id:"loader"}).appendTo("#meta").hide();$("#doubleu").clone().appendTo("#disclaimer .modal-body");$("#social").clone().appendTo("#likeprompt .modal-body").find(".fb-like").attr("data-width","280").parent().find(".fbshare img").attr("src","style/img/social/fbshare.gif");$("[rel=tooltip]").tooltip({placement:$(this).data("trigger")?$(this).data("trigger"):"top",trigger:$(this).data("trigger")?$(this).data("trigger"):"hover"});
var a=$("#note-inner"),c=$("#loader"),f=$("#error");$("#success");var d=$("#courseid"),e=$(".alert"),i=$("#searchform");e.add(".help-block").hide();Form={refresh:function(){g=!1;d.val("").parent().removeClass("success error").find("p.help-block").html("");e.add(".help-block").hide();a.fadeIn()},valid:function(){g=!0;d.parent().removeClass("error").addClass("success").find("p.help-block").fadeOut().html("")},invalid:function(){g=!1;d.parent().removeClass("success").addClass("error").find("p.help-block").hide().html("Enter a valid course code and section, please").fadeIn()},
execute:function(h){0==m&&($("#disclaimer").show().modal(),m++);var d,e="schedule/fri.html schedule/mon.html schedule/tues.html schedule/wed.html schedule/thurs.html schedule/sat.html schedule/sun.html".split(" "),g=function(j){var b=$(j).find("td:contains('"+h+"')");if(0!==b.length){var a=b.closest("table"),j=a.find(".day p").eq(b.index()).html(),d=a.find(".date p").eq(b.index()).html(),a=a.find(".time p").eq(b.index()).html(),c=b.find("p").eq(-2).html(),e=b.find("p").eq(-1).html(),b=b.find("p.orig_time").html();
return{day:j,date:Date.parse(d).toString("MMMM d, yyyy"),slot:a,title:c?c:"",teacher:e?e:"",rescheduled:b?b:""}}},i=function(){if("pending"===k.state())if(0==e.length)k.reject();else{var j=e.shift();jQuery.ajax({url:j,beforeSend:function(){a.hide();c.show()},dataType:"html"}).pipe(g,i).done(function(b){b?k.resolve(b):i()})}},k=jQuery.Deferred();i();d=k.promise();d.always(function(){c.hide();$("#againLink").parent().show()});d.done(function(a){l++;var b=Date.today(),d=Date.parse(a.date),c=(new TimeSpan(d-
b)).days;Date.compare(b,d);a.slot=a.slot.split("-");new TimeSpan(a.slot[0].split(":"));b="That";0<c?(b+="'s ",1<c?b+=c+" days from today. ":1==c&&(b+="tomorrow. "),b+="Study well!"):0==c?b+="'s today! OMG.":0>c&&(b+=" was ",-1>c?b+=-1*c+" days ago. ":-1==c&&(b+="yesterday. "),b+="Hope it went well!");c=a.date.replace(/[,\s]+/g,"-");d=a.slot[0].replace(/[:,\s]+/g,"-");c="<a onclick=\"sendResult('"+h+"', '"+a.title+"', '"+a.day+"','"+c+"', '"+d+'\'); return false;" href="#" class="btn btn-small"><i class="icon-envelope"></i><span>Send this result</span></a>';
d="";a.teacher&&a.title&&(d="<p>"+a.teacher+" ("+h+") <br/>"+a.title+"</p>");var e="";a.rescheduled&&(e="<p><small><span class='label label-important'>Originally scheduled for "+a.rescheduled+"</span></small></p>");$("#success p").html("The exam for <strong><abbr rel='tooltip' title='"+a.title+"'>"+h+"</abbr></strong> is on <p class='spaced'><b class='big'>"+a.date+"</b><br/>"+a.day+" @ "+a.slot[0]+" (till "+a.slot[1]+").</p><p>"+b+"</p>"+e+d).parent().fadeIn().find("span#sendbutton").html(c);$("#againLink").addClass("successful");
$("#success abbr").tooltip();postFBStory(h,a.day,a.date,a.slot[0])});d.fail(function(){f.fadeIn().find("p span").html(h);n++;3<n&&f.find("p.toomanyerrors").html('<p>You seem to be getting a lot of errors. Would you like to <a data-toggle="modal" href="#disclaimer">get in touch</a> with the developer?</p>')})}};var g=!1;d.mask("aaa999a",{placeholder:"",completed:function(){Form.valid()},autoclear:!1});$(".dismiss").on("click",function(){$(this).parent().parent().modal("hide")});$("#course_list_trigger").on("click",
function(){!1==$("#course_list_container").hasClass("loaded")&&c.clone().show().appendTo("#course_list_container").parent().load("list.html",function(){$(this).addClass("loaded").find("#loader").remove();$("#course_list_filter").quicksearch("#course_list_table tbody tr",{noResults:"#noresults",loader:c});$("#course_list_table tr").on("click",function(){var a=$(this).find("td").eq(0).html();d.val(a);$("#course_list").modal("hide")})})});$("#course_list_filter").quicksearch("#course_list_table tbody tr");
var m=0,l=0,n=0;$("#againLink").add("#title a").on("click",function(a){a.preventDefault();if($(this).hasClass("successful")&&(1==l||3==l||6==l))$("#likeprompt").modal(),$(this).removeClass("successful");Form.refresh()});i.on("submit",function(a){a.preventDefault();a=d.val().toUpperCase();a=$.trim(a.replace(/\s+/g,""));/^[A-Z]{3}\d{3}[A-Z]/.exec(a)?(Form.valid(),!0==g?Form.execute(a):Form.invalid()):Form.invalid()})});
</script>

<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
</head>

<body lang="en">
<div id="fb-root"></div>
<script>
var fb_appId = <?php echo $config['appId']; ?>;
window.fbAsyncInit=function(){FB.init({appId:fb_appId,channelUrl:window.location.protocol+"//"+window.location.host+"/channel.html",status:!0,cookie:!0,xfbml:!0});FB.Event.subscribe("auth.statusChange",function(a){a.authResponse?(console.log("Logged in!"),FB.api("/me",function(a){a.first_name&&(document.getElementById("auth-displayname").innerHTML=", "+a.first_name)}),document.getElementById("auth-loggedout").style.display="none",document.getElementById("auth-loggedin").style.display="block"):(console.log("App is not authorized by user"),
document.getElementById("auth-loggedout").style.display="block",document.getElementById("auth-loggedin").style.display="none")});FB.Canvas.setSize();FB.Canvas.setDoneLoading()};
function postToFeed(){FB.ui({method:"feed",link:"http://apps.facebook.com/whenismyexam/",picture:"http://whenismyexam.herokuapp.com/style/img/fb/icon75.png",name:"When is my Exam!?",caption:"IoBM exam timetables made easy.",description:"Quick and painless exam schedule search. Just enter your course code and instantly find out when your exam is!",actions:[{name:"IoBM Schedule Search",link:"https://apps.facebook.com/whenismyexam/"}]},function(a){(!a||!a.post_id)&&console.log("Not posted to Wall")})}
function sendResult(a,c,f,d,e){FB.ui({method:"send",description:"The exam for "+c+" ("+a+") is on "+f+", "+d+" at "+e+". Use this great app to search for your courses and instantly find out when their exams are scheduled!",name:"The exam for "+c,link:"https://apps.facebook.com/whenismyexam/",picture:"http://whenismyexam.herokuapp.com/style/img/Book-icon.png"},function(a){console.log(a)})}
function postFBStory(a,c,f,d){f=f.replace(/[,\s]+/g,"-");d=d.replace(/[:]+/g,"-");a=encodeURIComponent("http://whenismyexam.herokuapp.com/course/"+a+"/"+c+"/"+f+"/"+d);$("#fb_share_toggle").is(":checked")?FB.api("/me/whenismyexam:search?course="+a,"post",function(a){!a||a.error?console.log("post: Error occured => "+a.error.message):console.log("Post was successful! Action ID: "+a.id)}):console.log("User disallowed posting to feed")}function sendUpdateNotification(){}
(function(a){var c=a.getElementsByTagName("script")[0];a.getElementById("facebook-jssdk")||(a=a.createElement("script"),a.id="facebook-jssdk",a.async=!0,a.src="//connect.facebook.net/en_US/all.js",c.parentNode.insertBefore(a,c))})(document);
</script>

<div id="bg">

<div id="content" class="container">

	<div id="note-container">
		<div id="note-top"></div>

		<div id="note" class="">
			<h1 class="ir center" id="title"><a href="http://apps.facebook.com/whenismyexam/" title="quick, simple timetable search">When is my Exam!?</a></h1>
			
			<div id="social" class="clearfix">
				<div class="pull-left"><a href="#" onclick="postToFeed(); return false;" class="fbshare" rel="tooltip" title="Share on Facebook"><img alt="Share on Facebook" src="style/img/social/facebook.png"></a></div>
				<div class="pull-left fb-like" data-href="https://whenismyexam.herokuapp.com/" data-send="false" data-width="350" data-show-faces="false"></div>
			</div>
			<div class="clearfix" id="ribbon">
				<p rel="tooltip" title="Last update: September 26, 2012">FIRST HOURLIES</p>
			</div>

			<div id="note-inner">
				<div class="center">

					<p>Hi there<span id="auth-displayname"></span>!</p>

					<p class="lead">
						Enter your <strong>course code</strong> and <strong>section</strong>.
						<br/>Hit enter. That's it!
					</p>

					<div id="auth-loggedin">
						<form id="searchform">
							<div class="control-group input-append">
								<p class="help-block"></p>
								<input type="text" class="input-medium" id="courseid" rel="tooltip" data-placement="left" data-trigger="focus" title="Eg. SSC101B" placeholder="Eg. SSC101B">
								<button type="submit" class="btn-large btn btn-primary"><i class="icon-search icon-white"></i></button>
							</div>
						</form>
					</div>

					<div id="auth-loggedout">
						<p>You'll need to authorize this app in order to use it.</p>
						<fb:login-button scope="publish_actions" size="large" show-faces="true">Connect with Facebook</fb:login-button>
					</div>
				</div>

				<div id="notice">
					<p class="btn-group pull-right">
						<a class="btn" data-toggle="modal" href="#disclaimer"><i class="icon-exclamation-sign"></i>Disclaimer</a>
						<a id="course_list_trigger" class="btn" data-toggle="modal" href="#course_list"><i class="icon-th-list"></i>Course codes</a>
					</p>
					<div id="social_toggle">
						<input id="fb_share_toggle" name="fb_share_toggle" type="checkbox" checked>
						<label>Post to my Feed</label>
					</div>
				</div>
			</div>

			<div id="results">
				<div id="error" class="alert alert-error">
					<h1>Something is amiss</h1>
					<p>I couldn't find the schedule for <b><span></span></b>.<br/>
						Please make sure the course code was entered correctly and try again.
					</p>
					<p class="toomanyerrors"></p>
				</div>
				<div id="success" class="alert alert-success">
					<h1>Voila!</h1>
					<p></p>
					<span id="sendbutton"></span>
				</div>
			</div>
			<div id="meta">
				<p class="alert">
					<a class="btn-large btn btn-primary" id="againLink" href="" title="search another course"><i class="icon-refresh icon-white"></i>Search again</a>
					<a id="heart" class="btn-large btn" title="like this app?" data-toggle="modal" href="#likeprompt"><i class="icon-heart"></i></a>
				</p>
				<!-- loader -->
			</div>

			<div id="doubleu">
				<a class="ir" id="footer-doubleu" href="http://facebook.com/doubleudesigns" title="created and designed by Double U Designs">Double U Designs</a>
			</div>
		</div>

		<div id="note-bottom"></div>
	</div>

</div>

</div>

<div class="modal fade" id="disclaimer">
	<div class="modal-header">
		<a class="close dismiss" data-toggle="disclaimer">&times;</a>
		<h2>Disclaimer</h2>
	</div>
	<div class="modal-body">
		<p><strong>Please note.</strong> While every care has been taken to make this app as accurate and fool-proof as possible,
		 it is still just a script, and the developer makes no guarantees to its reliability.</p>
		<h3>Contact</h3>
		<p>If you find a bug, inaccurate result or have a suggestion to make this better, please <a href="http://facebook.com/doubleudesigns">get in touch with me</a>.</p>
	</div>
	<div class="modal-footer">
		<p class="pull-left"><small>&copy; 2012. Created by <a href="http://waleedzuberi.com/">Waleed Zuberi</a>.</small></p>
		<button data-toggle="disclaimer" class="dismiss btn-large btn btn-primary"><i class="icon-ok-sign icon-white"></i>I understand</button>
	</div>
</div>

<div class="modal fade" id="likeprompt">
	<div class="modal-header">
		<a class="close dismiss" data-toggle="likeprompt">&times;</a>
		<h2>Was it good for you, too?</h2>
	</div>
	<div class="modal-body">
		<p>If you find this tool useful, how about a little show of appreciation?
		<br/>Like us on Facebook and <a href="#" onclick="postToFeed(); return false;" title="Share on Facebook">tell your friends about us!</a></p>
	</div>
	<div class="modal-footer">
		<p class="pull-left"><small>&copy; 2012. Created by <a href="http://waleedzuberi.com/">Waleed Zuberi</a>.</small></p>
		<button data-toggle="likeprompt" class="dismiss btn-large btn btn-primary"><i class="icon-heart icon-white"></i>Great!</button>
	</div>
</div>
<div class="modal fade" id="course_list">
	<div class="modal-header">
		<a class="close dismiss" data-toggle="course_list">&times;</a>
		<h2>Course codes</h2>
	</div>
	<div class="modal-body">
		<p>Here's a (partial) list of course codes. You can filter the table using the text box.</p>
		<form><input type="text" class="span3 input" id="course_list_filter" placeholder="filter the table below"></form>
		<div id="course_list_container"></div>
	</div>
	<div class="modal-footer">
		<p class="pull-left"><small>&copy; 2012. Created by <a href="http://waleedzuberi.com/">Waleed Zuberi</a>.</small></p>
		<button data-toggle="course_list" class="dismiss btn-large btn btn-primary">Close</button>
	</div>
</div>

</body>
</html>