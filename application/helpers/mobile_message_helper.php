<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function print_message($header, $message){
	echo '
		<div class="ui-corner-all custom-corners">
			<div class="ui-bar ui-bar-a">
				<h3>'.$header.'</h3>
			</div>
			<div class="ui-body ui-body-a">
				<p>'.$message.'</p>
			</div>
		</div>
	';
}