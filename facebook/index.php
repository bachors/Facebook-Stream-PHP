<?php
/**
 * Facebook Stream PHP : Simple PHP wrapper for the v2.2 API
 * 
 * @category Awesomeness
 * @package  Facebook-API-PHP
 * @author   Ican Bachors <bachor.can@gmail.com>
 * @license  MIT License
 * @link     http://ibacor.com/labs/facebook-stream-using-php
 */

error_reporting(0);

if(empty($_GET['id'])){
	$_GET['id'] = '35688476100';
}

/* --------------------- Configuration -----------------------*/
$id_fb = $_GET['id']; // groups or pages
$base_url = 'http://localhost/facebook/';
$access_token = 'Your Access Token';
/* -----------------------------------------------------------*/

/* -- PHP function -- */
function indek($base_url,$id_fb,$access_token,$itil){
	$json = file_get_contents('https://graph.facebook.com/v2.2/'.$id_fb.'/feed?limit=25&access_token='.$access_token.'&until='.$itil);
	$result = json_decode($json);
	foreach ($result->data as $r) {									
		if(!empty($r->message)){
			echo '<div class="panel panel-default">';
			echo '<div class="panel-heading">';
			echo '<img src ="https://graph.facebook.com/'.$r->from->id.'/picture" class="pull-left">';
			echo '<p>'.$r->from->name.'<br>';
			if(!empty($r->comments)) {
				if(count($r->comments->data) == 25){
					$komen = count($r->comments->data).'+';
				}else{
					$komen = count($r->comments->data);
				}
			}else{
				$komen = 0;
			}
			if(!empty($r->likes)){
				if(count($r->likes->data) == 25){
					$like = count($r->likes->data).'+';
				}else{
					$like = count($r->likes->data);
				}
			}else{
				$like = 0;
			}
			if(!empty($r->shares)){
				$shares = $r->shares->count;
			}else{
				$shares = 0;
			}
			echo humanTiming(strtotime($r->created_time)).' ago<i class="fa fa-thumbs-up"> <a>'.$like.'</a></i><i class="fa fa-comment-o"> <a>'.$komen.'</a></i><i class="fa fa-share-alt"> <a>'.$shares.'</a></i></p>';
			echo '</div><div class="panel-body">';
			if(!empty($r->picture) && !empty($r->object_id)){
				echo '<a href="http://graph.facebook.com/'.$r->object_id.'/picture" class="popupimg pull-left" title="Perbesar"><img src="'.$r->picture.'"></a>';
			}
			echo '<pre>'.formatUrlsInText(substr(strip_tags(htmlentities($r->message)),0,150)).'... <a href="'.$base_url.'groups/'.$id_fb.'/post/'.str_replace("=","",$r->id).'" class="btn btn-danger btn-xs">Read more</a></pre>';
			echo'</div></div>';
		}
	}
	$nek = $result->paging->next;
	if(!empty($nek)){
		$data = explode('&until=', $nek);
		$idd = explode('&__paging_token', $data[1]);
		echo'<ul class="pager"><li class="next"><a href="'.$base_url.'groups/'.$id_fb.'/page/'.str_replace("=","",$idd[0]).'">Next &rarr;</a></li></ul>';
	}
}

function detil($idg,$access_token){
	$json = file_get_contents('https://graph.facebook.com/v2.2/'.$idg.'/search?access_token='.$access_token);
	$result = json_decode($json);									
	foreach ($result->data as $r) {
		if(!empty($r->message)){
			echo '<div class="panel panel-default">';
			echo '<div class="panel-heading">';
			echo '<img src ="https://graph.facebook.com/'.$r->from->id.'/picture" class="pull-left">';
			echo '<p>'.$r->from->name.'<br>';
			if(!empty($r->comments)) {
				if(count($r->comments->data) == 25){
					$komen = count($r->comments->data).'+';
				}else{
					$komen = count($r->comments->data);
				}
			}else{
				$komen = 0;
			}
			if(!empty($r->likes)){
				if(count($r->likes->data) == 25){
					$like = count($r->likes->data).'+';
				}else{
					$like = count($r->likes->data);
				}
			}else{
				$like = 0;
			}
			if(!empty($r->shares)){
				$shares = $r->shares->count;
			}else{
				$shares = 0;
			}
			echo humanTiming(strtotime($r->created_time)).' ago<i class="fa fa-thumbs-up"> <a>'.$like.'</a></i><i class="fa fa-comment-o"> <a>'.$komen.'</a></i><i class="fa fa-share-alt"> <a>'.$shares.'</a></i></p>';
			echo '</div><div class="panel-body">';
			if(!empty($r->picture) && !empty($r->object_id)){
				echo '<a href="http://graph.facebook.com/'.$r->object_id.'/picture" class="popupimg pull-left" title="Perbesar"><img src="'.$r->picture.'"></a>';
			}
			echo '<pre>'.formatUrlsInText(htmlentities($r->message)).'</pre>';
			echo'</div></div>';
			$ibc_fs = explode("_", $r->id);
			if($komen != 0) {
				komen($idg,$access_token,$ibc_fs[1]);
			}else{
				echo '<p align="center"><a class="btn btn-danger btn-xs" href="https://www.facebook.com/permalink.php?story_fbid='.$ibc_fs[1].'&id='.$idg.'" target="_BLANK">Full story</a></p>';
			}
		}
	}
}

function komen($idg,$access_token,$onfb){
	$json = file_get_contents('https://graph.facebook.com/v2.2/'.$idg.'/comments?access_token='.$access_token);
	$result = json_decode($json);									
	foreach ($result->data as $r) {
		if(!empty($r->message)){
			echo '<div class="bales"><div class="panel panel-default">';
			echo '<div class="panel-heading">';
			echo '<img src ="https://graph.facebook.com/'.$r->from->id.'/picture" class="pull-left">';
			echo '<p>'.$r->from->name.'<br>';
			echo humanTiming(strtotime($r->created_time)).' ago<i class="fa fa-thumbs-up"> <a>'.$r->like_count.'</a></i></p>';
			echo '</div><div class="panel-body">';
			$idk = $r->id;
			komen_img($idk,$access_token);
			echo '<pre>'.formatUrlsInText(htmlentities($r->message)).'</pre>';
			echo'</div></div></div>';
		}
	}
	echo '<p align="center"><a class="btn btn-danger btn-xs" href="https://www.facebook.com/permalink.php?story_fbid='.$onfb.'&id='.$idg.'" target="_BLANK">Full story</a></p>';
}

function komen_img($idk,$access_token){
	$json = file_get_contents('https://graph.facebook.com/v2.2/'.$idk.'/?fields=attachment&access_token='.$access_token);
	$result = json_decode($json);									
	if (!empty($result->attachment->media->image->src)) {
        echo '<a href="'.$result->attachment->media->image->src.'" class="popupimg pull-left komenimg" title="Perbesar"><img src="'.$result->attachment->media->image->src.'" alt="ibacor"></a>';
    }
}

function humanTiming ($time) {
	$time = time() - $time;
	$tokens = array (
		31536000 => 'year',
		2592000 => 'month',
		604800 => 'week',
		86400 => 'day',
		3600 => 'hour',
		60 => 'minute',
		1 => 'second'
	);
	foreach ($tokens as $unit => $text) {
		if ($time < $unit) continue;
		$numberOfUnits = floor($time / $unit);
		return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
	}
}
								
function formatUrlsInText($text){
	$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
	preg_match_all($reg_exUrl, $text, $matches);
	$usedPatterns = array();
	foreach($matches[0] as $pattern){
		if(!array_key_exists($pattern, $usedPatterns)){
			$usedPatterns[$pattern]=true;
			$text = str_replace  ($pattern, '<a href="'.$pattern.'" target="_BLANK" rel="nofollow">'.$pattern.'</a> ', $text);   
		}
	}
	return formatTagsInText($text);            
}
									
function formatTagsInText($text){
	$reg_exUrl = "/#(\\w+)/";
	preg_match_all($reg_exUrl, $text, $matches);
	$usedPatterns = array();
	foreach($matches[0] as $pattern){
		if(!array_key_exists($pattern, $usedPatterns)){
			$usedPatterns[$pattern]=true;
			$text = str_replace  ($pattern, '<a href="https://www.facebook.com/hashtag/'.preg_replace('/#/', '', $pattern).'" rel="nofollow" target="_BLANK">'.$pattern.'</a> ', $text);   
		}
	}
	return $text;            
}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>PHP Facebook Stream</title>
		<!-- CSS font-awesome -->
		<link type="text/css" rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css" />
		<!-- CSS fancybox -->
		<link type="text/css" rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css"/>
		<!-- CSS bootstrap -->
		<link type="text/css" rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" />
		<!-- CSS custom -->
		<style>
		body{
			background-color: #f1f1f1
		}
		.panel .panel-heading{
			background-color: #204D74;
			color: #fff
		}
		.panel-heading img{
			margin-right: 10px
		}
		div.bales .panel-heading{
			background-color: #fff;
			color: #333
		}
		.fa{
			margin-left: 10px;
			background: #fff;
			color: #337AB7;
			padding: 5px;
			border-radius: 5px
		}
		.fancybox-skin {
			background: none
		}
		.fancybox-opened .fancybox-skin {
			-webkit-box-shadow: none;
			-moz-box-shadow: none;
			box-shadow: none
		}
		pre {
			font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
			font-size: 14px;
			line-height: 1.42857;
			background: #fff;
			white-space: pre;
			white-space: pre-wrap;
			word-wrap: break-word;
			border: none
		}
		a.komenimg{
			width: 30%
		}
		a.komenimg img{
			width: 100%
		}
		footer{
			text-align: center;
			padding: 20px;
			background: #fff
		}
		</style>
	</head>
	<body>
		<!-- HTMl -->
		<nav class="navbar navbar-default" role="navigation">
			<div class="navbar-header">
				<a class="navbar-brand" href="<?php echo $base_url.'groups/'.$id_fb; ?>">Home</a>
			</div>
			<div>
				<ul class="nav navbar-nav">
					<li><a href="http://ibacor.com/media/sosmed-user-id-finder/" target="_blank">Facebook id finder?</a></li>
					<li>
						<form action="<?php echo $base_url; ?>" method="GET">
							<div class="input-group">
								<input name="id" class="form-control" type="text" placeholder="Groups ID or Pages ID" required/>
								<span class="input-group-btn">
									<button class="btn btn-danger">GO</button>
								</span>
							</div>
						</form>
					</li>
					<li><a href="http://ibacor.com/labs/jquery-ibacor-facebook-streaming/" target="_blank">jQuery Plugin <sup>free</sup></a></li>
				</ul>
				<a href="http://ibacor.com/labs/facebook-stream-using-php/" target="_blank" class="btn btn-success pull-right">Download this code</a>
			</div>
		</nav>
		<div class="container">
			<div class="row">
				<div class="col-md-8 col-md-offset-2">

					<!-- PHP call function -->
					<?php
					if(empty($_GET['s'])){
						if(empty($_GET['p'])){
							indek($base_url,$id_fb,$access_token,'');
						}else{
							indek($base_url,$id_fb,$access_token,$_GET['p']);
						}
					}else{
						detil($_GET['s'],$access_token);
					}
					?>

				</div>
			</div>
		</div>
		<footer>Code by <a href="http://ibacor.com" target="_blank">iBacor</a></footer>

		<!-- jQuery -->
		<script src="//code.jquery.com/jquery-2.1.3.min.js"></script>
		<!-- jQuery fancybox -->
		<script src="//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.js"></script>
		<script>
		$( document ).ready(function() {	   
			$(".popupimg").fancybox({   
				'width'  : '90%',
				'height'  : '90%',
				'autoScale'  : false,
				'transitionIn'  : 'none',
				'transitionOut'  : 'none',
				'type'  : 'iframe'
			});
		});
		</script>
	</body>
</html>
