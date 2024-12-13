// Get form and fields
const registerForm = document.getElementById("registerForm");

// Form fields
const nameField = document.getElementById("name");
const emailField = document.getElementById("email");
const passwordField = document.getElementById("password");
const confirmPasswordField = document.getElementById("confirmPassword");

// Error elements
const nameError = document.getElementById("nameError");
const emailError = document.getElementById("emailError");
const passwordError = document.getElementById("passwordError");
const confirmPasswordError = document.getElementById("confirmPasswordError");

// Utility functions for showing and hiding errors
function showError(input, errorElement, message) {
  errorElement.textContent = message;
  errorElement.style.display = "block";
  input.classList.add("error-border");
}

function hideError(input, errorElement) {
  errorElement.textContent = "";
  errorElement.style.display = "none";
  input.classList.remove("error-border");
}

// Real-time Validation for each input field

nameField.addEventListener("input", () => {
  const nameRegex = /^[a-zA-Z\s]{3,}$/;
  if (!nameRegex.test(nameField.value.trim())) {
    showError(
      nameField,
      nameError,
      "Name must be at least 3 characters long and contain only letters and spaces."
    );
  } else {
    hideError(nameField, nameError);
  }
});

emailField.addEventListener("input", () => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(emailField.value.trim())) {
    showError(emailField, emailError, "Please enter a valid email address.");
  } else {
    hideError(emailField, emailError);
  }
});

passwordField.addEventListener("input", () => {
  if (passwordField.value.length < 8) {
    showError(
      passwordField,
      passwordError,
      "Password must be at least 8 characters long."
    );
  } else {
    hideError(passwordField, passwordError);
  }
});

confirmPasswordField.addEventListener("input", () => {
  if (confirmPasswordField.value !== passwordField.value) {
    showError(
      confirmPasswordField,
      confirmPasswordError,
      "Passwords do not match."
    );
  } else {
    hideError(confirmPasswordField, confirmPasswordError);
  }
});

// Final form submission validation
registerForm.addEventListener("submit", (e) => {
  let valid = true;

  // Check all fields one last time
  if (nameField.value.trim() === "" || nameError.textContent) valid = false;
  if (emailField.value.trim() === "" || emailError.textContent) valid = false;
  if (passwordField.value.trim() === "" || passwordError.textContent)
    valid = false;
  if (
    confirmPasswordField.value.trim() === "" ||
    confirmPasswordError.textContent
  )
    valid = false;

  if (!valid) {
    e.preventDefault(); // Prevent form submission if invalid
    alert("Please fix the errors in the form.");
  }
});
