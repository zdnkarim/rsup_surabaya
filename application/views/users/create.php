<div class="card">
	<h1><?= $title ?></h1>

	<div style="margin-bottom: 1rem;">
		<a href="<?= base_url('users') ?>" class="btn">&laquo; Back to Users</a>
	</div>

	<?= form_open('users/create'); ?>
	<div class="form-group">
		<label for="username">Username</label>
		<input type="text" name="username" id="username" value="<?= set_value('username'); ?>" required>
		<?= form_error('username', '<div style="color: red; margin-top: 0.25rem;">', '</div>'); ?>
	</div>

	<div class="form-group">
		<label for="password">Password</label>
		<input type="password" name="password" id="password" required>
		<?= form_error('password', '<div style="color: red; margin-top: 0.25rem;">', '</div>'); ?>
	</div>

	<div class="form-group">
		<label for="confirm_password">Confirm Password</label>
		<input type="password" name="confirm_password" id="confirm_password" required>
		<?= form_error('confirm_password', '<div style="color: red; margin-top: 0.25rem;">', '</div>'); ?>
	</div>

	<div class="form-group">
		<label for="role_id">Role</label>
		<select name="role" id="role" required>
			<option selected hidden>Select Role</option>
			<option value="user">User</option>
			<option value="editor">Editor</option>
			<option value="admin">Admin</option>

		</select>
		<?= form_error('role', '<div style="color: red; margin-top: 0.25rem;">', '</div>'); ?>
	</div>

	<button type="submit" class="btn">Create User</button>
	<?= form_close(); ?>
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

	document.querySelector('form').addEventListener('submit', function(e) {
		let username = document.getElementById('username').value.trim();
		let password = document.getElementById('password').value;
		let confirmPassword = document.getElementById('confirm_password').value;
		let role = document.getElementById('role').value;
		let hasError = false;

		function showError(inputId, message) {
			let errorDiv = document.querySelector(`#${inputId} + div[style="color: red; margin-top: 0.25rem;"]`);

			if (!errorDiv) {
				errorDiv = document.createElement('div');
				errorDiv.style.color = 'red';
				errorDiv.style.marginTop = '0.25rem';

				const inputElement = document.getElementById(inputId);
				inputElement.parentNode.insertBefore(errorDiv, inputElement.nextSibling);
			}

			errorDiv.innerHTML = message;
		}

		document.querySelectorAll('div[style="color: red; margin-top: 0.25rem;"]').forEach(function(el) {
			el.innerHTML = '';
		});

		if (!username) {
			showError('username', 'Username is required');
			hasError = true;
		} else if (username.length < 3 || username.length > 20) {
			showError('username', 'Username must be between 3 and 20 characters');
			hasError = true;
		}

		if (!password) {
			showError('password', 'Password is required');
			hasError = true;
		} else if (password.length < 6) {
			showError('password', 'Password must be at least 6 characters');
			hasError = true;
		}

		if (password !== confirmPassword) {
			showError('confirm_password', 'Passwords do not match');
			hasError = true;
		}

		if (role === 'Select Role') {
			showError('role', 'Please select a role');
			hasError = true;
		}

		if (hasError) {
			e.preventDefault();
			return false;
		}

		fetch('<?= base_url('api/users') ?>', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({
					username: username,
					password: password,
					confirmPassword: confirmPassword,
					role: role
				})
			})
			.then(response => response.json())
			.then(data => {
				if (data.status) {
					alert('User created successfully');
					window.location.href = '<?= base_url('users') ?>';
				} else {
					alert(data.message || 'User creation failed');
				}
			})
			.catch(error => {
				alert('An error occurred: ' + error.message);
			});
		e.preventDefault();
	});
</script>
</body>

</html>
