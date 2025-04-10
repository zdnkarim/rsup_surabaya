<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Login - RSUP Surabaya</title>
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>

	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			line-height: 1.6;
			color: #333;
			background-color: #f5f5f5
		}

		.container {
			max-width: 400px;
			margin: 50px auto;
			padding: 20px;
			background-color: #fff;
			border-radius: 8px;
			box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
		}

		.logo {
			text-align: center;
			margin-bottom: 20px;
		}

		.logo h1 {
			color: #007bff;
		}

		.form-group {
			margin-bottom: 15px;
		}

		input {
			width: 100%;
			padding: 10px;
			border: 1px solid #ccc;
			border-radius: 4px;
		}

		.btn-container {
			display: flex;
			align-items: center;
			justify-content: center;
		}

		.btn {
			background-color: #007bff;
			width: 100%;
			color: white;
			padding: 10px 15px;
			border: none;
			border-radius: 4px;
			cursor: pointer;
		}

		.btn:hover {
			background-color: #0056b3;
		}

		.alert {
			padding: 10px;
			margin-bottom: 15px;
			border-radius: 4px;
		}

		.alert-danger {
			background-color: #f8d7da;
			color: #721c24;
		}

		.alert-success {
			background-color: #d4edda;
			color: #155724;
		}

		.validation-error {
			color: #dc3545;
			font-size: 0.875em;
			margin-top: 5px;
		}
	</style>
</head>

<body>
	<div class="container">
		<div class="logo">
			<h1>RSUP Surabaya</h1>
			<p>Technical Test Full Stack Developer</p>
		</div>

		<div id="alert-container">
			<?php if ($this->session->flashdata('error')) : ?>
				<div class="alert alert-danger">
					<?= $this->session->flashdata('error'); ?>
				</div>
			<?php endif; ?>

			<?php if ($this->session->flashdata('success')) : ?>
				<div class="alert alert-success">
					<?= $this->session->flashdata('success'); ?>
				</div>
			<?php endif; ?>
		</div>

		<form id="login-form">
			<div class="form-group">
				<label for="username">Username</label>
				<input type="text" id="username" name="username" required>
				<div class="validation-error" id="username-error"></div>
			</div>

			<div class="form-group">
				<label for="password">Password</label>
				<input type="password" id="password" name="password" required>
				<div class="validation-error" id="password-error"></div>
			</div>

			<div class="form-group">
				<div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></div>
				<div class="validation-error" id="recaptcha-error"></div>
			</div>

			<div class="btn-container">
				<button type="submit" class="btn btn-primary" id="login-btn">Login</button>
				<div class="spinner" id="login-spinner"></div>
			</div>
		</form>
	</div>

	<script>
		document.getElementById('login-form').addEventListener('submit', function(event) {
			event.preventDefault();

			const username = document.getElementById('username').value;
			const password = document.getElementById('password').value;
			const recaptchaResponse = grecaptcha.getResponse();
			let hasError = false;

			document.getElementById('username-error').innerText = '';
			document.getElementById('password-error').innerText = '';
			document.getElementById('recaptcha-error').innerText = '';

			document.getElementById('alert-container').innerHTML = '';

			if (!username.trim()) {
				document.getElementById('username-error').innerText = 'Username is required.';
				hasError = true;
			}
			if (username.length <= 3 || username.length >= 20) {
				document.getElementById('username-error').innerText = 'Username must be between 3 and 20 characters.';
				hasError = true;
			}

			if (!password.trim()) {
				document.getElementById('password-error').innerText = 'Password is required.';
				hasError = true;
			}
			if (password.length < 6) {
				document.getElementById('password-error').innerText = 'Password must be at least 6 characters.';
				hasError = true;
			}

			if (!recaptchaResponse) {
				document.getElementById('recaptcha-error').innerText = 'Please complete the reCAPTCHA.';
				hasError = true;
			}

			if (hasError) {
				return;
			}

			document.getElementById('login-spinner').style.display = 'block';
			document.getElementById('login-btn').disabled = true;

			fetch('<?= base_url('api/login') ?>', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					},
					body: JSON.stringify({
						username: username,
						password: password,
						recaptcha: recaptchaResponse
					})
				})
				.then(response => response.json())
				.then(data => {
					document.getElementById('login-spinner').style.display = 'none';
					document.getElementById('login-btn').disabled = false;

					if (data.status) {
						if (data.data) {
							localStorage.setItem('user', JSON.stringify(data.data));
						}

						const successDiv = document.createElement('div');
						successDiv.className = 'alert alert-success';
						successDiv.innerText = 'Login successful! Redirecting...';
						document.getElementById('alert-container').appendChild(successDiv);

						setTimeout(() => {
							window.location.href = '<?= base_url('dashboard') ?>';
						}, 1000);
					} else {
						grecaptcha.reset();

						const errorDiv = document.createElement('div');
						errorDiv.className = 'alert alert-danger';
						errorDiv.innerText = 'Login failed! Please check your username and password.';
						document.getElementById('alert-container').appendChild(errorDiv);
					}
				})
				.catch(error => {
					document.getElementById('login-spinner').style.display = 'none';
					document.getElementById('login-btn').disabled = false;

					grecaptcha.reset();

					const errorDiv = document.createElement('div');
					errorDiv.className = 'alert alert-danger';
					errorDiv.innerText = 'An error occurred. Please try again later.';
					document.getElementById('alert-container').appendChild(errorDiv);
				})
		})
	</script>
</body>

</html>
