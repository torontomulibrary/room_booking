<!DOCTYPE html>
<html class="" lang="en">

<head>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Ryerson University">
    <meta property="og:url" content="https://www.ryerson.ca/fcs/programs/">
    <meta property="og:title" content="Programs">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" value="https://www.ryerson.ca/fcs/programs/">
    <meta name="twitter:site" content="@RyersonU">
    <meta name="twitter:title" content="Programs">

    <title>Faculty of Community Services - Ryerson University</title>

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/template/fcs_2019/fcs_assets/clientlib-all.css" type="text/css">
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/template/fcs_2019/fcs_assets/clientlib-all.js"></script>

    

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/template/fcs_2019/fcs_assets/color.css" type="text/css">

  

	<?php if(isset($headers)) echo $headers; ?>
	
	
    <body>

        <div class="resGlobalHeader">

            <div class="global-header-top">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="res-global-header">
                                <a id="skip-to-main" class="btn btn-default" href="#main-content">Skip to Main Content</a>
                                <nav aria-label="global" class="navbar navbar-default">
                                    <div class="container-fluid">
                                        <div class="navbar-header">
                                            <a class="navbar-brand" href="https://www.ryerson.ca/"> <img src="<?php echo base_url(); ?>assets/template/fcs_2019/fcs_assets/ryerson_logo.svg" alt="Ryerson University" title="Ryerson University">
                                            </a>
                                            <button id="gh-other-btn" type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#gh-other-content" aria-expanded="false">
                                                <span class="sr-only">Toggle navigation</span> <span class="fa fa-bars"></span>
                                            </button>
                                            <button id="gh-info-btn" type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#gh-info-content" aria-expanded="false">
                                                <span class="sr-only">Toggle information</span> <span class="fa fa-info-circle"></span>
                                            </button>
                                            <button id="gh-search-btn" type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#gh-search-content" aria-expanded="false">
                                                <span class="sr-only">Toggle search</span> <span class="fa fa-search"></span>
                                            </button>
                                        </div>
                                        <div class="navbar-content">
                                            <form role="search" id="gh-search-content" class="resSearch nav navbar-nav collapse navbar-collapse navbar-form" method="GET" action="https://www.ryerson.ca/search/">
                                                <div class="input-group gh-search-container">
                                                    <label for="search" class="sr-only">Search Site and People</label>
                                                    <input id="search" name="q" type="text" class="form-control" placeholder="Search Site and People" autocomplete="off">
                                                    <span class="input-group-btn">
											<button aria-label="Search" class="btn btn-default" type="submit">
												<i class="fa fa-search" title="Search Ryerson.ca"></i>
											</button>
										</span>
                                                </div>
                                            </form>
                                            <div class="navbar-right">
                                                <ul id="gh-info-content" class="nav navbar-nav collapse navbar-collapse navbar-left" role="presentation">
                                                    <li class="dropdown hidden-xs">
                                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <span class="fa fa-info-circle"></span><span class="hidden-sm">
													Info for <span class="caret"></span>
                                                            </span>
                                                        </a>
                                                        <ul class="dropdown-menu">
                                                            <li><a href="https://www.ryerson.ca/future-students/">Future Students</a></li>
                                                            <li><a href="https://www.ryerson.ca/current-students/">Current Students</a></li>
                                                            <li><a href="https://www.ryerson.ca/faculty-staff/">Faculty and Staff</a></li>
                                                            <li><a href="https://www.ryerson.ca/teachers-counsellors/">Teachers and
														Counsellors</a></li>
                                                            <li><a href="https://www.ryerson.ca/alumni/">Alumni</a></li>
                                                            <li><a href="https://www.ryerson.ca/media/">Media</a></li>
                                                            <li><a href="https://www.ryerson.ca/giving/">Donors</a></li>
                                                        </ul>
                                                    </li>
                                                    <li class="visible-xs"><a href="https://www.ryerson.ca/future-students/">Future
												Students</a></li>
                                                    <li class="visible-xs"><a href="https://www.ryerson.ca/current-students/">Current
												Students</a></li>
                                                    <li class="visible-xs"><a href="https://www.ryerson.ca/faculty-staff/">Faculty
												and Staff</a></li>
                                                    <li class="visible-xs"><a href="https://www.ryerson.ca/teachers-counsellors/">Teachers
												and Counsellors</a></li>
                                                    <li class="visible-xs"><a href="https://www.ryerson.ca/alumni/">Alumni</a></li>
                                                    <li class="visible-xs"><a href="https://www.ryerson.ca/media/">Media</a></li>
                                                    <li class="visible-xs"><a href="https://www.ryerson.ca/giving/">Donors</a></li>
                                                </ul>
                                                <ul id="gh-other-content" class="nav navbar-nav collapse navbar-collapse navbar-left">
                                                    <li class="visible-xs"><a href="https://www.ryerson.ca/about/">About</a></li>
                                                    <li class="visible-xs"><a href="https://www.ryerson.ca/programs/">Programs</a></li>
                                                    <li class="visible-xs"><a href="https://www.ryerson.ca/admissions/">Admissions</a></li>
                                                    <li class="visible-xs"><a href="https://www.ryerson.ca/campus-life/">Campus
												Life</a></li>
                                                    <li class="visible-xs"><a href="https://www.ryerson.ca/research/">Research &amp; Innovation</a></li>
                                                    <li class="divider visible-xs"></li>
                                                    <li class="call-to-action"><a href="https://www.ryerson.ca/admissions/">Apply</a></li>
                                                    <li class="call-to-action"><a href="https://www.ryerson.ca/admissions/visits-tours/">Visit</a></li>
                                                    <li class="call-to-action"><a href="https://www.ryerson.ca/giving/">Give</a></li>
                                                    <li class="divider"></li>
                                                    <li><a href="http://my.ryerson.ca/">my.ryerson</a></li>
                                                </ul>

                                                <ul class="nav navbar-nav collapse navbar-collapse navbar-left" role="presentation">
                                                    <li class="dropdown hidden-xs">
                                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="button"> <span class="fa fa-bars"></span><span class="sr-only">Menu</span>
                                                        </a>
                                                        <ul class="dropdown-menu">

                                                            <li><a href="https://www.ryerson.ca/about/">About</a></li>
                                                            <li><a href="https://www.ryerson.ca/programs/">Programs</a></li>
                                                            <li><a href="https://www.ryerson.ca/admissions/">Admissions</a></li>
                                                            <li><a href="https://www.ryerson.ca/campus-life/">Campus Life</a></li>
                                                            <li><a href="https://www.ryerson.ca/research/">Research &amp; Innovation</a></li>
                                                        </ul>
                                                    </li>
                                                </ul>

                                            </div>
                                        </div>
                                    </div>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="res-local-header-wrapper" role="banner">
            <div class="container">
                <div class="resLocalHeader">

                    <div class="res-local-header">
                        <div class="row template-local-header">
                            <div class="local-header-section col-xs-12">

                                <div class="res-local-header">
                                    <div class="row template-local-header">
                                        <div class="local-header-section col-xs-12">

                                            <div class="site-heading">

                                                <a href="https://www.ryerson.ca/fcs/">
				        Faculty of Community Services
				    </a>

                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="resHorizontalNavigation">

            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <nav aria-label="primary" class="navbar res-horizontal-navigation">
                            <!-- Navigation Header -->
                            <div class="navbar-header">
                                <span class="navbar-brand visible-xs">

                        <a href="https://www.ryerson.ca/content/ryerson/fcs.html">Home</a>

                    </span>
                                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#706ec779-c5c6-40b3-9901-99bc27e5cdf3" aria-expanded="false">
                                    <span class="sr-only">Toggle navigation</span>
                                    <i class="fa fa-chevron-down"></i>
                                </button>
                            </div>
                            <!-- Navigation Links -->
                            <div class="collapse navbar-collapse" id="706ec779-c5c6-40b3-9901-99bc27e5cdf3">
                                <ul class="nav navbar-nav">

                                    <li class="">
                                        <a href="https://www.ryerson.ca/fcs/about/">
											About
										</a>
                                    </li>

                                    <li class="">
                                        <a href="https://www.ryerson.ca/fcs/programs/">
											Programs
										</a>
                                    </li>

                                    <li class="">
                                        <a href="https://www.ryerson.ca/fcs/student-success/">
											Student Success
										</a>
                                    </li>

                                    <li class="">
                                        <a href="https://www.ryerson.ca/fcs/learning-and-teaching/">
											Learning and Teaching
										</a>
                                    </li>

                                    <li class="">
                                        <a href="https://www.ryerson.ca/fcs/research/">
											Research
										</a>
                                    </li>

                                    <li class="">
                                        <a href="https://www.ryerson.ca/fcs/people/">
											People
										</a>
                                    </li>

                                    <li class="">
                                        <a href="https://www.ryerson.ca/fcs/contact/">
											Contact
										</a>
                                    </li>

                                    <li class="">
                                        <a href="https://www.ryerson.ca/fcs/news-events1/">
											News and Events
										</a>
                                    </li>

                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">

            <div class="row template-content">

                <main id="mainContent" class="content-area-section col-md-12 col-sm-12">

                  <?php if(isset($breadcrumbs)):?>
		   <div class="breadcrumbs">
				 <a href="<?php echo base_url();?>">Home</a> &#187; <a href="/about"><?php echo $breadcrumbs; ?>
			</div>
		<?php endif; ?>
		
		  <?php if(isset($content)) echo $content; ?>

                </main>

            </div>
        </div>

        <footer class="res-local-footer-wrapper" aria-label="local">
            <div class="container">
                <div class="resLocalFooter">

                    <div class="res-local-footer">
                        <div class="row template-local-footer">
                            <div class="col-xs-12">

                                <div class="content stackparsys">

                                    <div class="resTwoColEven section">

                                        <div class="row">

                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <div class="stackparsys">

                                                    <div class="resText parbase section">

                                                        <div class="res-text richtextContent background-hidden" id="373f819d-d0eb-4370-bcc6-a970b87b9e8b">

                                                            <p><a href="https://www.ryerson.ca/content/ryerson/fcs.html">Faculty of Community Services</a></p>
                                                            <p>Sally Horsfall Eaton Centre for Studies in Community Health, Room SHE-697 (sixth floor)
                                                                <br> 99 Gerrard St. East
                                                                <br> Toronto, ON M5B 1G7</p>
                                                            <p><strong>Phone:</strong>&nbsp;<a href="tel:416-979-5000">416-979-5000</a>, ext. 5034
                                                                <br> <strong>Fax:</strong>&nbsp;<a href="tel:416-979-5384">416-979-5384</a></p>

                                                        </div>
                                                    </div>

                                                </div>

                                            </div>

                                            <div class="clearfix visible-xs-block"></div>

                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <div class="stackparsys">

                                                    <div class="resMap section">

                                                        <div class="res-map">

                                                            <div id="551345c1-3fd8-4498-8ad7-e879680868d0" aria-hidden="true">
                                                                <a target="_blank" href="https://www.google.com/maps/place/99%20Gerrard%20St%20E%2C%20Toronto%2C%20ON%2C%20M5B%202M2%2C%20Canada"><img class="img-responsive" src="<?php echo base_url(); ?>assets/template/fcs_2019/fcs_assets/staticmap.png"></a>
                                                            </div>
                                                            <div class="sr-only">Map of 99 Gerrard St E, Toronto, ON, M5B 2M2, Canada</div>


                                                        </div>
                                                    </div>

                                                </div>

                                            </div>

                                            <div class="clearfix visible-lg-block"></div>

                                            <div class="clearfix visible-md-block"></div>

                                            <div class="clearfix visible-sm-block"></div>

                                            <div class="clearfix visible-xs-block"></div>

                                        </div>
                                    </div>

                                    <div class="resFourColEven section">

                                        <div class="row">

                                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                                <div class="stackparsys">

                                                    <div class="resText parbase section">

                                                        <div class="res-text richtextContent background-hidden" id="016a4799-ed11-42a3-bf42-db4c9fd72381">

                                                            <h2><strong>Future Students</strong></h2>
                                                            <ul>
                                                                <li>
                                                                    <a></a><a href="https://www.ryerson.ca/content/ryerson/fcs/programs/undergraduate-programs.html">Explore our undergraduate programs</a></li>
                                                                <li>
                                                                    <a></a><a href="https://www.ryerson.ca/content/ryerson/fcs/programs/graduate-programs.html">Explore our graduate programs</a></li>
                                                            </ul>

                                                        </div>
                                                    </div>

                                                    <div class="resText parbase section">

                                                        <div class="res-text richtextContent background-hidden" id="98b9a931-949d-4cc6-8584-fd33da596b00">

                                                            <h2>Request a Web Update</h2>
                                                            <ul>
                                                                <li><a href="https://www.ryerson.ca/content/ryerson/fcs-news-events/events/submit-an-event.html">Submit an event</a></li>
                                                                <li><a href="https://www.ryerson.ca/content/ryerson/fcs-news-events/bulletin-board/submit-a-bulletin-board-item.html">Submit a bulletin item</a></li>
                                                            </ul>
                                                            <p>Send other web update requests to: <a href="mailto:fcs.website@ryerson.ca">fcs.website@ryerson.ca</a></p>

                                                        </div>
                                                    </div>

                                                </div>

                                            </div>

                                            <div class="clearfix visible-xs-block"></div>

                                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                                <div class="stackparsys">

                                                    <div class="resText parbase section">

                                                        <div class="res-text richtextContent background-hidden" id="24c72660-f201-4c98-b608-d1a2f115c41d">

                                                            <h2><strong>Current Students</strong></h2>
                                                            <ul>
                                                                <li><a href="https://www.ryerson.ca/content/ryerson/fcs/student-success/student-awards-and-grants.html">Student awards and grants</a></li>
                                                                <li><a href="https://www.ryerson.ca/content/ryerson/fcs/socialinnovation.html">Social Innovation</a></li>
                                                            </ul>

                                                        </div>
                                                    </div>

                                                </div>

                                            </div>

                                            <div class="clearfix visible-sm-block"></div>

                                            <div class="clearfix visible-xs-block"></div>

                                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                                <div class="stackparsys">

                                                    <div class="resText parbase section">

                                                        <div class="res-text richtextContent background-hidden" id="257966c9-bb16-4aff-9d7b-694d7eb0ca08">

                                                            <h2><strong>Faculty and Staff</strong></h2>
                                                            <ul>
                                                                <li><a href="https://www.ryerson.ca/content/ryerson/fcs/learning-and-teaching.html">Learning and teaching</a></li>
                                                                <li><a href="https://www.ryerson.ca/content/ryerson/fcs/research.html">Research</a></li>
                                                            </ul>

                                                        </div>
                                                    </div>

                                                </div>

                                            </div>

                                            <div class="clearfix visible-xs-block"></div>

                                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                                <div class="stackparsys">

                                                    <div class="resText parbase section">

                                                        <div class="res-text richtextContent background-hidden" id="6c6cdac7-e324-47e5-9977-bfaf4c30a6c0">

                                                            <h2><strong>Follow the Faculty of Community Services</strong></h2>

                                                        </div>
                                                    </div>

                                                    <div class="resSocialLinks section">

                                                        <div class="res-link-buttons text-right">

                                                            <ul class="list-inline">

                                                                <li>
                                                                    <a href="https://twitter.com/ryersonfcs" target="_blank">
                                                                        <i class="fa fa-twitter icon-circle" title="Twitter" aria-hidden="true"></i>
                                                                        <span class="sr-only">Twitter, opens new window</span>
                                                                    </a>
                                                                </li>

                                                                <li>
                                                                    <a href="https://www.youtube.com/playlist?list=PLAImK_PQ9xUfiS0lNbX_1VSew7IER9Uf3" target="_blank">
                                                                        <i class="fa fa-youtube icon-circle" title="YouTube" aria-hidden="true"></i>
                                                                        <span class="sr-only">YouTube, opens new window</span>
                                                                    </a>
                                                                </li>

                                                                <li>
                                                                    <a href="https://www.instagram.com/ryersonfcs/" target="_blank">
                                                                        <i class="fa fa-instagram icon-circle" title="Instagram" aria-hidden="true"></i>
                                                                        <span class="sr-only">Instagram, opens new window</span>
                                                                    </a>
                                                                </li>

                                                                <li>
                                                                    <a href="https://www.flickr.com/photos/145471556@N06/albums" target="_blank">
                                                                        <i class="fa fa-flickr icon-circle" title="Flickr" aria-hidden="true"></i>
                                                                        <span class="sr-only">Flickr, opens new window</span>
                                                                    </a>
                                                                </li>

                                                            </ul>

                                                        </div>
                                                    </div>

                                                </div>

                                            </div>

                                            <div class="clearfix visible-lg-block"></div>

                                            <div class="clearfix visible-md-block"></div>

                                            <div class="clearfix visible-sm-block"></div>

                                            <div class="clearfix visible-xs-block"></div>

                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </footer>

        <div class="resGlobalFooter">

            <footer class="resGlobalFooter" aria-label="global">
                <div class="top-middle-container">
                    <!-- top-middle-container starts -->
                    <div class="container">

                        <!-- Footer Top -->
                        <div class="row footer-top">
                            <div class="col-sm-9 col-xs-6">
                                <strong>Ryerson University</strong>
                            </div>
                            <div class="col-sm-3 col-xs-6 text-right footer-toggle">
                                <a title="Expand/Hide Footer" data-toggle="collapse" href="#footer-data" aria-label="expand or hide footer" aria-expanded="false" aria-controls="footer-data" class="footer-toggle-button">
                                </a>
                            </div>
                        </div>

                        <!-- Footer Middle -->
                        <div class="row collapse in footer-middle" id="footer-data">
                            <div class="col-sm-9 col-xs-6 text-left">
                                <address>
		            350 Victoria Street<br>
		            Toronto, ON M5B 2K3<br>
		            <abbr title="Phone">P:</abbr> <a href="tel:+14169795000">(416) 979-5000</a>
		        </address>
                                <ol class="breadcrumb min">
                                    <li><a href="https://www.ryerson.ca/contact/">Directory</a></li>
                                    <li><a href="https://www.ryerson.ca/maps/">Maps and Directions</a></li>
                                </ol>
                            </div>
                            <div class="col-sm-3 col-xs-6 text-right">
                                <p>Follow Ryerson</p>
                                <p>
                                    <a href="https://www.facebook.com/ryersonu" target="_blank"><i class="fa fa-facebook-f icon-circle" title="Facebook" aria-hidden="true"></i><span class="sr-only">Facebook, opens new window</span></a>
                                    <a href="https://www.instagram.com/ryerson_u" target="_blank"><i class="fa fa-instagram icon-circle" title="Instagram" aria-hidden="true"></i><span class="sr-only">Instagram, opens new window</span></a>
                                    <a href="https://twitter.com/RyersonU" target="_blank"><i class="fa fa-twitter icon-circle" title="Twitter" aria-hidden="true"></i><span class="sr-only">Twitter, opens new window</span></a>
                                    <a href="https://www.youtube.com/RyersonUTube" target="_blank"><i class="fa fa-youtube icon-circle" title="YouTube" aria-hidden="true"></i><span class="sr-only">YouTube, opens new window</span></a>
                                    <a href="https://www.linkedin.com/company/ryerson-university" target="_blank"><i class="fa fa-linkedin icon-circle" title="LinkedIn" aria-hidden="true"></i><span class="sr-only">LinkedIn, opens new window</span></a>
                                </p>
                                <ol class="breadcrumb min">
                                    <li><a href="https://www.ryerson.ca/jobs/">Careers</a></li>
                                    <li><a href="https://www.ryerson.ca/media/">Media Room</a></li>
                                </ol>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- top-middle-container ends -->

                <div class="bottom-container">
                    <!-- bottom-container starts -->
                    <div class="container">

                        <!-- Footer Bottom -->
                        <div class="row footer-bottom">
                            <div class="col-xs-12">
                                <ol class="breadcrumb min">
                                    <li><a href="https://www.ryerson.ca/privacy/">Privacy Policy</a></li>
                                    <li><a href="https://www.ryerson.ca/accessibility/">Accessibility</a></li>
                                    <li><a href="https://www.ryerson.ca/terms-conditions/">Terms &amp; Conditions</a></li>
                                </ol>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- bottom-container ends -->
            </footer>
        </div>

    </body>

</html>