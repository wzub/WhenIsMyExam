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
if (substr(getUrl(), 0, 8) != 'https://' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
	header('Location: https://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	exit();
}

require_once("sdk/src/facebook.php");

$config = array();
$config['appId'] = '374368595928882';
$config['secret'] = 'cfd4a28c4a4eb26fa3b9f7a9e94d24ff';

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
	<meta property="fb:app_id" content="374368595928882" />

<meta name="viewport" content="width=device-width, initial-scale=1"/>

<title>When is my exam!?</title>

<script src="style/js/jquery-1.7.1.min.js" type="text/javascript"></script>
<script src="style/js/scripts.js" type="text/javascript"></script>

<?php if (!$mobile && !$iobm) { ?>
<script type="text/javascript">
if(window==top){top.location="https://apps.facebook.com/whenismyexam/";}
</script>
<?php } ?>

<script type="text/javascript">
// want to see the source code?
// get in touch: https://facebook.com/doubleudesigns
$(function(){$("body").addClass("js");$("<div/>",{id:"loader"}).appendTo("#meta").hide();
$("#doubleu").clone().appendTo("#disclaimer .modal-body");$("#social").clone().appendTo("#likeprompt .modal-body").find(".fb-like").attr("data-width","280").parent().find(".fbshare img").attr("src","style/img/social/fbshare.gif");
$("[rel=tooltip]").tooltip({placement:$(this).data("trigger")?$(this).data("trigger"):"top",trigger:$(this).data("trigger")?$(this).data("trigger"):"hover"});
var b=$("#note-inner");var g=$("#loader");var i=$("#error");var e=$("#success");var a=$("#courseid");var h=$(".alert");var f=$("#searchform");h.add(".help-block").hide();
Form={refresh:function(){l=false;a.val("").parent().removeClass("success error").find("p.help-block").html("");h.add(".help-block").hide();b.fadeIn();},valid:function(){l=true;
a.parent().removeClass("error").addClass("success").find("p.help-block").fadeOut().html("");},invalid:function(){l=false;a.parent().removeClass("success").addClass("error").find("p.help-block").hide().html("Enter a valid course code and section, please").fadeIn();
},execute:function(o){if(k==0){$("#disclaimer").show().modal();k++;}var p=["schedule/rescheduled.html","schedule/mon-wed.html","schedule/tues-thurs.html","schedule/sat.html","schedule/sun.html"];
function m(t){var q=jQuery.Deferred();function s(v){return jQuery.ajax({url:v,beforeSend:function(){b.hide();g.show();},dataType:"html"});}function r(A){var y=$(A).find("td:contains('"+o+"')");
if(y.length!==0){var z=y.closest("table");var B=z.find(".day p").eq(y.index()).html();var w=z.find(".date p").eq(y.index()).html();var D=z.find(".time p").eq(y.index()).html();
var C=y.find("p").eq(-2).html();var v=y.find("p").eq(-1).html();var E=y.find("p.orig_time").html();var x={day:B,date:Date.parse(w).toString("MMMM d, yyyy"),slot:D,title:C?C:"",teacher:v?v:"",rescheduled:E?E:""};
return x;}}function u(){if(q.state()!=="pending"){return;}if(t.length==0){q.reject();return;}s(t.shift()).pipe(r,u).done(function(v){if(v){q.resolve(v);
}else{u();}});}u();return q.promise();}var n=m(p);n.always(function(){g.hide();$("#againLink").parent().show();});n.done(function(s){d++;var w=Date.today();
var v=Date.parse(s.date);var C=new TimeSpan(v-w);var y=C.days;var B=Date.compare(w,v);s.slot=s.slot.split("-");var t=new TimeSpan(s.slot[0].split(":"));
var x="That";if(y>0){x+="'s ";if(y>1){x+=y+" days from today. ";}else{if(y==1){x+="tomorrow. ";}}x+="Study well!";}else{if(y==0){x+="'s today! OMG.";}else{if(y<0){x+=" was ";
if(y<-1){x+=(y*(-1))+" days ago. ";}else{if(y==-1){x+="yesterday. ";}}x+="Hope it went well!";}}}var r=s.date.replace(/[,\s]+/g,"-");var q=s.slot[0].replace(/[:,\s]+/g,"-");
var A="<a onclick=\"sendResult('"+o+"', '"+s.day+"','"+r+"', '"+q+'\'); return false;" href="#" class="btn btn-small"><i class="icon-envelope"></i><span>Send this result</span></a>';
var u="";if(s.teacher&&s.title){u="<p>"+s.teacher+" ("+o+") <br/>"+s.title+"</p>";}var z="";if(s.rescheduled){z="<p><small><span class='label label-important'>Originally scheduled for "+s.rescheduled+"</span></small></p>";
}$("#success p").html("The exam for <strong><abbr rel='tooltip' title='"+s.title+"'>"+o+"</abbr></strong> is on <p class='spaced'><b class='big'>"+s.date+"</b><br/>"+s.day+" @ "+s.slot[0]+" (till "+s.slot[1]+").</p><p>"+x+"</p>"+z+u).parent().fadeIn().find("span#sendbutton").html(A);
$("#againLink").addClass("successful");$("#success abbr").tooltip();postFBStory(o,s.day,s.date,s.slot[0]);});n.fail(function(){i.fadeIn().find("p span").html(o);
c++;if(c>3){i.find("p.toomanyerrors").html('<p>You seem to be getting a lot of errors. Would you like to <a data-toggle="modal" href="#disclaimer">get in touch</a> with the developer?</p>');
}});}};var l=false;a.mask("aaa999a",{placeholder:"",completed:function(){Form.valid();},autoclear:false});$(".dismiss").on("click",function(){$(this).parent().parent().modal("hide");
});function j(){$("#course_list_table tr").on("click",function(){var m=$(this).find("td").eq(0).html();a.val(m);$("#course_list").modal("hide");});}$("#course_list_trigger").on("click",function(){if($("#course_list_container").hasClass("loaded")==false){g.clone().show().appendTo("#course_list_container").parent().load("list.html",function(){$(this).addClass("loaded").find("#loader").remove();
$("#course_list_filter").quicksearch("#course_list_table tbody tr",{noResults:"#noresults",loader:g});j();});}});$("#course_list_filter").quicksearch("#course_list_table tbody tr");
var k=0;var d=0;var c=0;$("#againLink").add("#title a").on("click",function(m){m.preventDefault();if($(this).hasClass("successful")&&(d==1||d==3||d==6)){$("#likeprompt").modal();
$(this).removeClass("successful");}Form.refresh();});f.on("submit",function(o){o.preventDefault();var n=a.val().toUpperCase();n=$.trim(n.replace(/\s+/g,""));
var m=/^[A-Z]{3}\d{3}[A-Z]/;if(m.exec(n)){Form.valid();}else{Form.invalid();return;}if(l==true){Form.execute(n);}else{Form.invalid();}});});
</script>

<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
</head>

<body lang="en">
<div id="fb-root"></div>
<script>
window.fbAsyncInit=function(){FB.init({appId:"299087623498147",channelUrl:window.location.protocol+"//"+window.location.host+"/channel.html",status:!0,cookie:!0,xfbml:!0});FB.getLoginStatus(function(a){"connected"===a.status?(accessToken=a.authResponse.accessToken,console.log("Logged in, authorized. All set.")):"not_authorized"===a.status?(a="https://www.facebook.com/dialog/oauth/?client_id=299087623498147&redirect_uri=https://apps.facebook.com/whenismyexam/",a+="&scope=publish_actions",window.top.location=
a):console.log("Not logged in to Facebook.")});FB.Canvas.setSize();FB.Canvas.setDoneLoading()};
function postToFeed(){FB.ui({method:"feed",link:"http://apps.facebook.com/whenismyexam/",picture:"http://whenismyexam.herokuapp.com/style/img/fb/icon75.png",name:"When is my Exam!?",caption:"IoBM exam timetables made easy.",description:"Quick and painless exam schedule search. Just enter your course code and instantly find out when your exam is!",actions:[{name:"IoBM Schedule Search",link:"https://apps.facebook.com/whenismyexam/"}]},function(a){(!a||!a.post_id)&&console.log("Not posted to Wall")})}
function sendResult(a,e,f,c){FB.ui({method:"send",description:"The exam for "+a+" is on "+e+", "+f+" at "+c+". Use this app to search for all your courses and instantly find out when their exams are scheduled!",name:"When is my Exam!? app",link:"https://apps.facebook.com/whenismyexam/",picture:"http://whenismyexam.herokuapp.com/style/img/Book-icon.png"},function(a){console.log(a)})}
function postFBStory(a,e,f,c){f=f.replace(/[,\s]+/g,"-");c=c.replace(/[:]+/g,"-");a=encodeURIComponent("http://whenismyexam.herokuapp.com/course/"+a+"/"+e+"/"+f+"/"+c);FB.api("/me/whenismyexam:search?course="+a,"post",function(a){!a||a.error?console.log("post: Error occured => "+a.error.message):console.log("Post was successful! Action ID: "+a.id)})}
(function(a){var e=a.getElementsByTagName("script")[0];a.getElementById("facebook-jssdk")||(a=a.createElement("script"),a.id="facebook-jssdk",a.async=!0,a.src="//connect.facebook.net/en_US/all.js",e.parentNode.insertBefore(a,e))})(document);
</script>

<div id="bg">

<div id="content" class="container">

	<div id="note-container">
		<div id="note-top"></div>

		<div id="note" class="">
			<h1 class="ir center" id="title"><a href="http://apps.facebook.com/whenismyexam/" title="quick, simple timetable search">When is my Exam!?</a></h1>
			
			<div id="social" class="clearfix">
				<div class="pull-left"><a href="#" onclick="postToFeed(); return false;" class="fbshare" rel="tooltip" title="Share on Facebook"><img alt="Share on Facebook" src="style/img/social/facebook.png"></a></div>
				<div class="pull-left fb-like" data-href="//whenismyexam.herokuapp.com/" data-send="false" data-width="350" data-show-faces="false"></div>
			</div>
			<div class="clearfix" id="ribbon">
				<p rel="tooltip" title="Last update: August 10, 2012" >FINALS</p>
			</div>

			<div id="note-inner">
				<div class="center">

				<?php if ($user && !$iobm) { ?>
					<p>Hi, <?php echo $user[first_name];?>!</p>
				<?php } else { ?>
					<!-- not logged in -->
				<?php } ?>

					<p class="lead">
						Enter your <strong>course code</strong> and <strong>section</strong>.
						<br/>Hit enter. That's it!
					</p>

				<?php if ($user || $iobm) { // if logged in, or at IoBM, show the form ?>
					<form id="searchform">
						<div class="control-group">
							<p class="help-block"></p>
							<input type="text" class="span3 input-xlarge" id="courseid" rel="tooltip" data-placement="left" data-trigger="focus" title="Eg., SSC101B" placeholder="Eg., SSC101B">
						</div>
						<button type="submit" class="btn-large btn btn-primary"><i class="icon-search icon-white"></i>Search</button>
					</form>
				<?php } else { // not logged in, not at IoBM ?>
					<p>You'll need to authorize this app in order to use it.</p>
					<fb:login-button scope="publish_actions" size="large" show-faces="true">Connect with Facebook</fb:login-button>
				<?php } ?>
				</div>

				<div id="notice">
					<p class="btn-group">
						<a class="btn" data-toggle="modal" href="#disclaimer"><i class="icon-exclamation-sign"></i>Disclaimer</a>
						<a id="course_list_trigger" class="btn" data-toggle="modal" href="#course_list"><i class="icon-th-list"></i>Course codes</a>
					</p>
					<!-- <p>
						<span class="label label-info">Note</span>
						<small>Updated: August 2012</small>
					</p> -->
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