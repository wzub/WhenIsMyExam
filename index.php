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
$(function(){$('body').addClass('js');$('<div/>',{id:'loader'}).appendTo('#meta').hide();$('#doubleu').clone().appendTo('#disclaimer .modal-body');$('#social').clone().appendTo('#likeprompt .modal-body').find('.fb-like').attr('data-width','280').parent().find('.fbshare img').attr('src','style/img/social/fbshare.gif');$("[rel=tooltip]").tooltip({placement:$(this).data('trigger')?$(this).data('trigger'):'top',trigger:$(this).data('trigger')?$(this).data('trigger'):'hover'});var $mainContainer=$("#note-inner");var $loader=$("#loader");var $error=$("#error");var $success=$("#success");var $courseid=$('#courseid');var $alert=$('.alert');var $searchform=$('#searchform');$alert.add('.help-block').hide();Form={refresh:function(){mask_done=false;$courseid.val('').parent().removeClass('success error').find('p.help-block').html("");$alert.add('.help-block').hide();$mainContainer.fadeIn()},valid:function(){mask_done=true;$courseid.parent().removeClass('error').addClass('success').find('p.help-block').fadeOut().html("")},invalid:function(){mask_done=false;$courseid.parent().removeClass('success').addClass('error').find('p.help-block').hide().html("Enter a valid course code and section, please").fadeIn()},execute:function(q){if(search_count==0){$('#disclaimer').show().modal();search_count++}var urls=["schedule/mon-wed.html","schedule/tues-thurs.html","schedule/sat.html","schedule/sun.html"];function Finder(urls){var dfd=jQuery.Deferred();function fetchUrl(url){return jQuery.ajax({url:url,beforeSend:function(){$mainContainer.hide();$loader.show()},dataType:'html'})}function parseMatch(res){var $result=$(res).find("td:contains('"+q+"')");if($result.length!==0){var $closest=$result.closest('table');var day=$closest.find(".day p").eq($result.index()).html();var date=$closest.find('.date p').eq($result.index()).html();var slot=$closest.find('.time p').eq($result.index()).html();var title=$result.find('p').eq(-2).html();var teacher=$result.find('p').eq(-1).html();var rescheduled=$result.find('p.orig_time').html();var timetable={'day':day,'date':Date.parse(date).toString('MMMM d, yyyy'),'slot':slot,'title':title?title:'','teacher':teacher?teacher:'','rescheduled':rescheduled?rescheduled:''};return timetable}}function getMatch(){if(dfd.state()!=="pending"){return}if(urls.length==0){dfd.reject();return}fetchUrl(urls.shift()).pipe(parseMatch,getMatch).done(function(data){if(data){dfd.resolve(data)}else{getMatch()}})}getMatch();return dfd.promise()}var f=Finder(urls);f.always(function(){$loader.hide();$('#againLink').parent().show()});f.done(function(timetable){success_count++;var today=Date.today();var date_un=Date.parse(timetable.date);var diff_num=new TimeSpan(date_un-today);var diff_num_d=diff_num['days'];var comparison=Date.compare(today,date_un);timetable.slot=timetable.slot.split("-");var slot_h=new TimeSpan(timetable.slot[0].split(":"));var diff="That";if(diff_num_d>0){diff+="'s ";if(diff_num_d>1){diff+=diff_num_d+" days from today. "}else if(diff_num_d==1){diff+="tomorrow. "}diff+="Study well!"}else if(diff_num_d==0){diff+="'s today! OMG."}else if(diff_num_d<0){diff+=" was ";if(diff_num_d<-1){diff+=(diff_num_d*(-1))+" days ago. "}else if(diff_num_d==-1){diff+="yesterday. "}diff+="Hope it went well!"}var send_date=timetable.date.replace(/[,\s]+/g,'-');var send_time=timetable.slot[0].replace(/[:,\s]+/g,'-');var sendbutton='<a onclick="sendResult(\''+q+'\', \''+timetable.day+'\',\''+send_date+'\', \''+send_time+'\'); return false;" href="#" class="btn btn-small"><i class="icon-envelope"></i><span>Send this result</span></a>';var small_info='';if(timetable.teacher&&timetable.title){small_info="<p>"+timetable.teacher+" ("+q+") <br/>"+timetable.title+"</p>"}var rescheduled='';if(timetable.rescheduled){rescheduled="<p><small><span class='label label-important'>Originally scheduled for "+timetable.rescheduled+"</span></small></p>"}$("#success p").html("The exam for <strong><abbr rel='tooltip' title='"+timetable.title+"'>"+q+"</abbr></strong> is on <p class='spaced'><b class='big'>"+timetable.date+"</b><br/>"+timetable.day+" @ "+timetable.slot[0]+" (till "+timetable.slot[1]+").</p><p>"+diff+"</p>"+rescheduled+small_info).parent().fadeIn().find('span#sendbutton').html(sendbutton);$("#againLink").addClass('successful');$('#success abbr').tooltip();postFBStory(q,timetable.day,timetable.date,timetable.slot[0])});f.fail(function(){$error.fadeIn().find('p span').html(q);error_count++;if(error_count>3){$error.find('p.toomanyerrors').html('<p>You seem to be getting a lot of errors. Would you like to <a data-toggle="modal" href="#disclaimer">get in touch</a> with the developer?</p>')}})}};var mask_done=false;$courseid.mask("aaa999a",{placeholder:"",completed:function(){Form.valid()},autoclear:false});$('.dismiss').on("click",function(){$(this).parent().parent().modal('hide')});function course_list_loaded(){$('#course_list_table tr').on("click",function(){var tr_val=$(this).find('td').eq(0).html();$courseid.val(tr_val);$('#course_list').modal('hide')})}$('#course_list_trigger').on("click",function(){if($('#course_list_container').hasClass('loaded')==false){$loader.clone().show().appendTo('#course_list_container').parent().load('list.html',function(){$(this).addClass('loaded').find('#loader').remove();$('#course_list_filter').quicksearch('#course_list_table tbody tr',{noResults:'#noresults',loader:$loader});course_list_loaded()})}});$('#course_list_filter').quicksearch('#course_list_table tbody tr');var search_count=0;var success_count=0;var error_count=0;$('#againLink').add('#title a').on("click",function(e){e.preventDefault();if($(this).hasClass('successful')&&(success_count==1||success_count==3||success_count==6)){$('#likeprompt').modal();$(this).removeClass('successful')}Form.refresh()});$searchform.on("submit",function(e){e.preventDefault();var q=$courseid.val().toUpperCase();q=$.trim(q.replace(/\s+/g,''));var pattern=/^[A-Z]{3}\d{3}[A-Z]/;if(pattern.exec(q)){Form.valid()}else{Form.invalid();return}if(mask_done==true){Form.execute(q)}else{Form.invalid()}})});
</script>

<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
</head>

<body lang="en">
<div id="fb-root"></div>
<script>
window.fbAsyncInit=function(){FB.init({appId:'374368595928882',channelUrl:window.location.protocol+"//"+window.location.host+"/channel.html",status:true,cookie:true,xfbml:true});FB.Event.subscribe("auth.statusChange",function(a){if(a.authResponse){var u=a.authResponse.userID;var t=a.authResponse.accessToken;console.log("Logged in, so reloading page");console.log(t);FB.api('/me',function(me){if(me.name){document.getElementById('auth-displayname').innerHTML=", "+me.name;}});document.getElementById('auth-loggedout').style.display='none';document.getElementById('auth-loggedin').style.display='block';}else{console.log("App is not authorized by user");document.getElementById('auth-loggedout').style.display='block';document.getElementById('auth-loggedin').style.display='none';}});FB.Canvas.setSize();FB.Canvas.setDoneLoading();};function postToFeed(){var obj={method:'feed',link:'http://apps.facebook.com/whenismyexam/',picture:'http://whenismyexam.herokuapp.com/style/img/fb/icon75.png',name:'When is my Exam!?',caption:'IoBM exam timetables made easy.',description:'Quick and painless exam schedule search. Just enter your course code and instantly find out when your exam is!',actions:[{name:'IoBM Schedule Search',link:'https://apps.facebook.com/whenismyexam/'}]};function postToFeedCallback(response){if(response&&response.post_id){}else{console.log('Not posted to Wall');}}FB.ui(obj,postToFeedCallback);}function sendResult(code,day,date,time){var obj={method:'send',description:'The exam for '+code+' is on '+day+', '+date+' at '+time+'. Use this app to search for all your courses and instantly find out when their exams are scheduled!',name:'When is my Exam!? app',link:'https://apps.facebook.com/whenismyexam/',picture:'http://whenismyexam.herokuapp.com/style/img/Book-icon.png'};FB.ui(obj,function(response){console.log(response);});}function postFBStory(code,day,date,time){date=date.replace(/[,\s]+/g,'-');time=time.replace(/[:]+/g,'-');var storyurl=encodeURIComponent('http://whenismyexam.herokuapp.com/course/'+code+'/'+day+'/'+date+'/'+time);FB.api('/me/whenismyexam:search'+'?course='+storyurl,'post',function(response){if(!response||response.error){console.log('post: Error occured => '+response.error.message);}else{console.log('Post was successful! Action ID: '+response.id);}});}(function(d){var js,id='facebook-jssdk',ref=d.getElementsByTagName('script')[0];if(d.getElementById(id)){return;}js=d.createElement('script');js.id=id;js.async=true;js.src="//connect.facebook.net/en_US/all.js";ref.parentNode.insertBefore(js,ref);}(document));
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

					<p>Hi there<span id="auth-displayname"></span>!</p>

					<p class="lead">
						Enter your <strong>course code</strong> and <strong>section</strong>.
						<br/>Hit enter. That's it!
					</p>

					<div id="auth-loggedin">
						<form id="searchform">
							<div class="control-group">
								<p class="help-block"></p>
								<input type="text" class="span3 input-xlarge" id="courseid" rel="tooltip" data-placement="left" data-trigger="focus" title="Eg., SSC101B" placeholder="Eg., SSC101B">
							</div>
							<button type="submit" class="btn-large btn btn-primary"><i class="icon-search icon-white"></i>Search</button>
						</form>
					</div>

					<div id="auth-loggedout">
						<p>You'll need to authorize this app in order to use it.</p>
						<fb:login-button scope="publish_actions" size="large" show-faces="true">Connect with Facebook</fb:login-button>
					</div>
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