<div class="card">
	<h1><?= $title ?></h1>

	<div style="margin-bottom: 1rem;">
		<a href="<?= base_url('users') ?>" class="btn">&laquo; Back to Users</a>
	</div>

	<?= form_open("users/update/$id"); ?>
	<div class="form-group">
		<label for="username">Username</label>
		<input type="text" name="username" id="username" value="<?= set_value('username'); ?>" required>
		<?= form_error('username', '<div style="color: red; margin-top: 0.25rem;">', '</div>'); ?>
	</div>

	<div class="form-group">
		<label for="password">Password</label>
		<input type="password" name="password" id="password">
		<small style="display: block; margin-top: 0.25rem; color: #666;">Leave empty to keep current password</small>
		<?= form_error('password', '<div style="color: red; margin-top: 0.25rem;">', '</div>'); ?>
	</div>

	<div class="form-group">
		<label for="confirm_password">Confirm Password</label>
		<input type="password" name="confirm_password" id="confirm_password">
		<?= form_error('confirm_password', '<div style="color: red; margin-top: 0.25rem;">', '</div>'); ?>
	</div>

	<div class="form-group">
		<label for="role">Role</label>
		<select name="role" id="role" required>
			<option selected hidden>Select Role</option>
			<option value="user">User</option>
			<option value="editor">Editor</option>
			<option value="admin">Admin</option>
		</select>
		<?= form_error('role', '<div style="color: red; margin-top: 0.25rem;">', '</div>'); ?>
	</div>

	<button type="submit" class="btn">Update User</button>
	<?= form_close(); ?>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {

		const usernameField = document.getElementById('username');
		const roleField = document.getElementById('role');
		usernameField.disabled = true;
		roleField.disabled = true;
		usernameField.placeholder = "Loading...";


		fetch('<?= base_url("api/users/$id") ?>')
			.then(response => {
				if (!response.ok) {
					throw new Error('Failed to load user data. Status: ' + response.status);
				}
				return response.json();
			})
			.then(data => {
				if (data.status === true && data.data) {

					populateUserForm(data.data);
				} else {

					alert(data.message || 'Failed to load user data');
					window.location.href = '<?= base_url("users") ?>';
				}
			})
			.catch(error => {
				console.error('Error:', error);
				alert('Error loading user data: ' + error.message);
				window.location.href = '<?= base_url("users") ?>';
			})
			.finally(() => {

				usernameField.disabled = false;
				roleField.disabled = false;
				usernameField.placeholder = "";
			});
	});

	function populateUserForm(userData) {

		document.getElementById('username').value = userData.username || '';


		const roleSelect = document.getElementById('role');
		for (let i = 0; i < roleSelect.options.length; i++) {
			if (roleSelect.options[i].value === userData.role) {
				roleSelect.options[i].selected = true;
				break;
			}
		}
	}

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
		e.preventDefault

		let username = document.getElementById('username').value.trim();
		let password = document.getElementById('password').value;
		let confirmPassword = document.getElementById('confirm_password').value;
		let role = document.getElementById('role').value;
		let hasError = false;


		function showError(inputId, message) {

			let errorDiv = document.querySelector(`#${inputId} + div[style="color: red; margin-top: 0.25rem;"]`);

			if (!errorDiv) {

				if (inputId === 'password') {
					errorDiv = document.querySelector(`#${inputId} + small + div[style="color: red; margin-top: 0.25rem;"]`);
				}

				if (!errorDiv) {

					errorDiv = document.createElement('div');
					errorDiv.style.color = 'red';
					errorDiv.style.marginTop = '0.25rem';


					const inputElement = document.getElementById(inputId);
					if (inputId === 'password') {

						inputElement.parentNode.insertBefore(errorDiv, inputElement.nextElementSibling.nextSibling);
					} else {
						inputElement.parentNode.insertBefore(errorDiv, inputElement.nextSibling);
					}
				}
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


		if (password) {

			if (password.length < 6) {
				showError('password', 'Password must be at least 6 characters');
				hasError = true;
			}


			if (password !== confirmPassword) {
				showError('confirm_password', 'Passwords do not match');
				hasError = true;
			}
		} else if (confirmPassword) {

			showError('password', 'Please enter a password');
			hasError = true;
		}


		if (role === 'Select Role') {
			showError('role', 'Please select a role');
			hasError = true;
		}

		if (hasError) {
			return false;
		}


		const userId = <?= $id ?>;

		fetch(`<?= base_url('api/users') ?>/${userId}`, {
				method: 'PUT',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({
					username: username,
					password: password || null,
					confirmPassword: confirmPassword || null,
					role: role
				})
			})
			.then(response => {
				if (!response.ok) {
					return response.json().then(data => {
						throw new Error(data.message || 'Failed to update user');
					});
				}
				return response.json();
			})
			.then(data => {
				if (data.status) {
					alert('User updated successfully');
					window.location.href = '<?= base_url('users') ?>';
				} else {
					alert(data.message || 'User update failed');
				}
			})
			.catch(error => {
				console.error('Error:', error);
				alert(error.message || 'An error occurred while updating the user');
			});
	});
</script>
