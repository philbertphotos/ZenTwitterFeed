<?php

/*
Plugin made to incorporate Twitter Feed on your Zenphoto in your theme's where you want it to appear.
 *		<?php if (function_exists('printTwitterFeed')) { ?>
 *		<div class="tfeed"><?php printTwitterFeed(); ?></div>
 *		<?php  } ?>
*/
$plugin_is_filter = 5|ADMIN_PLUGIN|THEME_PLUGIN;
$plugin_description = gettext("Adds Twitter Feed to Zenphoto.");
$plugin_author      = gettext("Joseph Philbert");
$plugin_version     = "1.0";
$plugin_URL         = "";

/*
Plugin options.
*/
$option_interface = 'zenTwitterOptions';

/*
Plugin option handling class
*/
class zenTwitterOptions {
				function zenTwitterOptions() {
								setOptionDefault('zenTwitter_twitterid', 'philbertphotos');
								setOptionDefault('zenTwitter_numberoftweets', 3);
								setOptionDefault('zenTwitter_tags', true);
								setOptionDefault('zenTwitter_nofollow', true);
								setOptionDefault('zenTwitter_target', true);
								setOptionDefault('zenTwitter_widget', true);
				}
				

	
function getOptionsSupported() {
return array (gettext('Twitter ID') => array('key' => 'zenTwitter_twitterid','type'
													=> OPTION_TYPE_TEXTBOX,
													'order'=>1,
													'desc' => gettext('Swap crearegroup for your Twitter User-name')),
													
						gettext('Number of Tweets') => array('key' => 'zenTwitter_numberoftweets','type'
													=> OPTION_TYPE_TEXTBOX,
													'order'=>2,
													'desc' => gettext('How many tweets to you want to show? Swap 4 for how many you would like.')),
													
						gettext('Twitter Tags') => array('key' => 'zenTwitter_tags','type' 
													=> OPTION_TYPE_CHECKBOX,
													'order' =>3,
													'desc' => gettext('Would you like to activate links within the tweets?')),
						gettext('No Follow') => array('key' => 'zenTwitter_nofollow','type' 
													=> OPTION_TYPE_CHECKBOX,
													'order' =>4,
													'desc' => gettext('Would you like to activate nofollow (Best for SEO)?')),		
						gettext('Link Behaviour') => array('key' => 'zenTwitter_target','type' 
													=> OPTION_TYPE_CHECKBOX,
													'order' =>5,
													'desc' => gettext('Would you like links to appear in a new window/tab?')),		
						gettext('Widget') => array('key' => 'zenTwitter_widget','type' 
													=> OPTION_TYPE_CHECKBOX,
													'order' =>6,
													'desc' => gettext('Would you like to show the Twitter Follow Widget button?')));		
									}
	}		

function PrintTwitterFeed () {
    $twitterid = (getOption('zenTwitter_twitterid'));
    $numberoftweets = getOption('zenTwitter_numberoftweets');
    $tags = (getOption('zenTwitter_tags'));
    $nofollow = getOption('zenTwitter_nofollow');
    $target = getOption('zenTwitter_target');
    $widget = (getOption('zenTwitter_widget'));	 
     
    // Here's the Science - futher comments can be found below
    function changeLink($string, $tags=false, $nofollow, $target){
      if(!$tags){
       $string = strip_tags($string);
      } else {
       if($target){
        $string = str_replace("<a", "<a target=\"_blank\"", $string);
       }
       if($nofollow){
        $string = str_replace("<a", "<a rel=\"nofollow\"", $string);
       }
      }
      return $string;
     }
 
     function getLatestTweet($xml, $tags=false, $nofollow=true, $target=true,$widget=false){
        global $twitterid;
      $xmlDoc = new DOMDocument();
      $xmlDoc->load($xml);
 
      $x = $xmlDoc->getElementsByTagName("entry");
 
      $tweets = array();
      foreach($x as $item){
       $tweet = array();
 
       if($item->childNodes->length)
       {
        foreach($item->childNodes as $i){
         $tweet[$i->nodeName] = $i->nodeValue;
        }
       }
        $tweets[] = $tweet;
      }
 
    // Here's the opening DIV and List Tags.
       echo "<div id=\"latesttweet\"><ul>\n";
 
      foreach($tweets as $tweettag){
       $tweetdate = $tweettag["published"];
       $tweet = $tweettag["content"];
       $timedate = explode("T",$tweetdate);
       $date = $timedate[0];
       $time = substr($timedate[1],0, -1);
       $tweettime = (strtotime($date." ".$time))+3600; // This is the value of the time difference - UK + 1 hours (3600 seconds)
       $nowtime = time();
       $timeago = ($nowtime-$tweettime);
       $thehours = floor($timeago/3600);
       $theminutes = floor($timeago/60);
       $thedays = floor($timeago/86400);
       if($theminutes < 60){
        if($theminutes < 1){
         $timemessage =  "Less than 1 minute ago";
        } else if($theminutes == 1) {
         $timemessage = $theminutes." minute ago.";
         } else {
         $timemessage = $theminutes." minutes ago.";
         }
        } else if($theminutes > 60 && $thedays < 1){
         if($thehours == 1){
         $timemessage = $thehours." hour ago.";
         } else {
         $timemessage = $thehours." hours ago.";
         }
        } else {
         if($thedays == 1){
         $timemessage = $thedays." day ago.";
         } else {
         $timemessage = $thedays." days ago.";
         }
        }
        // Here's the list tags wrapping each tweet.
        echo "<li>" . changeLink($tweet, $tags, $nofollow, $target) . "<br />\n";
        // Here's the span wrapping the time stamp.
        echo "<span>" . $timemessage . "</span></li>\n";
       }
    // Here's the closing DIV and List Tags.
        echo "</ul></div>";
 
        // Here's the Twitter Follow Button Widget
        if($widget){
            echo "<a href=\"https://twitter.com/" . (getOption('zenTwitter_twitterid')) . "\" class=\"twitter-follow-button\" data-show-count=\"true\">Follow @" . (getOption('zenTwitter_twitterid')) . "</a>
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=\"//platform.twitter.com/widgets.js\";fjs.parentNode.insertBefore(js,fjs);}}(document,\"script\",\"twitter-wjs\");</script>";
        }
 
     }
        $tweetxml = "http://search.twitter.com/search.atom?q=from:" . $twitterid . "&rpp=" . $numberoftweets . "";
        getLatestTweet($tweetxml, $tags, $nofollow, $target, $widget);
}
?>