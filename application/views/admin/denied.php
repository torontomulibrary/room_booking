<?php ob_start();?>


<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<?php ob_start();?>

<?= $this->session->flashdata('message'); ?>



<!---
	Create a table listing all of the existing roles, and options available
	for each role. This only appears when not editing/creating new roles
--->
<div class="alert alert-danger" role="alert">
<h2>You do not have sufficient privileges to view this page</h2>

<p>If you need something changed here, please contact the site administrator (<a href="mailto:<?php echo SITE_ADMIN; ?>"><?php echo SITE_ADMIN; ?></a>)</p>
</div>

<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>