<div class="card">
	<h1>Welcome to RSUP Surabaya Dashboard</h1>
	<p>Hello, <strong><?= $username ?></strong>! You are logged in as
		<?php
		if ($role == 'admin') echo 'an Admin';
		else if ($role == 'editor') echo 'an Editor';
		else echo 'a User';
		?>
	</p>

	<div style="margin-top: 1.5rem;">
		<h2>Quick Links</h2>
		<?php if ($role == 'admin') : ?>
			<a href="<?= base_url('users') ?>" class="btn">Manage Users</a>
		<?php elseif ($role == 'editor') : ?>
			<a href="<?= base_url('users') ?>" class="btn">User Dashboard</a>
		<?php endif; ?>
		<?php if ($role == 'user') : ?>
			<a href="<?= base_url('articles') ?>" class="btn">Article Dashboard</a>
		<?php else : ?>
			<a href="<?= base_url('users') ?>" class="btn">Manage Articles</a>
		<?php endif; ?>

	</div>
</div>

</div>

<script>
	function logoutUser() {
		var xhr = new XMLHttpRequest();
		xhr.open('DELETE', '<?= base_url("api/logout") ?>', true);
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xhr.onreadystatechange = function() {
			if (xhr.readyState === 4) {
				try {
					var response = JSON.parse(xhr.responseText);
					if (response.status) {
						window.location.href = '<?= base_url() ?>';
					} else {
						alert(response.message || 'Logout failed');
					}
				} catch (e) {
					window.location.href = '<?= base_url() ?>';
				}
			}
		};
		xhr.send();
	}
</script>
</body>

</html>
