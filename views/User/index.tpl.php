<h1>User Profile</h1>
<ul>
  <li><a href='<?=create_url(null, 'init')?>'>Init database, create tables and create default admin user</a>
  <li><a href='<?=create_url(null, 'login', 'root/root')?>'>Login as root:root (should work)</a>
  <li><a href='<?=create_url(null, 'login', 'root@laradrion.com/root')?>'>Login as root@laradrion.com:root (should work)</a>
  <li><a href='<?=create_url(null, 'login', 'doe/doe')?>'>Login as doe:doe (should work)</a>
  <li><a href='<?=create_url(null, 'login', 'doe@laradrion.com/doe')?>'>Login as doe@laradrion.com:doe (should work)</a>
  <li><a href='<?=create_url(null, 'login', 'admin/root')?>'>Login as admin:root (should fail, wrong akronym)</a>
  <li><a href='<?=create_url(null, 'login', 'root/admin')?>'>Login as admin:root (should fail, wrong password)</a>
  <li><a href='<?=create_url(null, 'login', 'admin@laradrion/root')?>'>Login as admin@laradrion.com:root (should fail, wrong email)</a>
  <li><a href='<?=create_url(null, 'logout')?>'>Logout</a>
</ul>
<p>This is what is known on the current user.</p>

<?php if($is_authenticated): ?>
  <p>User is authenticated.</p>
  <pre><?=print_r($user, true)?></pre>
<?php else: ?>
  <p>User is anonymous and not authenticated.</p>
<?php endif; ?>