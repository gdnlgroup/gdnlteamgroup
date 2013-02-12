<?php

function nccsf_logo() {

    $logo_type = nccsf_top('logo_type');

    switch ($logo_type) {
        case 'text_logo':
            $logo = '<h1 class="logo">' . nccsf_top('logo_text') . '</h1>';
            break;
        case 'image_logo':
            $logo = '<img src="' . nccsf_top('logo_image') . '" alt="" />';
            break;
        default:
            $logo = '<div class="logo-sep">&nbsp;</div>';
    }

    return $logo;
}

function nccsf_content() {
    if (nccsf_top('module_content') == 'enable') {
        $content = '<div id="page_content" class="module textcenter">';
        $content .= nccsf_top('page_content');
        $content .= '</div>';
    } else {
        $content = '';
    }
    return $content;
}

function nccsf_countdown() {
    if (nccsf_top('module_countdown') == 'enable') {
        $content = '<div id="countdown-timer" class="module">';

        $date = nccsf_top('launch_date');
        $today = date('Y-m-d h:i:s a');
        if (strtotime($date) > strtotime($today)):

            $content .= '<div id="countdown">
<script type="text/javascript">
var montharray=new Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec")
function countdown(yr,m,d,h,i,s){
theyear=yr;themonth=m;theday=d;thehour=h;themin=i;thesec=s
var today=new Date()
var todayy=today.getYear()
if (todayy < 1000)
todayy+=1900
var todaym=today.getMonth()
var todayd=today.getDate()
var todayh=today.getHours()
var todaymin=today.getMinutes()
var todaysec=today.getSeconds()
var todaystring=montharray[todaym]+" "+todayd+", "+todayy+" "+todayh+":"+todaymin+":"+todaysec
futurestring=montharray[m-1]+" "+d+", "+yr+" "+h+":"+i+":"+s
dd=Date.parse(futurestring)-Date.parse(todaystring)
dday=Math.floor(dd/(60*60*1000*24)*1)
dhour=Math.floor((dd%(60*60*1000*24))/(60*60*1000)*1)
dmin=Math.floor(((dd%(60*60*1000*24))%(60*60*1000))/(60*1000)*1)
dsec=Math.floor((((dd%(60*60*1000*24))%(60*60*1000))%(60*1000))/1000*1)
if(dday==0&&dhour==0&&dmin==0&&dsec==1){
document.forms.count.count2.value=current
return
}
else
$(\'#countdown\').html(\'<span class="day">\'+dday+ \'<span class="days timer-text"> days</span></span> <span class="hour">\'+dhour+\' <span class="hours timer-text">hours</span></span> <span class="min">\'+dmin+\'<span class="minutes  timer-text"> minutes</span></span> <span class="sec">\'+dsec+\' <span class="seconds timer-text"> seconds</span></span>\');
setTimeout("countdown(theyear,themonth,theday,thehour,themin,thesec)",1000);
}
countdown(' . date('Y', strtotime($date)) . ',' . date('m', strtotime($date)) . ',' . date('d', strtotime($date)) . ',' . date('H', strtotime($date)) . ',' . date('i', strtotime($date)) . ',' . date('s', strtotime($date)) . ')
</script>
    <noscript>
        <span class="launch-date">' . date('M dS, Y') . '</span>
    </noscript>
</div>';
            $content .= '</div>';

        endif;


    } else {
        $content = '';
    }
    return $content;
}

function nccsf_twitter_feed() {
    if (nccsf_top('module_twitter_feeds') == 'enable') {
        $content = '<div id="twitter-feed" class="module">';
        $content .= '
<div class="twitters" id="tweets">
    '.__('Loading Tweets', 'nccsf').'
</div>
<span class="twitter-icon-big">&nbsp;</span>
';

        $content .= "
<script type=\"text/javascript\">
		getTwitters('tweets', {
		id: '". nccsf_top('twitter_handle') ."',
		timeout: 3,
		count: 1,
		onTimeout: function () {
                	this.innerHTML = 'Twitter is down right now :( Follow me @".nccsf_top('twitter_handle')." to view my updates!';
                },
                onTimeoutCancel: true, // don't allow twitter to finsih the job
		ignoreReplies: true
	});
</script>
";

        $content .= '</div>';
    } else {
        $content = '';
    }
    return $content;
}

function nccsf_social_media_icons() {
    if (nccsf_top('module_social_media_icons') == 'enable') {
        $content = '
<div id="social-media">
<!-- AddThis Button BEGIN -->
<div class="addthis_toolbox addthis_default_style ">
<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
<a class="addthis_button_tweet"></a>
<a class="addthis_counter addthis_pill_style"></a>
</div>
<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4e2e720b1cfd1131"></script>
<!-- AddThis Button END -->
</div>
';
    } else {
        $content = '';
    }
    return $content;
}

function nccsf_contact_form() {
    if (nccsf_top('module_contact_form') == 'enable') {
        $content = '
<div id="contact-form">
    <a href="mailto:'.  nccsf_top('contact_email').'" title="" class="icon-contact">Contact Us</a>
</div>
';
    } else {
        $content = '';
    }
    return $content;
}

function nccsf_email_subscription() {
    if (nccsf_top('module_email_subscription') == 'enable') {
        $content = '<div id="email_subscription" class="module clearfix">';
        $content .= '<div id="email_subscription_form">
<form action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open(\'http://feedburner.google.com/fb/a/mailverify?uri='.  nccsf_top('feedburner_id').'\', \'popupwindow\', \'scrollbars=yes,width=550,height=520\');return true">
<input type="text" placeholder="'.__('Enter your email address', 'nccsf').'" name="email"/>
<input type="hidden" value="'.  nccsf_top('feedburner_id').'" name="uri"/>
<input type="hidden" name="loc" value="en_US"/>
<input type="submit" value="'.__('Notify Me', 'nccsf').'" />
</form>
</div>
';
        $content .= '</div><p>&nbsp;</p>';
    } else {
        $content = '';
    }
    return $content;
}