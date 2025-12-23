function triggerUpload() {
  document.getElementById("profileUpload").click();
}

document
  .getElementById("profileUpload")
  .addEventListener("change", function () {
    if (this.files && this.files[0]) {
      const reader = new FileReader();
      reader.onload = (e) => {
        document.getElementById("avatarPreview").src = e.target.result;
      };
      reader.readAsDataURL(this.files[0]);
    }
  });
