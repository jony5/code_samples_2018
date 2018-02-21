<?php

/* 
// J5
// Code is Poetry */
require('_crnrstn.root.inc.php');
include_once($CRNRSTN_ROOT . '_crnrstn.config.inc.php');
require($oENV->getEnvParam('DOCUMENT_ROOT').$oENV->getEnvParam('DOCUMENT_ROOT_DIR').'/common/inc/session/session.inc.php');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php
require($oENV->getEnvParam('DOCUMENT_ROOT').$oENV->getEnvParam('DOCUMENT_ROOT_DIR').'/common/inc/head/head.inc.php');
?>
</head>

<body>
<?php
	require($oENV->getEnvParam('DOCUMENT_ROOT').$oENV->getEnvParam('DOCUMENT_ROOT_DIR').'/common/inc/contact/contact.inc.php');
	?>

<div id="body_wrapper">
	<!-- HEAD CONTENT -->
	<?php
	require($oENV->getEnvParam('DOCUMENT_ROOT').$oENV->getEnvParam('DOCUMENT_ROOT_DIR').'/common/inc/nav/topnav.inc.php');
	?>
	<div class="cb"></div>
    
    <!-- LIFESTYLE BANNER -->
	<div id="banner_lifestyle_wrapper" style="background-image:url(<?php echo $oENV->getEnvParam('ROOT_PATH_CLIENT_HTTP').$oENV->getEnvParam('ROOT_PATH_CLIENT_HTTP_DIR'); ?>common/imgs/wood.jpg);">
    	<div id="banner_lifestyle">
			<?php
			require($oENV->getEnvParam('DOCUMENT_ROOT').$oENV->getEnvParam('DOCUMENT_ROOT_DIR').'/common/inc/lifestyle/banner.inc.php');
			?>
            
        </div>
    	
    </div>
    <div id="banner_lifestyle_dropshadow" style="background-image:url(<?php echo $oENV->getEnvParam('ROOT_PATH_CLIENT_HTTP').$oENV->getEnvParam('ROOT_PATH_CLIENT_HTTP_DIR'); ?>common/imgs/dropshadow.gif);">
    	<div id="banner_lifestyle_dropshadow_corner"><img src="<?php echo $oENV->getEnvParam('ROOT_PATH_CLIENT_HTTP').$oENV->getEnvParam('ROOT_PATH_CLIENT_HTTP_DIR'); ?>common/imgs/dropshadow_corner.gif" width="6" height="6" alt=""></div>
    </div>
    
    <div id="user_transaction_wrapper" class="user_transaction_wrapper" style="display:none;">
        <div class="user_transaction_content">
            <div id="user_transaction_status_msg" class="<?php echo $oUSER->transStatusMessage_ARRAY[0]; ?>"><?php echo $oUSER->transStatusMessage_ARRAY[1]; ?></div>
        </div>
    </div>
    
    <div class="cb_30"></div>
    <!-- PAGE CONTENT -->
    <div id="content_wrapper">
    	<div id="vert_nav_wrapper">
    		<div class="vert_nav_lnk_wrapper"><a href="<?php echo $oENV->getEnvParam('ROOT_PATH_CLIENT_HTTP').$oENV->getEnvParam('ROOT_PATH_CLIENT_HTTP_DIR'); ?>about/work/highlights/" target="_self"></a></div>
            <div class="vert_nav_lnk_wrapper"><a href="<?php echo $oENV->getEnvParam('ROOT_PATH_CLIENT_HTTP').$oENV->getEnvParam('ROOT_PATH_CLIENT_HTTP_DIR'); ?>about/work/experience/" target="_self"></a></div>
            <div class="vert_nav_lnk_wrapper"><a href="<?php echo $oENV->getEnvParam('ROOT_PATH_CLIENT_HTTP').$oENV->getEnvParam('ROOT_PATH_CLIENT_HTTP_DIR'); ?>about/work/skills/" target="_self"></a></div>
    		<div class="vert_nav_lnk_wrapper"></div>
    	</div>
    
    	<div id="content">
            <div class="content_title">Welcome!</div>
            <div class="content_copy">
            	<div class="col">
           			<p>I'm Jonathan 'J5' Harris, a web professional living and working in Atlanta, 
						GA. With 6 years of solid agency experience (+10 years of programming 
						experience in open source web technologies) behind me, I am always open to 
						investigate fresh opportunities to work with active, growing and digitally 
						fueled companies in order to strengthen and broaden aspects of their service 
						offerings from a technical perspective. For my previous employer, I worked 
						with corporate clients to formulate and execute (with my own bare hands in the 
						code whenever necessary) multi-channel business marketing initiatives. 
						</p>
                        <p>Digital brand strategy and execution are my core competencies.</p>
               	</div>
               	
               	<div class="col">
                        
                        <p>In 2004 I worked as a freelance designer, web application developer and 
						serial entrepreneur. After the implosion of my 8 person startup company, 
						CommercialNet Inc., I entered the world of interactive marketing and 
						advertising by accepting a UI developer position with the Atlanta based 
						agency, <a href="http://moxieusa.com" target="_blank">Moxie</a>.</p>
                        
						<p>In 2007 I helped a talented and diverse team of people at Moxie to start 
						the eCRM department. Lead by Darryl Bolduc, Tina West and Sapana Nanuwa (and with over 50 years of combined 
						email marketing experience), we worked with our clients to design and execute 
						state-of-the-art email marketing programs in support of their global strategic 
						initiatives. </p>
                </div>
                <div class="col">
                	<p>Born on Nov. 10th, 2005, my dog...also nicknamed 'J5'...is part 
						Korean Jindo, German Shepherd and Timber Wolf. </p>
                        <p><div class="embedded_image" style="width:295px;"><img src="<?php echo $oENV->getEnvParam('ROOT_PATH_CLIENT_HTTP').$oENV->getEnvParam('ROOT_PATH_CLIENT_HTTP_DIR'); ?>common/imgs/j5_octane.jpg" width="295" height="221" alt="J5" title="J5 chillin at Octane Coffee"></div></p>
                    <p>When I worked at agency, J5 accompanied me to the office on occasion as well as to local parks, coffee shops, 
						neighborhood bars and even the occasional house party.</p>
						<p>Click <a href="<?php echo $oENV->getEnvParam('ROOT_PATH_CLIENT_HTTP').$oENV->getEnvParam('ROOT_PATH_CLIENT_HTTP_DIR');  ?>downloads/resume/jharris_resume.pdf" target="_blank">here</a> to download the latest version of 
						my resume or visit my <a href="https://www.linkedin.com/in/jonathan-harris-6397143" target="_blank">LinkedIn</a> profile.</p>
                </div>
            </div>
            
            
            <div class="cb"></div>
            <div class="content_hr"></div>
            <div class="content_title">How did the idea for &quot;J5&quot; come about?</div>
            <div class="content_copy">
            	<div class="col">
           			<p>This is an excellent question! You see, back in the days of dial up (late 90's), I was quite new to the world of the interwebs. I didn't even have an email address. Realizing that I needed to get some kind of messaging account called an email address, I went to the folks at Juno. They hooked me up with a free email account and dial-up internet access!</p>
					<p><div class="embedded_image" style="width:296px;"><a href="https://en.wikipedia.org/wiki/Short_Circuit_%281986_film%29" target="_blank"><img src="<?php echo $oENV->getEnvParam('ROOT_PATH_CLIENT_HTTP').$oENV->getEnvParam('ROOT_PATH_CLIENT_HTTP_DIR');  ?>common/imgs/jony5_no_disassemble.jpg" width="296" height="197" alt="No disassemble...Johnny number 5" title="Johnny number 5"></a></div></p>
					<p>When I was filling out the Juno forms to get an email address, they asked me what I wanted it to be. I had no idea! Well, at that time, I had just finished watching the movie <a href="https://en.wikipedia.org/wiki/Short_Circuit_%281986_film%29" target="_blank">Short Circuit</a>, and so I was like &quot;I'll get the email jony5.&quot; (Johnny 5). From that point forward, I was <em>jony5@juno.com</em>. This era of my digital existence was defined by slow loading images and phone calls that broke the internet connection!</p>
				</div>
                <div class="col">
					<p>The birth of my nickname came about a little while later. In 2000, I had successfully (and satisfactorily) interviewed for a job with the bike shop at the Perimeter <a href="https://www.rei.com/stores/perimeter.html" target="_blank">REI</a> store. Before completing some introductory job placement materials and being released into the gene pool of REI employees, the manager told me that there were already 3 or 4 &quot;Jonathans&quot; working at the store, and I would have to have a different name. I told her that my email address was jony5@juno.com, and so maybe I could be &quot;jony5&quot;. </p>

					<p>At that moment, Elizabeth christened me as such, and for the next 4 years, people ONLY knew me as &quot;jony5&quot;. In fact, I even had one coworker write a check to me as &quot;Johnny 5.&quot; I was like, &quot;dude, that's not my real name!.&quot; As I sit here and reminisce, I realize how fortunate I really was to be associated with people of that calibre. I mean, I have always worked with really great people! Like marbling in a premium cut of the choicest beef, the genesis of my nickname jony5 is intertwined with some of the coolest and most down-to-earth people I've been fortunate enough to have been able to know.</p>
				</div>
                <div class="col">
					<p>In January of 2006, I acquired (for the first time in my life) a little puppy dog! At a loss for what to name him, I decided to go with &quot;J5&quot;...a play off of my nickname &quot;jony5&quot;. About a month later, I found myself working with advertising agency, <a href="http://moxieusa.com">Moxie</a>...and introduced myself to them as &quot;jony5&quot;...the name given to me by coworkers at REI. The office was dog friendly, and so people became accustomed to seeing &quot;J5&quot; and &quot;jony5&quot;...which gradually morphed over time to &quot;the 2 fives&quot;, &quot;five squared&quot; or just simply &quot;J5 and J5&quot;.</p>

					<p>At this point (16 years from the start), many of my friends (including all my Moxie coworkers) and even family members call me &quot;J5&quot;, and that is just fine with me.</p>
                    
                </div>
            </div>
            
            
            
    	</div><!-- END PAGE CONTENT -->
    
    </div>
    
    <div class="cb_30"></div>
    <?php
	require($oENV->getEnvParam('DOCUMENT_ROOT').$oENV->getEnvParam('DOCUMENT_ROOT_DIR').'/common/inc/footer/footer.inc.php');
	?>
    <div class="cb_50"></div>

</div>
</body>
</html>