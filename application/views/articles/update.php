<div class="card">
	<h1><?= $title ?></h1>

	<div style="margin-bottom: 1rem;">
		<a href="<?= base_url('articles') ?>" class="btn">&laquo; Back to Articles</a>
	</div>

	<?= form_open_multipart("articles/update/$id"); ?>
	<div class="form-group">
		<label for="title">Title</label>
		<input type="text" name="title" id="title" value="<?= set_value('title'); ?>" required>
		<?= form_error('title', '<div style="color: red; margin-top: 0.25rem;">', '</div>'); ?>
	</div>

	<div class="form-group">
		<label for="content">Content</label>
		<textarea name="content" id="content" required><?= set_value('content'); ?></textarea>
		<?= form_error('content', '<div style="color: red; margin-top: 0.25rem;">', '</div>'); ?>
	</div>

	<div class="form-group">
		<label for="category">Category</label>
		<input type="text" name="category" id="category" value="<?= set_value('category'); ?>" required>
		<?= form_error('category', '<div style="color: red; margin-top: 0.25rem;">', '</div>'); ?>
	</div>

	<div class="form-group">
		<label for="image">Image</label>
		<input type="file" name="image" id="image" value="<?= set_value('image'); ?>">
		<?= form_error('image', '<div style="color: red; margin-top: 0.25rem;">', '</div>'); ?>
	</div>

	<div class="form-group">
		<label for="file">File</label>
		<input type="file" name="file" id="file" value="<?= set_value('file'); ?>">
		<?= form_error('file', '<div style="color: red; margin-top: 0.25rem;">', '</div>'); ?>
	</div>

	<button type="submit" class="btn">Update Articles</button>
	<?= form_close(); ?>
</div>

</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		const titleField = document.getElementById('title');
		const contentField = document.getElementById('content');
		const categoryField = document.getElementById('category');
		titleField.disabled = true;
		contentField.disabled = true;
		categoryField.disabled = true;
		titleField.placeholder = "Loading...";
		contentField.placeholder = "Loading...";
		categoryField.placeholder = "Loading...";

		fetch('<?= base_url("api/datas/$id") ?>')
			.then(response => response.json())
			.then(data => {
				if (data.status === true && data.data) {
					populateArticleForm(data.data);
				} else {
					alert(data.message || 'Failed to load article data');
					window.location.href = '<?= base_url("articles") ?>';
				}
			})
			.catch(error => {
				alert('An error occurred while loading article data');
				window.location.href = '<?= base_url("articles") ?>';
			})
			.finally(() => {
				titleField.disabled = false;
				contentField.disabled = false;
				categoryField.disabled = false;
				titleField.placeholder = "";
				contentField.placeholder = "";
				categoryField.placeholder = "";
			});
	})

	function populateArticleForm(articleData) {
		document.getElementById('title').value = articleData.title || '';
		document.getElementById('content').value = articleData.content || '';
		document.getElementById('category').value = articleData.category || '';
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

	document.querySelector('form').addEventListener('submit', function(e) {
		userId = <?= $userId ?>;

		let title = document.getElementById('title').value.trim();
		let content = document.getElementById('content').value.trim();
		let category = document.getElementById('category').value.trim();
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

		if (!title) {
			showError('title', 'Title is required');
			hasError = true;
		} else if (title.length < 8 || title.length > 20) {
			showError('title', 'Title must be between 8 and 20 characters');
			hasError = true;
		}

		if (!content) {
			showError('content', 'Content is required');
			hasError = true;
		} else if (content.length < 20 || content.length > 200) {
			showError('content', 'Content must be between 20 and 200 characters');
			hasError = true;
		}

		if (!category) {
			showError('category', 'Category is required');
			hasError = true;
		} else if (category.length < 3 || category.length > 20) {
			showError('category', 'Category must be between 3 and 20 characters');
			hasError = true;
		}

		if (hasError) {
			e.preventDefault();
			return false;
		}

		const articleId = <?= $id ?>;

		fetch(`<?= base_url("api/datas") ?>/${articleId}`, {
				method: 'PUT',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					title: title,
					content: content,
					category: category
				}),
			})
			.then(response => response.json())
			.then(data => {
				if (data.status) {
					alert('Article updated successfully');
					window.location.href = '<?= base_url("articles") ?>';
				} else {
					alert(data.message || 'Failed to update article');
				}
			})
			.catch(error => {
				alert('An error occurred while updating the article');
			});
		e.preventDefault();
	})
</script>
