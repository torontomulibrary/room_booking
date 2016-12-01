<!DOCTYPE html>
<html dir="ltr"
prefix="content: http://purl.org/rss/1.0/modules/content/ dc: http://purl.org/dc/terms/ foaf: http://xmlns.com/foaf/0.1/ og: http://ogp.me/ns# rdfs: http://www.w3.org/2000/01/rdf-schema# sioc: http://rdfs.org/sioc/ns# sioct: http://rdfs.org/sioc/types# skos: http://www.w3.org/2004/02/skos/core# xsd: http://www.w3.org/2001/XMLSchema#"
class="js" lang="en">
  <head>
    <link rel="profile" href="http://www.w3.org/1999/xhtml/vocab" />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="shortcut icon" href="//ryersonperformance.ca/sites/default/files/favicon.ico.jpg" type="image/jpeg" />
    <link rel="canonical" href="https://ryersonperformance.ca/" />
    <link rel="shortlink" href="https://ryersonperformance.ca/" />
    <title>School of Performance | Ryerson University |</title>
   
	<link href="<?= base_url() ?>assets/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="<?= base_url() ?>assets/js/bootstrap.min.js"></script>   

    <link type="text/css" rel="stylesheet" href="<?php echo base_url()?>assets/template/performance/css/performance_theme.css" media="all" />

	
	<style>
		#page{
			max-width: 960px;
			margin: 0 auto;
			background-color: #ffffff;
			padding-bottom: 75px;
		}
		
		#content_container{
			width: 94%;
			margin: 0 auto;
		}
		
		body{
			background-color: #333333;
		}
	</style>
	

	
	<?php if(isset($headers)) echo $headers; ?>
  </head>
  <body class="html front not-logged-in no-sidebars page-node navbar-is-fixed-top">
    <div id="skip-link">
      <a href="#main-content" class="sr-only sr-only-focusable">Skip to main content</a>
    </div>

    <header style="border: none" id="navbar" role="banner" class="navbar navbar-default">
      <div class="container-fluid animated bounceInDown col-xs-12 col-lg-8 col-lg-offset-2">
        <div class="navbar-header">
        <a class="logo navbar-btn pull-left" href="//ryersonperformance.ca/" title="Home">
          <img src="<?php echo base_url()?>assets/template/performance/images/schoolofperformancebrandmark-centrewhite.png" alt="Home" />
        </a> 
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span> </button></div>
        <div class="navbar-collapse collapse main-nav">
          <nav role="navigation">
            <ul class="menu nav navbar-nav">
              <li class="first leaf hidden-md">
                <a href="https://ryersonperformance.ca/" class="active">Home</a>
              </li>
              <li class="expanded">
                <a href="https://ryersonperformance.ca/about">About</a>
              </li>
              <li class="expanded">
                <a href="https://ryersonperformance.ca/programs">Programs</a>
              </li>
              <li class="collapsed">
                <a href="https://ryersonperformance.ca/shows" title="">Shows</a>
              </li>
              <li class="leaf">
                <a href="https://ryersonperformance.ca/student-work">Student Work</a>
              </li>
              <li class="last expanded">
                <a href="https://ryersonperformance.ca/summer-programs">Summer Programs</a>
                <ul class="dropdown-sub-menu hidden">
                  <li class="first collapsed">
                    <a href="https://ryersonperformance.ca/summer-programs/about">About</a>
                  </li>
                  <li class="last collapsed">
                    <a href="https://ryersonperformance.ca/summer-programs/summer-programs">Programs</a>
                  </li>
                </ul>
              </li>
            </ul>
          </nav>
        </div>
        <div class="header-button pull-right">
          <div class="region region-header-button">
            <section id="block-block-11" class="block block-block social-icons-header hidden-xs hidden-sm clearfix">
              <div class="btn-group">
              <a alt="Follow Ryerson School of Performance on Twitter" href="https://twitter.com/RUPerformance" target="_blank">
                <i class="fa fa-twitter fa-lg"></i>
              </a> 
              <a alt="Follow Ryerson School of Performance on Instagram" href="https://www.instagram.com/ruperformance/"
              target="_blank">
                <i class="fa fa-instagram fa-lg"></i>
              </a> 
              <a alt="Friend Ryerson School of Performance on Facebook" href="https://www.facebook.com/ryersonschoolofperformance"
              target="_blank">
                <i class="fa fa-facebook fa-lg"></i>
              </a></div>
            </section>
          </div>
        </div>
      </div>
    </header>
    <div id="page">
	<?php if(isset($breadcrumbs)):?>
		<div class="breadcrumbs">
			<a href="<?php echo base_url();?>">Home</a> &#187; <a href="/about"><?php echo $breadcrumbs; ?>
		</div>
	<?php endif; ?>
		
	 <?php if(isset($content)) echo '<div id="content_container">'.$content .'</div>'; ?>
	</div>
	
	<footer class="footer container-fluid">
      <div class="region region-footer">
        <section id="block-block-13" class="block block-block col-lg-8 col-lg-offset-2 clearfix">
          <div class="row">
            <div class="col-sm-8">
              <div class="col-sm-4">
                <p>
                <strong>Building Location</strong>
                <br />20 Dundas St West
                <br />9th Floor
                <br />Toronto
                <br />416-979-5086
                <br />
                <a alt="View interactive campus map" href="http://www.ryerson.ca/maps/index.html" target="_blank">Interactive Campus Map</a></p>
              </div>
              <div class="col-sm-4">
                <p>
                <strong>Mailing Address</strong>
                <br />350 Victoria Street
                <br />Toronto, Ontario
                <br />M5B 2K3</p>
              </div>
              <div class="col-sm-4">
                <p>
                <strong>Box Office/Studios</strong>
                <br />Student Learning Center
                <br />345 Yonge St
                <br />Toronto, Ontario
                <br />M5M 3G5
                <br />416-979-5118
                <br />
                <a href="mailto:performanceinfo@ryerson.ca">performanceinfo@ryerson.ca</a></p>
                <p>
                <b>Ryerson Theatre</b>
                <br />43 Gerrard Street East
                <br />Toronto, Ontario</p>
              </div>
            </div>
            <div class="col-sm-3 col-sm-offset-1">
              <p style="padding: 10px 20px;">
                <img alt="Ryerson School of Performance Logo" src="<?php echo base_url()?>assets/template/performance/images/rsp_logo.png" style="padding: 10px" />
                <br />
                <a alt="Visit Ryerson University&#39;s homepage" href="http://www.ryerson.ca/" target="_blank">
                  <img alt="Ryerson University / FCAD logo" src="<?php echo base_url()?>assets/template/performance/images/ryerson_fcad_logo.png" />
                </a>
              </p>
            </div>
          </div>
          <div class="footer-menu">
            <a href="https://ryersonperformance.ca/">Home</a>
            <a href="https://ryersonperformance.ca/about">About</a>
            <a href="https://ryersonperformance.ca/programs">Programs</a>
            <a href="https://ryersonperformance.ca/shows">Shows</a>
            <a href="https://ryersonperformance.ca/student-work">Student Work</a>
            <a href="https://ryersonperformance.ca/summer-programs">Summer Programs</a>
          </div>
        </section>
      </div>
    </footer>
    
    
    
  </body>
</html>
