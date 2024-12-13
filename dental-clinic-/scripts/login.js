// Wait for DOM to fully load
document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form");
  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("password");

  // Error display function
  function showError(input, message) {
    const errorContainer = input.parentElement.querySelector(".error-message");
    if (!errorContainer) {
      const error = document.createElement("div");
      error.className = "error-message";
      error.style.color = "red";
      error.style.fontSize = "0.85rem";
      error.style.marginTop = "0.3rem";
      input.parentElement.appendChild(error);
      error.textContent = message;
    } else {
      errorContainer.textContent = message;
    }
  }

  // Remove error function
  function removeError(input) {
    const errorContainer = input.parentElement.querySelector(".error-message");
    if (errorContainer) {
      errorContainer.remove();
    }
  }

  // Email validation
  emailInput.addEventListener("input", function () {
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(emailInput.value)) {
      showError(emailInput, "Please enter a valid email address.");
    } else {
      removeError(emailInput);
    }
  });

  // Password validation
  passwordInput.addEventListener("input", function () {
    if (passwordInput.value.length < 6) {
      showError(passwordInput, "Password must be at least 6 characters.");
    } else {
      removeError(passwordInput);
    }
  });

  // Form submission validation
  form.addEventListener("submit", function (e) {
    let isValid = true;

    // Validate email
    if (!emailInput.value.trim()) {
      showError(emailInput, "Email is required.");
      isValid = false;
    } else {
      removeError(emailInput);
    }

    // Validate password
    if (!passwordInput.value.trim()) {
      showError(passwordInput, "Password is required.");
      isValid = false;
    } else if (passwordInput.value.length < 6) {
      showError(passwordInput, "Password must be at least 6 characters.");
      isValid = false;
    } else {
      removeError(passwordInput);
    }

    // Prevent form submission if invalid
    if (!isValid) {
      e.preventDefault();
    }
  });
});
