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
            <div class="skip-link"><a class="assistive-text" href="#access">Skip to Main Menu</a></div>
            <a href="http://library.ryerson.ca/"><img src="<?php echo base_url()?>assets/template/rula/images/RULA_logo.png" alt="Ryerson University Library &amp; Archives" /></a>
        </div> <!-- end div logo_images -->
        <div id="banner_right"><a href="http://library.ryerson.ca/contactus"><img src="<?php echo base_url()?>assets/template/rula/images/askus_logo.png" alt="Ask a Librarian for Help"/></a></div>
        <div style="clear: both;"></div>
        <nav id="access" role="navigation">
            <h3 class="assistive-text">Main menu</h3>
            <div class="skip-link"><a class="assistive-text" href="#content">Skip to content</a></div>
            <div class="skip-link"><a class="assistive-text" href="#secondnav">Skip to sidebar</a></div>
            <div id="top-nav">
                <ul id="top-nav-list" class="grey-gradient"><!-- Each <li> represents a new nav item - putting a <ul class="top-sub-menu"> will create a drop-down sub-menu -->	
                    <li class="top-nav-item top-nav-item-first">
                        <a href="http://catalogue.library.ryerson.ca/" class="nav-link">Catalogue</a>
                    </li>
                    <li class="top-nav-item">
                        <a href="http://library.ryerson.ca/guides/" class="nav-link">Research Help</a>
                    </li>
                    <li class="top-nav-item">
                        <a href="http://library.ryerson.ca/articles/" class="nav-link">Articles</a>
                    </li>
                    <li class="top-nav-item">
                        <a href="http://library.ryerson.ca/services/" class="nav-link">Services</a>
                    </li>
                    <li class="top-nav-item">
                        <a href="http://library.ryerson.ca/info/" class="nav-link">About Us</a>
                    </li>
                    <li class="top-nav-item">
                        <a href="http://library.ryerson.ca/collections/" class="nav-link">Collections &#x25BC;</a>
                        <ul class="top-sub-menu">
                            <li class="top-sub-item"><a href="http://library.ryerson.ca/asc/">Archives and Special Collections</a></li>
                            <li class="top-sub-item"><a href="http://www.ryerson.ca/madar/">Map & Data Resources</a></li>
                            <li class="top-sub-item"><a href="http://digitalcommons.ryerson.ca/">Digital Commons @ Ryerson</a></li>
                        </ul>
                    </li>
                    <li class="top-nav-item top-nav-item-last">
                    <!-- Search this Site Section -->
                        <form id="searchform" name="topnavsearch" method="get" action="http://search.ryerson.ca/search"> 
                            <input type="hidden" name="site" value="Library" /> 
                            <input type="hidden" name="output" value="xml_no_dtd" /> 
                            <input type="hidden" name="client" value="default_frontend" /> 
                            <input type="hidden" name="proxystylesheet" value="default_frontend" /> 
                            <label for="searchtext" class="assistive-text">Search the Library website</label>
                            <input id="searchtext" style="width: 130px; height: 20px;" type="text" name="q" maxlength="255" value="Search the Library site" onClick="if(this.value=='Search the Library site') {this.value='';}"/> 
                            <input type="image" src="<?php echo base_url()?>assets/template/rula/images/search-icon.png" alt="search icon"  /> 
                        </form> 
                    </li>
                    <li style="clear:both;"></li>
                </ul>
                <div style="clear:both;"></div>
            </div>
        </nav><!-- #access -->
	</header><!-- #branding -->
    <div id="content">
		<?php if(isset($breadcrumbs)):?>
		   <div class="breadcrumbs">
				 <a href="<?php echo base_url();?>">Home</a> &#187; <a href="/about"><?php echo $breadcrumbs; ?>
			</div>
		<?php endif; ?>
		
         <?php if(isset($content)) echo $content; ?>
    
    </div>      
    
    <footer id="colophon" role="contentinfo">
            <p> 
                <a href="http://library.ryerson.ca/">Home</a> | <a href="http://library.ryerson.ca/accessibility">Accessibility</a> | <a href="http://library.ryerson.ca/siteindex">Site Index</a> | <a href="http://library.ryerson.ca/contactus">Contact Us</a> | <a href="http://www.facebook.com/pages/Toronto-ON/Ryerson-University-Library/5863804371?ref=mf"><img src="<?php echo base_url()?>assets/template/rula/images/facebook_icon.gif" alt="Facebook" /></a> 
                        <a href="http://www.twitter.com/ryersonlibrary"><img src="<?php echo base_url()?>assets/template/rula/images/twitter_icon.png" alt="Twitter" width="16" height="16" /></a> 
                        <a href="http://www.flickr.com/photos/ryersonlibrary"><img src="<?php echo base_url()?>assets/template/rula/images/flickr_icon.gif" alt="Flickr" width="16" height="16" /></a>
                        <a href="http://www.youtube.com/user/ryersonlibrary/videos"><img src="<?php echo base_url()?>assets/template/rula/images/youtube_icon.png" alt="YouTube" width="16" height="16" /></a>
                        <a href="http://pipes.yahoo.com/pipes/pipe.run?_id=46e7ea505e28c621582d37a391b705d6&_render=rss"><img src="<?php echo base_url()?>assets/template/rula/images/feed_icon.png" alt="RSS feed" width="16" height="16" /></a>
                    | <script type="text/javascript"> 
                            <!-- 
                            var year = new Date().getFullYear()
                            document.write('&copy;',year)
                            //-->
                        </script> 
                        <noscript> 
                        &copy;2014
                        </noscript>
                  <a href="http://www.ryerson.ca/"><img src="<?php echo base_url()?>assets/template/rula/images/RUfooter_logo.gif" alt="Ryerson University" width="114" height="20" /></a>
            </p>
    </footer> 
</div><!-- page -->
<!-- insert Analytics code -->
</body>
</html>
