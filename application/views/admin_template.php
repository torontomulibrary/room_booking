<?php $controller = $this->router->fetch_class() ?>
<?php $method = $this->router->fetch_method() ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- <link rel="icon" href="../../favicon.ico"> -->
	
	<!-- Bootstrap core CSS -->
	<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/css/normalize.css">
	<link href="<?= base_url() ?>assets/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="<?= base_url() ?>assets/js/bootstrap.min.js"></script>
	

    <?php if(isset($title)):?><title><?= $title?></title><?php endif;?>
	<?php if(isset($headers)) echo $headers; ?>

    

	<style type="text/css">
		/*
		 * Base structure
		 */

		/* Move down content because we have a fixed navbar that is 50px tall */
		body {
		  padding-top: 50px;
		}


		/*
		 * Global add-ons
		 */

		.sub-header {
		  padding-bottom: 10px;
		  border-bottom: 1px solid #eee;
		}

		/*
		 * Top navigation
		 * Hide default border to remove 1px line.
		 */
		.navbar-fixed-top {
		  border: 0;
		}

		/*
		 * Sidebar
		 */

		/* Hide for mobile, show later */
		.sidebar {
		  display: none;
		}
		@media (min-width: 768px) {
		  .sidebar {
			position: fixed;
			top: 51px;
			bottom: 0;
			left: 0;
			z-index: 1000;
			display: block;
			padding: 20px;
			overflow-x: hidden;
			overflow-y: auto; /* Scrollable contents if viewport is shorter than content. */
			background-color: #f5f5f5;
			border-right: 1px solid #eee;
		  }
		}

		/* Sidebar navigation */
		.nav-sidebar {
		  margin-right: -21px; /* 20px padding + 1px border */
		  margin-bottom: 20px;
		  margin-left: -20px;
		}
		.nav-sidebar > li > a {
		  padding-right: 20px;
		  padding-left: 20px;
		}
		.nav-sidebar > .active > a,
		.nav-sidebar > .active > a:hover,
		.nav-sidebar > .active > a:focus {
		  color: #fff;
		  background-color: #428bca;
		}


		/*
		 * Main content
		 */

		.main {
		  padding: 20px;
		}
		@media (min-width: 768px) {
		  .main {
			padding-right: 40px;
			padding-left: 40px;
		  }
		}
		.main .page-header {
		  margin-top: 0;
		}


		/*
		 * Placeholder dashboard ideas
		 */

		.placeholders {
		  margin-bottom: 30px;
		  text-align: center;
		}
		.placeholders h4 {
		  margin-bottom: 0;
		}
		.placeholder {
		  margin-bottom: 20px;
		}
		.placeholder img {
		  display: inline-block;
		  border-radius: 50%;
		}
		
		.chosen-search{
			display: none;
		}
		
		#codeigniter_profiler{
			z-index: 1001;
			display: block;
			margin-left:330px;
		}
		
		
	</style>
	
	
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?= base_url() ?>">Room Booking</a>
        </div>
		<div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="<?php echo base_url(); ?>login/logout">Logout</a></li>
          </ul>
        </div>
      </div>
    </div>

    <div class="container-fluid">
		<div class="row">
			<div class="col-sm-3 col-md-2 sidebar">
				<ul class="nav nav-sidebar">
					<li class="<?php echo ($method === "index" ? 'active' : ''); ?>"><a href="<?= base_url() ?>admin">Overview</a></li>
					<li class="<?php echo ($method === "reports" ? 'active' : ''); ?>"><a href="<?= base_url() ?>admin/reports">Reports</a></li>					
				</ul>
				
				<ul class="nav nav-sidebar">
					<li class="<?php echo ($method === "block_booking" ? 'active' : ''); ?>"><a href="<?= base_url() ?>admin/block_booking">Create Block Booking</a></li>
				</ul>
					
				<ul class="nav nav-sidebar">
					<li class="<?php echo ($method === "users" ? 'active' : ''); ?>"><a href="<?= base_url() ?>admin/users">Manage Users</a></li>         
					<li class="<?php echo ($method === "super_admin" ? 'active' : ''); ?>"><a href="<?= base_url() ?>admin/super_admin">Manage Super Users</a></li>         
				</ul>

				<ul class="nav nav-sidebar">
					<li class="<?php echo ($method === "rooms" ? 'active' : ''); ?>"><a href="<?= base_url() ?>admin/rooms">Manage Rooms</a></li>
					<li class="<?php echo ($method === "room_resources" ? 'active' : ''); ?>"><a href="<?= base_url() ?>admin/room_resources">Manage Room Resources</a></li>
					<li class="<?php echo ($method === "buildings" ? 'active' : ''); ?>"><a href="<?= base_url() ?>admin/buildings">Manage Buildings</a></li>
					<li class="<?php echo ($method === "roles" ? 'active' : ''); ?>"><a href="<?= base_url() ?>admin/roles">Manage Role Types</a></li>
				</ul>
				
				<ul class="nav nav-sidebar">
					<li class=""><a href="<?= base_url() ?>admin/clear_cache">Clear all cache</a></li>
				</ul>
			</div>

			
		</div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          
			
		  <?php if(isset($content)) echo $content; ?>
			
		</div>
		  
      </div>
    </div>

    
  </body>
</html>
