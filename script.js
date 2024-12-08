// Function to handle image preview without affecting the design
function previewImages(event) {
  const files = event.target.files;
  const imageBoxes = document.querySelectorAll('.image-box');

  // Clear existing images from the boxes
  imageBoxes.forEach((box) => (box.innerHTML = ''));

  // Loop through each file and display it
  Array.from(files).forEach((file, index) => {
    if (index < imageBoxes.length) {
      const reader = new FileReader();
      reader.onload = function (e) {
        const img = document.createElement('img');
        img.src = e.target.result;
        img.classList.add('img-fluid');
        img.style.maxWidth = '100%';
        imageBoxes[index].appendChild(img);
      };
      reader.readAsDataURL(file);
    }
  });
}
