<!DOCTYPE html>
<html class="" lang="en-US">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width">
  <title>
    Study Space Booking | Ryerson University Library &amp; Archives  </title>

  <link rel="shortcut icon" href="https://library.ryerson.ca/wp-content/themes/rula_wordpress/images/favicon.ico?v=1.23.0">


<link rel="stylesheet" id="font-awesome-style-css" href="<?php echo base_url(); ?>assets/template/rula_covid/css/font-awesome.css" type="text/css" media="all">
<link rel="stylesheet" id="bootstrap-style-css" href="<?php echo base_url(); ?>assets/template/rula_covid/css/bootstrap.css" type="text/css" media="all">

<link rel="stylesheet" id="ryerson-web-style-css" href="<?php echo base_url(); ?>assets/template/rula_covid/css/style.css" type="text/css" media="all">

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/template/rula_covid/js/bootstrap.min.js"></script>


	<style type="text/css">	
		#room_booking_content{
			max-width: 1000px;
			margin: 2em auto;
			padding: 0 2em;
			padding-bottom: 1em;
			background-color: #f9f9f9;
			min-height: 700px;
		}
	</style>
		
		<?php if(isset($headers)) echo $headers; ?>
		
	</head>

<body class="page-template-default page ">

  
    <a class="assistive-text btn" href="#app_links">Skip to main menu</a>
    <a class="assistive-text btn" href="#booking_container">Skip to content</a>

  <header class="global-header-top">
  <div class="container">
    <div class="row">
      <div class="col-xs-12">
        <nav class="navbar">
          <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">

              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#global-navigation" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <i class="fa fa-bars" title="Toggle navigation"></i>
              </button>

              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#site-search" aria-expanded="false">
                <span class="sr-only">Toggle search form</span>
                <i class="fa fa-search" title="Toggle search form"></i>
              </button>

              <!-- RULA Logo standalone site compatability -->
                               <a class="navbar-brand" href="https://library.ryerson.ca/" ga-on="click" ga-event-category="Navigation" ga-event-action="Header click" ga-event-label="Logo"><img style="height: 50px" src="https://library.ryerson.ca/wp-content/themes/rula_wordpress/images/rylib_logo.svg" alt="Ryerson University Library &amp; Archives"></a>
                          </div>

            <!-- Site Search -->
            <div class="collapse navbar-collapse navbar-left" id="site-search">
                            <form action="https://library.ryerson.ca/" method="get" class="navbar-form form-track-submits" data-ga-event-category="Search / Resource Discovery" data-ga-event-action="Site search" data-ga-event-label="Search WordPress">
                <input type="text" name="s" placeholder="Search library.ryerson.ca">
                <button aria-label="Search" type="submit"><i class="fa fa-search" title="Search"></i></button>
              </form>
                          </div>

            <!-- Global Header Top Navigation Area -->
            <div class="collapse navbar-collapse navbar-right" id="global-navigation">
              <ul class="nav navbar-nav" role="navigation">
                <li id="menu-item-31015" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-31015"><a href="https://my.ryerson.ca/" ga-on="click" ga-event-category="Navigation" ga-event-action="Header click" ga-event-label="my.ryerson">my.ryerson</a></li>
<li id="menu-item-30963" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-30963"><a href="https://catalogue.library.ryerson.ca/patroninfo" ga-on="click" ga-event-category="Navigation" ga-event-action="Header click" ga-event-label="Renew Loans">Renew Loans</a></li>
              </ul>
            </div>
          </div><!-- /.container-fluid -->
        </nav>
      </div>
    </div>
  </div>
</header>

<header class="global-header-bottom">
  <div class="container">
    <div class="navbar-header">
      <a class="navbar-brand" href="https://library.ryerson.ca/">Home</a>
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#global-header-bottom" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <i class="fa fa-angle-down" title="Toggle navigation"></i>
      </button>
    </div>
    <div id="global-header-bottom" class="collapse navbar-collapse"><ul id="menu-library-navigation" class="nav navbar-nav"><li itemscope="itemscope" itemtype="https://www.schema.org/SiteNavigationElement" id="menu-item-449" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-449"><a title="Catalogue" href="https://catalogue.library.ryerson.ca/" ga-on="click" ga-event-category="Navigation" ga-event-action="Header click" ga-event-label="Catalogue">Catalogue</a></li>
<li itemscope="itemscope" itemtype="https://www.schema.org/SiteNavigationElement" id="menu-item-447" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-447"><a title="Articles" href="https://library.ryerson.ca/articles/" ga-on="click" ga-event-category="Navigation" ga-event-action="Header click" ga-event-label="Articles">Articles</a></li>
<li itemscope="itemscope" itemtype="https://www.schema.org/SiteNavigationElement" id="menu-item-448" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-448"><a title="Research Help" href="https://library.ryerson.ca/guides" ga-on="click" ga-event-category="Navigation" ga-event-action="Header click" ga-event-label="Research Help">Research Help</a></li>
<li itemscope="itemscope" itemtype="https://www.schema.org/SiteNavigationElement" id="menu-item-831" class="menu-item menu-item-type-post_type menu-item-object-page  page_item page-item-478 menu-item-831"><a title="Services" href="https://library.ryerson.ca/services/" ga-on="click" ga-event-category="Navigation" ga-event-action="Header click" ga-event-label="Services">Services</a></li>
<li itemscope="itemscope" itemtype="https://www.schema.org/SiteNavigationElement" id="menu-item-25617" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-25617"><a title="About Us" href="https://library.ryerson.ca/info/about-us/" ga-on="click" ga-event-category="Navigation" ga-event-action="Header click" ga-event-label="About Us">About Us</a></li>
</ul></div>  </div>
</header>

    
      
<header class="rula-page-header">
  <div class="container">
    <div class="row">
      <div class="col-xs-12">
        <h1>Study Space Booking [Temporarily Restricted]</h1>
      </div>
    </div>
  </div>
</header>

  



<?php if(isset($breadcrumbs)):?>
   <div class="breadcrumbs">
		 <a href="<?php echo base_url();?>">Home</a> &#187; <a href="/about"><?php echo $breadcrumbs; ?>
	</div>
<?php endif; ?>
		
<div id="room_booking_content">
	<?php if(isset($content)) echo $content; ?>
</div>
	
<footer class="local-footer">
  <div class="container">
    <div class="row">
      <div class="col-xs-12 col-sm-3">
        <h4>Ryerson University Library and Archives</h4>
        <p>
                      <a href="http://www.ryerson.ca/maps/?building=LIB" ga-on="click" ga-event-category="Navigation" ga-event-action="Footer click" ga-event-label="Library Building">Library Building</a><br>            
                    350 Victoria Street, 2nd Floor<br>
          <abbr title="Phone">P:</abbr> (416) 979-5055<br>          Fax: (416) 979-5215<br>          Email: <a href="mailto:refdesk@ryerson.ca">refdesk@ryerson.ca</a>        </p>
        <ul class="social">
                      <li><a href="https://www.facebook.com/RyersonLibrary/" ga-on="click" ga-event-category="Navigation" ga-event-action="Footer click" ga-event-label="Facebook"><i class="fa fa-facebook"></i></a></li>
                    

                      <li><a href="https://twitter.com/ryersonlibrary" ga-on="click" ga-event-category="Navigation" ga-event-action="Footer click" ga-event-label="Twitter"><i class="fa fa-twitter"></i></a></li>
          
                      <li><a href="https://www.instagram.com/ryersonulibrary" ga-on="click" ga-event-category="Navigation" ga-event-action="Footer click" ga-event-label="Instagram"><i class="fa fa-instagram"></i></a></li>
                    
          
                      <li><a href="https://www.youtube.com/user/ryersonlibrary" ga-on="click" ga-event-category="Navigation" ga-event-action="Footer click" ga-event-label="YouTube"><i class="fa fa-youtube"></i></a></li>
          
                      <li><a href="https://apps.library.ryerson.ca/api/merge/" ga-on="click" ga-event-category="Navigation" ga-event-action="Footer click" ga-event-label="RSS"><i class="fa fa-rss"></i></a></li>
                  </ul>
      </div>
      <div class="col-xs-12 col-sm-3">
        <h4>Links</h4>
        <nav class="local-footer-links">
                    <ul id="menu-footer" class="menu"><li id="menu-item-29131" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-29131"><a href="https://library.ryerson.ca/services/disabilities/accessibility/" ga-on="click" ga-event-category="Navigation" ga-event-action="Footer click" ga-event-label="Accessibility Services">Accessibility Services</a></li>
<li id="menu-item-29132" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-29132"><a href="https://library.ryerson.ca/copyright" ga-on="click" ga-event-category="Navigation" ga-event-action="Footer click" ga-event-label="Copyright Services">Copyright Services</a></li>
<li id="menu-item-29133" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-29133"><a href="https://library.ryerson.ca/info/about-us#contactus" ga-on="click" ga-event-category="Navigation" ga-event-action="Footer click" ga-event-label="Contact Us">Contact Us</a></li>
<li id="menu-item-29134" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-29134"><a href="https://library.ryerson.ca/siteindex/" ga-on="click" ga-event-category="Navigation" ga-event-action="Footer click" ga-event-label="Site Index">Site Index</a></li>
</ul>                  </nav>
      </div>
      <div class="col-xs-12 col-sm-6">
                  <a href="https://www.google.ca/maps/place/Ryerson+University+Library+%26+Archives/@43.658187,-79.3828597,17z/data=!3m1!4b1!4m5!3m4!1s0x882b34cab2a56619:0x67aab32eaa367e3b!8m2!3d43.658187!4d-79.380671" ga-on="click" ga-event-category="Navigation" ga-event-action="Footer click" ga-event-label="Google Map">
            <img src="<?php echo base_url(); ?>assets/template/rula_covid/images/rula_map.png" alt="Google Map" class="map">
          </a>
              </div>
    </div>
  </div>
</footer>

<footer class="global-footer-top">
  <div class="container">
    <div class="row gft-top">
      <div class="col-xs-9 text-left">
        <a href="https://www.ryerson.ca/"><strong>Ryerson University</strong></a>
      </div>
      <div class="col-xs-3 text-right">
        <a href="#gft-collapse" data-toggle="collapse" class="collapse-toggle collapsed"></a>
      </div>
    </div>

    <div id="gft-collapse" class="collapse">
      <div class="row gft-middle">
        <div class="col-xs-6 text-left">
          350 Victoria Street<br>
          Toronto, ON M5B 2K3<br>
          <abbr title="Phone">P:</abbr> (416) 979-5000<br>
        </div>
        <div class="col-xs-6 text-right">
          <p>Follow Ryerson</p>
          <ul class="social">
            <li><a href="https://www.facebook.com/ryersonu" ga-on="click" ga-event-category="Navigation" ga-event-action="Footer click" ga-event-label="Facebook"><i class="fa fa-facebook"></i></a></li>
            <li><a href="https://twitter.com/RyersonU"><i class="fa fa-twitter"></i></a></li>
            <li><a href="https://www.youtube.com/user/RyersonUTube" ga-on="click" ga-event-category="Navigation" ga-event-action="Footer click" ga-event-label="YouTube"><i class="fa fa-youtube"></i></a></li>
            <li><a href="https://www.linkedin.com/company/ryerson-university" ga-on="click" ga-event-category="Navigation" ga-event-action="Footer click" ga-event-label="LinkedIn"><i class="fa fa-linkedin"></i></a></li>
          </ul>

        </div>
        <div class="col-xs-12">
          <div class="row gft-bottom">
            <div class="col-xs-6 text-left">
              <ul class="global-footer-links">
                <li><a href="http://ryerson.ca/contact/" ga-on="click" ga-event-category="Navigation" ga-event-action="Footer click" ga-event-label="Directory">Directory</a></li>
                <li><a href="http://ryerson.ca/maps/" ga-on="click" ga-event-category="Navigation" ga-event-action="Footer click" ga-event-label="Maps and Directions">Maps and Directions</a></li>
              </ul>
            </div>
            <div class="col-xs-6 text-right">
              <ul class="global-footer-links">
                <li><a href="http://ryerson.ca/jobs/" ga-on="click" ga-event-category="Navigation" ga-event-action="Footer click" ga-event-label="Careers">Careers</a></li>
                <li><a href="http://ryerson.ca/media/" ga-on="click" ga-event-category="Navigation" ga-event-action="Footer click" ga-event-label="Media Room">Media Room</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</footer>

<footer class="global-footer-bottom">
  <div class="container">
    <div class="row">
      <div class="col-xs-12">
        <ul class="global-footer-links">
          <li><a href="http://ryerson.ca/privacy/" ga-on="click" ga-event-category="Navigation" ga-event-action="Footer click" ga-event-label="Privacy Policy">Privacy Policy</a></li>
          <li><a href="http://ryerson.ca/accessibility/" ga-on="click" ga-event-category="Navigation" ga-event-action="Footer click" ga-event-label="Accessibility">Accessibility</a></li>
          <li><a href="http://www.ryerson.ca/ryerson.ca/terms" ga-on="click" ga-event-category="Navigation" ga-event-action="Footer click" ga-event-label="Terms &amp; Conditions">Terms &amp; Conditions</a></li>
        </ul>
      </div>
    </div>
    
  </div>
</footer>


<script type="text/javascript">	jQuery(function () { jQuery('.nav-tabs a').click(function (e) { e.preventDefault();
	jQuery(this).tab('show'); }) });

</script>


</body></html>