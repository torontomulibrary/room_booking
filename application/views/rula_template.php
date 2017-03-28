<!DOCTYPE html>
<!--[if IE 6]>
<html lang="en-US" id="ie6" class="lt-ie9 lt-ie8 lt-ie7 no-js">
<![endif]-->
<!--[if IE 7]>
<html lang="en-US" id="ie7" class="lt-ie9 lt-ie8 no-js">
<![endif]-->
<!--[if IE 8]>
<html lang="en-US" id="ie8" class="lt-ie9 no-js">
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html lang="en-US">
<!--<![endif]-->
<head>
    <title>Room Booking - Ryerson University Library and Archives</title>
    <meta charset="UTF-8">
	
	<link href="<?= base_url() ?>assets/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="<?= base_url() ?>assets/js/bootstrap.min.js"></script>   

	<link rel="icon" href="<?php echo base_url()?>assets/template/rula/images/favicon-rula.ico" type="image/ico"/>
    <link rel="apple-touch-icon" href="<?php echo base_url()?>assets/template/rula/images/home-icon.png" type="image/png"/>
    <link rel="stylesheet" href="<?php echo base_url()?>assets/template/rula/css/standard_style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo base_url()?>assets/template/rula/css/standard_print.css" type="text/css" media="print" />
	
	<style>
	header *{
		box-sizing: content-box !important;
	}
	
	#app_links ul{
		padding: 0;
	}
	
	h3 {
		font-size: 24px !important;
	}
	</style>
	
<!--[if IE 7]>
<link rel="stylesheet" type="text/css" media="all" href="css/standard_style_ie7.css" />
<![endif]-->
<!--[if lt IE 9]>
<script src="<?php echo base_url()?>assets/template/rula/js/html5shiv.js"></script>
<script src="<?php echo base_url()?>assets/template/rula/js/html5shiv-printshiv.js"></script>
<script src="<?php echo base_url()?>assets/template/rula/js/respond.min.js"></script>
<![endif]-->

<?php if(isset($headers)) echo $headers; ?>

</head>
<body>
<div id="page">
	<header id="branding" role="banner">
            <div id="sitename">
                <div class="skip-link"><a class="assistive-text" href="##access">Skip to main menu</a></div>
                <a href="http://library.ryerson.ca/"><img src="<?php echo base_url() ?>assets/template/rula/images/RULA_logo.png" alt="Ryerson University Library &amp; Archives" /></a>
            </div> <!-- end div logo_images -->
            <div id="askushead"><a href="https://server.iad.liveperson.net/hc/64904228/?cmd=file&file=visitorWantsToChat&site=64904228&SV!skill=Ryerson&LEAppKey=f907f2d9acd64b7f8c00b83bed3c2822&referrer=http%3A//library.ryerson.ca/info/contactus/&bId=16"><img src="http://library.ryerson.ca/wp-content/themes/rula/images/ask_us_libguides_sm.png" alt="Need Help? Ask Us"></a></div>
 			<div style="clear: both;"></div>

			<nav id="access" role="navigation">
				<h3 class="assistive-text">Main menu</h3>
				<div class="skip-link"><a class="assistive-text" href="#content" title="Skip to content">Skip to content</a></div>
				<div id="top-nav">
					<ul id="top-nav-list">
						<li id="menu-item-449" class="menu-item"><a title="Library Catalogue for Books, Media, Journals" href="http://catalogue.library.ryerson.ca/">Catalogue</a></li>
						<li id="menu-item-448" class="menu-item"><a href="http://library.ryerson.ca/guides">Research Help</a></li>
						<li id="menu-item-447" class="menu-item"><a title="Browse the list of Databases and Indexes" href="http://library.ryerson.ca/articles/">Articles</a></li>
						<li id="menu-item-831" class="menu-item"><a title="List of Library Services" href="http://library.ryerson.ca/services/">Services</a></li>
						<li id="menu-item-446" class="menu-item menu-item-type-post_type menu-item-object-page"><a title="About the Library" href="http://library.ryerson.ca/info/">About Us</a></li>
						
						<li id="menu-item-443" class="menu-item menu-item-has-children"><a title="Special and Digital Collections" href="http://library.ryerson.ca/collections/">Collections &#9660</a>
							<ul class="sub-menu">
								<li id="menu-item-20" class="menu-item"><a href="http://library.ryerson.ca/asc/">Archives and Special Collections</a></li>
								<li id="menu-item-442" class="menu-item"><a title="GeoData Maps &amp; Statistics" href="http://library.ryerson.ca/gmdc">GeoData Maps &#038; Statistics</a></li>
								<li id="menu-item-444" class="menu-item"><a title="Ryerson&#8217;s Institutional Repository" href="http://library.ryerson.ca/collections/digital-commons/">RULA Digital Repository</a></li>
							</ul>
						</li>
						<li class="menu-item menu-item-search">
							<!-- Search this Site Section -->
							<form id="sitesearch" role="search" name="sitesearch" method="get" action="http://search.ryerson.ca/search"> 
								<input type="hidden" name="site" value="Library" /> 
								<input type="hidden" name="output" value="xml_no_dtd" /> 
								<input type="hidden" name="client" value="default_frontend" />								
								<input type="hidden" name="proxystylesheet" value="default_frontend" />
								<label for="sitesearchbox" class="assistive-text">Search the Library website</label>
								<input type="search" id="sitesearchbox" name="q" maxlength="255" value="Search the Library Website" onClick="if(this.value=='Search the Library Website') {this.value='';}"/> 
								<input type="image" src="http://library.ryerson.ca/wp-content/themes/rula/images/search_icon_small.png" alt="Submit Search"  /> 
							</form> 
						</li>
						<li style="clear:both;border:0;"></li>
					</ul>
				<div style="clear:both;"></div>
				</div>
			</nav><!-- #access -->
			
	</header><!-- ##branding -->
    <div id="content">
		<?php if(isset($breadcrumbs)):?>
		   <div class="breadcrumbs">
				 <a href="<?php echo base_url();?>">Home</a> &#187; <a href="/about"><?php echo $breadcrumbs; ?>
			</div>
		<?php endif; ?>
		
         <?php if(isset($content)) echo $content; ?>
    
    </div>      
    
<footer id="colophon" role="contentinfo">
            <div id="footer"> 		
				<p> 
					<a href="http://library.ryerson.ca">Home</a> | <a href="/info/accessibility" data-ga-event="Footer,Link,Accessibility">Accessibility </a> | <a href="/contactus" data-ga-event="Footer,Link,Contactus">Contact Us</a> | <a href="http://library.ryerson.ca/copyright" data-ga-event="Footer,Link,Copyright">Copyright</a> | <a href="/siteindex">Site Index</a> |  
                    <a href="http://www.facebook.com/pages/Toronto-ON/Ryerson-University-Library/5863804371?ref=mf" data-ga-event="Footer,SocialMedia,Facebook"><img src="https://library.ryerson.ca/wp-content/themes/rula_wordpress/images/facebook_icon.gif" alt="Facebook" title="Facebook" /></a> 
                    <a href="http://www.twitter.com/ryersonlibrary" data-ga-event="Footer,SocialMedia,Twitter"><img src="https://library.ryerson.ca/wp-content/themes/rula_wordpress/images/twitter_icon.png" alt="Twitter" width="16" height="16" /></a> 
                    <a href="http://www.flickr.com/photos/ryersonlibrary" data-ga-event="Footer,SocialMedia,Flickr"><img src="https://library.ryerson.ca/wp-content/themes/rula_wordpress/images/flickr_icon.gif" alt="Flickr" width="16" height="16" /></a> 
                    <a href="http://www.youtube.com/user/ryersonlibrary/videos" data-ga-event="Footer,SocialMedia,Youtube"><img src="https://library.ryerson.ca/wp-content/themes/rula_wordpress/images/youtube_icon.png" alt="YouTube" width="16" height="16" /></a>
                    <a href="http://pipes.yahoo.com/pipes/pipe.run?_id=46e7ea505e28c621582d37a391b705d6&_render=rss" data-ga-event="Footer,SocialMedia,RSS"><img src="https://library.ryerson.ca/wp-content/themes/rula_wordpress/images/feed_icon.png" alt="RSS Feed" width="16" height="16" /></a> | 
                                    &copy;2015 <a href="http://www.ryerson.ca/" data-ga-event="Footer,SocialMedia,RU"><img src="https://library.ryerson.ca/wp-content/themes/rula_wordpress/images/RUfooter_logo.gif" alt="Ryerson University" width="114" height="20" /></a>
				</p> 
			</div>
    </footer> 
</div><!-- page -->
<!-- insert Analytics code -->
</body>
</html>
