<div class="card">
	<h1><?= $title ?></h1>

	<?php if ($role == 'admin') : ?>
		<div style="margin-bottom: 1rem;">
			<a href="<?= base_url('users/create') ?>" class="btn">Create New User</a>
		</div>
	<?php endif; ?>

	<table>
		<thead>
			<tr>
				<th>Username</th>
				<th>Role</th>
				<?php if ($role == 'admin') : ?>
					<th>Actions</th>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody id="usersTableBody">
			<tr>
				<td colspan="3" style="text-align: center;">Loading users...</td>
			</tr>
		</tbody>

	</table>

	<div id="pagination" class="pagination" style="margin-top: 1rem;"></div>
</div>

</div>

<script>
	let currentPage = 1;
	let totalPages = 1;
	let itemsPerPage = 5;

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

	function fetchUsers(page = 1) {
		currentPage = page;

		var xhr = new XMLHttpRequest();
		xhr.open('GET', `<?= base_url("api/users") ?>?page=${page}&limit=${itemsPerPage}`, true);
		xhr.onreadystatechange = function() {
			if (xhr.readyState === 4) {
				if (xhr.status === 200) {
					try {
						var response = JSON.parse(xhr.responseText);
						if (response.status) {
							displayUsers(response.data);

							if (response.pagination) {
								totalPages = response.pagination.total_pages;
								renderPagination();
							}
						} else {
							displayError(response.message || 'Failed to fetch users');
						}
					} catch (e) {
						displayError('Invalid response from server');
					}
				} else {
					displayError('Server error: ' + xhr.status);
				}
			}
		};
		xhr.send();
	}

	function displayUsers(users) {
		const tableBody = document.getElementById('usersTableBody');
		tableBody.innerHTML = '';

		if (users.length === 0) {
			const row = document.createElement('tr');
			row.innerHTML = '<td colspan="3" style="text-align: center;">No users found.</td>';
			tableBody.appendChild(row);
			return;
		}

		const currentUserId = <?= $this->session->userdata('id') ?>;
		const currentUserRole = '<?= $this->session->userdata('role') ?>';


		users.forEach(function(user) {
			const row = document.createElement('tr');

			const usernameCell = document.createElement('td');
			usernameCell.textContent = user.username;
			row.appendChild(usernameCell);

			const roleCell = document.createElement('td');
			roleCell.textContent = user.role;
			row.appendChild(roleCell);

			const actionsCell = document.createElement('td');

			if (currentUserRole == 'admin') {
				const editLink = document.createElement('a');
				editLink.href = '<?= base_url('users/') ?>' + user.id;
				editLink.className = 'btn btn-warning';
				editLink.style.padding = '0.25rem 0.5rem';
				editLink.style.fontSize = '0.85rem';
				editLink.textContent = 'Edit';
				actionsCell.appendChild(editLink);

				if (user.id != currentUserId) {
					actionsCell.appendChild(document.createTextNode(' '))

					const deleteButton = document.createElement('button');
					deleteButton.type = 'button';
					deleteButton.className = 'btn btn-danger';
					deleteButton.style.padding = '0.25rem 0.5rem';
					deleteButton.style.fontSize = '0.85rem';
					deleteButton.textContent = 'Delete';
					deleteButton.onclick = function() {
						deleteUser(user.id, this);
					};
					actionsCell.appendChild(deleteButton);
				}

			}

			row.appendChild(actionsCell);
			tableBody.appendChild(row);
		});
	}

	function displayError(message) {
		const tableBody = document.getElementById('usersTableBody');
		tableBody.innerHTML = '<tr><td colspan="3" style="text-align: center; color: red;">' + message + '</td></tr>';
	}

	function renderPagination() {
		const paginationDiv = document.getElementById('pagination');
		paginationDiv.innerHTML = '';

		if (totalPages <= 1) {
			return
		}

		const prevButton = document.createElement('button');
		prevButton.textContent = 'Previous';
		prevButton.className = 'btn' + (currentPage === 1 ? ' disabled' : '');
		prevButton.disabled = currentPage === 1;
		prevButton.onclick = function() {
			if (currentPage > 1) {
				fetchUsers(currentPage - 1);
			}
		};
		paginationDiv.appendChild(prevButton);

		const startPage = Math.max(1, currentPage - 2);
		const endPage = Math.min(totalPages, startPage + 4);

		for (let i = startPage; i <= endPage; i++) {
			const pageButton = document.createElement('button');
			pageButton.textContent = i;
			pageButton.className = 'btn' + (i === currentPage ? ' active' : '');
			pageButton.onclick = function() {
				fetchUsers(i);
			};
			paginationDiv.appendChild(pageButton);
		}

		const nextButton = document.createElement('button');
		nextButton.textContent = 'Next';
		nextButton.className = 'btn' + (currentPage === totalPages ? ' disabled' : '');
		nextButton.disabled = currentPage === totalPages;
		nextButton.onclick = function() {
			if (currentPage < totalPages) {
				fetchUsers(currentPage + 1);
			}
		};
		paginationDiv.appendChild(nextButton);
	}

	function deleteUser(userId, element) {
		if (!confirm('Are you sure you want to delete this user?')) {
			return;
		}

		var xhr = new XMLHttpRequest();
		xhr.open('DELETE', '<?= base_url("api/users/delete/") ?>' + userId, true);

		xhr.onreadystatechange = function() {
			if (xhr.readyState === 4) {
				try {
					var response = JSON.parse(xhr.responseText);
					if (response.status) {
						fetchUsers(1);
					} else {
						alert(response.message || 'Failed to delete user');
					}
				} catch (e) {
					alert('Invalid response from server');
				}
			}
		};

		xhr.send();
	}

	document.addEventListener('DOMContentLoaded', function() {
		fetchUsers(1);
	});
</script>
</body>

</html>
