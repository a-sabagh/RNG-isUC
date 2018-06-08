<?php
if(!current_user_can("manage_options"))
    return;
if(isset($_GET['settings-updated']) and $_GET['settings-updated'] == TRUE){
	add_settings_error( "uc-settings", "uc-settings", __("Settings Updated","rng-isuc"), "updated" );
}elseif(isset($_GET['settings-updated']) and $_GET['settings-updated'] == FALSE){
	add_settings_error( "uc-settings", "uc-settings", __("Error with Updating","rng-isuc") );
}
?>
<div class="wrap">
<h1><?php echo get_admin_page_title(); ?></h1>
	<form action="options.php" method="post">
		<?php 
		settings_fields("uc-settings");
		do_settings_sections("uc-settings");
		submit_button(__("save","rng-isuc"));
		?>
	</form>
</div>