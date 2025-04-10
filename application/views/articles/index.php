<div class="card">
	<?php if ($role == 'article') : ?>
		<h1><?= "$title Dashboard" ?></h1>
	<?php else : ?>
		<h1><?= "Manage $title" ?></h1>
	<?php endif; ?>

	<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
		<?php if ($role != 'user') : ?>
			<div>
				<a href="<?= base_url('articles/create') ?>" class="btn">Create New <?= $title ?></a>
			</div>
		<?php else: ?>
			<div></div>
		<?php endif; ?>

		<div class="search-container" style="display: flex; align-items: center;">
			<input type="text" id="searchInput" placeholder="Search articles..." style="padding: 0.5rem; margin-right: 0.5rem; width: 200px;">
			<button type="button" id="searchButton" class="btn" onclick="searchArticles()">Search</button>
		</div>
	</div>

	<table>
		<thead>
			<tr>
				<th>Title</th>
				<th>Category</th>
				<th>Author</th>
				<th>Created At</th>
				<?php if ($role != 'user') : ?>
					<th>Actions</th>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody id="articlesTableBody">
			<tr>
				<?php if ($role != 'user') : ?>
					<td colspan="5" style="text-align: center;">Loading articles...</td>
				<?php else: ?>
					<td colspan="4" style="text-align: center;">Loading articles...</td>
				<?php endif; ?>
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
	let currentKeyword = '';

	document.addEventListener('DOMContentLoaded', function() {
		fetchArticles(1, currentKeyword);
	});

	function fetchArticles(page = 1, keyword) {
		currentPage = page;

		var xhr = new XMLHttpRequest();
		var uri = `<?= base_url("api/articles") ?>?page=${page}&limit=${itemsPerPage}&search=${keyword}`;

		xhr.open('GET', uri, true);
		xhr.onreadystatechange = function() {
			if (xhr.readyState === 4) {
				if (xhr.status === 200) {
					try {
						var response = JSON.parse(xhr.responseText);
						if (response.status) {
							displayArticles(response.data);

							if (response.pagination) {
								totalPages = response.pagination.total_pages;
								renderPagination();
							}
						} else {
							displayError(response.message || 'Failed to fetch articles');
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

	function displayArticles(articles) {
		const tableBody = document.getElementById('articlesTableBody');
		tableBody.innerHTML = '';

		const currentUserId = <?= $this->session->userdata('id') ?>;
		const currentUserRole = '<?= $this->session->userdata('role') ?>';

		if (articles.length === 0) {
			const row = document.createElement('tr');
			if (currentUserRole != 'user') {
				row.innerHTML = '<td colspan="5" style="text-align: center;">No articles found.</td>';
			} else {
				row.innerHTML = '<td colspan="4" style="text-align: center;">No articles found.</td>';
			}
			tableBody.appendChild(row);
			return;
		}

		articles.forEach(function(article) {
			const row = document.createElement('tr');

			const titleCell = document.createElement('td');
			titleCell.textContent = article.title;
			row.appendChild(titleCell);

			const categoryCell = document.createElement('td');
			categoryCell.textContent = article.category;
			row.appendChild(categoryCell);

			const authorCell = document.createElement('td');
			authorCell.textContent = article.username;
			row.appendChild(authorCell);

			const createdAtCell = document.createElement('td');
			createdAtCell.textContent = article.created_at;
			row.appendChild(createdAtCell);

			const actionsCell = document.createElement('td');

			if (currentUserRole != 'user') {
				const editLink = document.createElement('a');
				editLink.href = '<?= base_url('articles/') ?>' + article.id;
				editLink.className = 'btn btn-warning';
				editLink.style.padding = '0.25rem 0.5rem';
				editLink.style.fontSize = '0.85rem';
				editLink.textContent = 'Edit';
				actionsCell.appendChild(editLink);

				if (article.user_id == currentUserId || currentUserRole == 'admin') {
					actionsCell.appendChild(document.createTextNode(' '))

					const deleteButton = document.createElement('button');
					deleteButton.type = 'button';
					deleteButton.className = 'btn btn-danger';
					deleteButton.style.padding = '0.25rem 0.5rem';
					deleteButton.style.fontSize = '0.85rem';
					deleteButton.textContent = 'Delete';
					deleteButton.onclick = function() {
						deleteArticle(article.id, this);
					};
					actionsCell.appendChild(deleteButton);
				}

			}

			row.appendChild(actionsCell);
			tableBody.appendChild(row);
		});
	}

	function displayError(message) {
		const tableBody = document.getElementById('articlesTableBody');
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
				fetchArticles(currentPage - 1, currentKeyword);
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
				fetchArticles(i, currentKeyword);
			};
			paginationDiv.appendChild(pageButton);
		}

		const nextButton = document.createElement('button');
		nextButton.textContent = 'Next';
		nextButton.className = 'btn' + (currentPage === totalPages ? ' disabled' : '');
		nextButton.disabled = currentPage === totalPages;
		nextButton.onclick = function() {
			if (currentPage < totalPages) {
				fetchArticles(currentPage + 1, currentKeyword);
			}
		};
		paginationDiv.appendChild(nextButton);
	}

	function searchArticles() {
		const keyword = document.getElementById('searchInput').value.trim();
		currentKeyword = keyword;
		currentPage = 1;

		const paginationDiv = document.getElementById('pagination');
		paginationDiv.innerHTML = '';

		fetchArticles(1, currentKeyword);
	}

	function deleteArticle(articleId, element) {
		if (!confirm('Are you sure you want to delete this article?')) {
			return;
		}

		var xhr = new XMLHttpRequest();
		xhr.open('DELETE', '<?= base_url("api/datas/") ?>' + articleId, true);

		xhr.onreadystatechange = function() {
			if (xhr.readyState === 4) {
				try {
					var response = JSON.parse(xhr.responseText);
					if (response.status) {
						fetchArticles(1, currentKeyword);
					} else {
						alert(response.message || 'Failed to delete artcile');
					}
				} catch (e) {
					alert('Invalid response from server');
				}
			}
		};

		xhr.send();
	}

	function logout() {
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
