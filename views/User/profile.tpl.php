<h1>User Profile</h1>
<?php
if($is_authenticated)
{
?>
<?=$profile_form?>
<?php } else { ?>
<p>You need to be logged in to view your profile.</p>
<?php } ?>

