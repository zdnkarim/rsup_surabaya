<div class="card">
	<h1><?= $title ?></h1>

	<div style="margin-bottom: 1rem;">
		<a href="<?= base_url('articles') ?>" class="btn">&laquo; Back to Articles</a>
	</div>

	<?= form_open_multipart('articles/create'); ?>
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
		<label for="image">Image (PNG, JPG, JPEG, GIF max 2MB)</label>
		<input type="file" name="image" id="image" accept="image/*">
		<div id="imagePreview" style="margin-top: 0.5rem; display: none; max-width: 200px;">
			<img id="imagePreviewImg" style="max-width: 100%;">
		</div>
		<div id="imageError" style="color: red; margin-top: 0.25rem;"></div>
	</div>

	<div class="form-group">
		<label for="file">File (PDF only, max 5MB)</label>
		<input type="file" name="file" id="file" accept=".pdf">
		<div id="fileInfo" style="margin-top: 0.5rem; display: none;"></div>
		<div id="fileError" style="color: red; margin-top: 0.25rem;"></div>
	</div>

	<button type="submit" class="btn">Create Articles</button>
	<?= form_close(); ?>
</div>

</div>

<script>
	document.getElementById('image').addEventListener('change', function(e) {
		const file = e.target.files[0];
		const preview = document.getElementById('imagePreview');
		const previewImg = document.getElementById('imagePreviewImg');
		const errorDiv = document.getElementById('imageError');

		errorDiv.textContent = '';
		preview.style.display = 'none';

		if (file) {

			const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
			if (!validTypes.includes(file.type)) {
				errorDiv.textContent = 'Invalid file type. Please upload a PNG, JPG, JPEG, or GIF file.';
				e.target.value = '';
				return;
			}

			if (file.size > 2 * 1024 * 1024) {
				errorDiv.textContent = 'File size exceeds 2MB limit.';
				e.target.value = '';
				return;
			}

			const reader = new FileReader();
			reader.onload = function(e) {
				previewImg.src = e.target.result;
				preview.style.display = 'block';
			}
			reader.readAsDataURL(file);
		}
	});

	document.getElementById('file').addEventListener('change', function(e) {
		const file = e.target.files[0];
		const infoDiv = document.getElementById('fileInfo');
		const errorDiv = document.getElementById('fileError');

		errorDiv.textContent = '';
		infoDiv.style.display = 'none';

		if (file) {
			if (file.type !== 'application/pdf') {
				errorDiv.textContent = 'Invalid file type. Please upload a PDF file.';
				e.target.value = '';
				return;
			}

			if (file.size > 5 * 1024 * 1024) {
				errorDiv.textContent = 'File size exceeds 5MB limit.';
				e.target.value = '';
				return;
			}

			infoDiv.textContent = `Selected: ${file.name} (${(file.size / 1024).toFixed(2)} KB)`;
			infoDiv.style.display = 'block';
		}
	});

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
			return false;
		}

		const formData = new FormData(this);
		formData.append('userId', userId);


		fetch('<?= base_url("api/datas") ?>', {
				method: 'POST',
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				if (data.status) {
					alert('Article created successfully');
					window.location.href = '<?= base_url("articles") ?>';
				} else {
					alert(data.message || 'Failed to create article');
				}
			})
			.catch(error => {
				alert('An error occurred while creating the article');
			});
		e.preventDefault();
	})
</script>
