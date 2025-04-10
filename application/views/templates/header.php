<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $title ?> - RSUP Surabaya</title>
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			font-family: Arial, sans-serif;
			line-height: 1.6;
			color: #333;
			background-color: #f5f5f5;
		}

		header {
			background-color: #1a73e8;
			color: #fff;
			padding: 1rem;
		}

		.navbar {
			display: flex;
			justify-content: space-between;
			align-items: center;
		}

		.logo {
			font-size: 1.5rem;
			font-weight: bold;
		}

		.nav-links {
			display: flex;
			list-style: none;
		}

		.nav-links li {
			margin-left: 1.5rem;
		}

		.nav-links a {
			color: #fff;
			text-decoration: none;
			transition: color 0.3s;
		}

		.nav-links a:hover {
			color: #ddd;
		}

		.container {
			max-width: 1200px;
			margin: 2rem auto;
			padding: 0 1rem;
		}

		.card {
			background-color: #fff;
			border-radius: 4px;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
			padding: 1.5rem;
			margin-bottom: 1.5rem;
		}

		.form-group {
			margin-bottom: 1rem;
		}

		label {
			display: block;
			margin-bottom: 0.5rem;
			font-weight: bold;
		}

		input[type="text"],
		input[type="password"],
		textarea,
		select {
			width: 100%;
			padding: 0.5rem;
			border: 1px solid #ddd;
			border-radius: 4px;
			font-size: 1rem;
		}

		textarea {
			min-height: 150px;
		}

		.btn {
			display: inline-block;
			padding: 0.5rem 1rem;
			background-color: #1a73e8;
			color: #fff;
			border: none;
			border-radius: 4px;
			cursor: pointer;
			text-decoration: none;
			font-size: 1rem;
			transition: background-color 0.3s;
		}

		.btn:hover {
			background-color: #1557b0;
		}

		.btn-danger {
			background-color: #dc3545;
		}

		.btn-danger:hover {
			background-color: #bd2130;
		}

		.btn-warning {
			background-color: #ffc107;
			color: #000;
		}

		.btn-warning:hover {
			background-color: #e0a800;
		}

		.btn-success {
			background-color: #28a745;
		}

		.btn-success:hover {
			background-color: #218838;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 1rem;
		}

		table th,
		table td {
			padding: 0.75rem;
			border-bottom: 1px solid #ddd;
			text-align: left;
		}

		table th {
			background-color: #f8f9fa;
			font-weight: bold;
		}

		.alert {
			padding: 0.75rem 1.25rem;
			margin-bottom: 1rem;
			border-radius: 4px;
		}

		.alert-success {
			background-color: #d4edda;
			color: #155724;
			border: 1px solid #c3e6cb;
		}

		.alert-danger {
			background-color: #f8d7da;
			color: #721c24;
			border: 1px solid #f5c6cb;
		}

		.pagination {
			display: flex;
			list-style: none;
			margin: 1rem 0;
		}

		.pagination li {
			margin-right: 0.5rem;
		}

		.pagination a {
			display: block;
			padding: 0.5rem 0.75rem;
			border: 1px solid #ddd;
			color: #1a73e8;
			text-decoration: none;
			border-radius: 4px;
		}

		.pagination .active a {
			background-color: #1a73e8;
			color: #fff;
			border-color: #1a73e8;
		}

		.search-form {
			display: flex;
			margin-bottom: 1.5rem;
		}

		.search-form input[type="text"] {
			flex-grow: 1;
			margin-right: 0.5rem;
		}

		.login-container {
			max-width: 400px;
			margin: 5rem auto;
		}

		.login-logo {
			text-align: center;
			margin-bottom: 2rem;
		}

		.file-preview {
			margin-top: 1rem;
			max-width: 300px;
		}

		.file-preview img {
			max-width: 100%;
			height: auto;
			border: 1px solid #ddd;
			border-radius: 4px;
			padding: 0.25rem;
		}

		.article-content {
			line-height: 1.8;
		}

		.article-content p {
			margin-bottom: 1.5rem;
		}

		.article-meta {
			margin-bottom: 1rem;
			color: #666;
			font-size: 0.9rem;
		}

		.article-attachments {
			margin-top: 2rem;
		}

		.attachment-link {
			display: inline-flex;
			align-items: center;
			margin-right: 1rem;
			color: #1a73e8;
			text-decoration: none;
		}

		.pagination {
			display: flex;
			justify-content: center;
			gap: 8px;
			margin-top: 20px;
		}

		.pagination .btn {
			padding: 8px 15px;
			border: 1px solid #ddd;
			background-color: #f8f8f8;
			cursor: pointer;
			color: black;
			font-size: 14px;
		}

		.pagination .btn.active {
			background-color: #1a73e8;
			color: white;
			border-color: #1a73e8;
		}

		.pagination .btn.disabled {
			opacity: 0.5;
			cursor: not-allowed;
		}
	</style>
</head>

<body>
	<header>
		<div class="navbar">
			<div class="logo">RSUP Surabaya</div>
			<?php if ($this->session->userdata('logged_in')) : ?>
				<ul class="nav-links">
					<li><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
					<?php if ($role != 'user') : ?>
						<li><a href="<?= base_url('users') ?>">Users</a></li>
					<?php endif; ?>
					<li><a href="javascript:void(0);" onclick="logoutUser()">Logout (<?= $username ?>)</a></li>
				</ul>
			<?php endif; ?>
		</div>
	</header>

	<div class="container">
		<?php if ($this->session->flashdata('success')) : ?>
			<div class="alert alert-success">
				<?= $this->session->flashdata('success'); ?>
			</div>
		<?php endif; ?>

		<?php if ($this->session->flashdata('error')) : ?>
			<div class="alert alert-danger">
				<?= $this->session->flashdata('error'); ?>
			</div>
		<?php endif; ?>
