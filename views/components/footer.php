</main>

<footer class="bg-white shadow-md mt-auto b-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    </div>
</footer>

<!-- Cropper.js JS -->
<script src="https://unpkg.com/cropperjs@1.5.13/dist/cropper.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.querySelector('input[type="file"][name="image"]');
    const previewContainer = document.createElement('div');
    const image = document.createElement('img');
    let cropper;

    if (fileInput) {
        // insert preview container after input
        fileInput.parentElement.appendChild(previewContainer);
        previewContainer.appendChild(image);
        previewContainer.classList.add('mt-4', 'relative', 'w-64', 'h-64');
        image.classList.add('max-w-full', 'rounded-lg', 'shadow');

        fileInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (event) {
                image.src = event.target.result;

                // destroy previous cropper
                if (cropper) cropper.destroy();

                // wait for image to load
                image.onload = function () {
                    cropper = new Cropper(image, {
                        aspectRatio: 1,
                        viewMode: 1,
                        autoCropArea: 1,
                        cropend() {
                            cropAndUpload();
                        }
                    });
                };
            };
            reader.readAsDataURL(file);
        });
    }

    function cropAndUpload() {
        if (!cropper) return;

        cropper.getCroppedCanvas({
            width: 300,
            height: 300
        }).toBlob(blob => {
            const formData = new FormData();
            formData.append('image', blob, 'profile.jpg');

            fetch('/profile/image', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Upload failed');
                return response.text();
            })
            .then(() => {
                window.location.reload();
            })
            .catch(err => {
                alert('Error uploading image: ' + err.message);
            });
        }, 'image/jpeg');
    }
});
</script>

</body>
</html>
