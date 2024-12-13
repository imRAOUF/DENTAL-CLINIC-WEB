const appointmentForm = document.getElementById("appointmentForm");

// Get form fields
const serviceField = document.getElementById("service");
const dateField = document.getElementById("date");
const timeField = document.getElementById("time");
const nameField = document.getElementById("name");
const phoneField = document.getElementById("phone");

// Get error message elements
const serviceError = document.getElementById("serviceError");
const dateError = document.getElementById("dateError");
const timeError = document.getElementById("timeError");
const nameError = document.getElementById("nameError");
const phoneError = document.getElementById("phoneError");

// Utility: Show and Hide Error Messages
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

// Real-Time Validation Functions
serviceField.addEventListener("change", () => {
  if (serviceField.value === "") {
    showError(serviceField, serviceError, "Please select a service.");
  } else {
    hideError(serviceField, serviceError);
  }
});

dateField.addEventListener("input", () => {
  const today = new Date();
  const selectedDate = new Date(dateField.value);
  if (selectedDate < today.setHours(0, 0, 0, 0)) {
    showError(dateField, dateError, "The selected date cannot be in the past.");
  } else {
    hideError(dateField, dateError);
  }
});

timeField.addEventListener("input", () => {
  const time = timeField.value;
  const [hours, minutes] = time.split(":").map(Number);
  if (hours < 9 || hours > 17 || (hours === 17 && minutes > 0)) {
    showError(
      timeField,
      timeError,
      "Please select a time between 9:00 AM and 5:00 PM."
    );
  } else {
    hideError(timeField, timeError);
  }
});

nameField.addEventListener("input", () => {
  const nameRegex = /^[a-zA-Z\s]{3,}$/;
  if (!nameRegex.test(nameField.value.trim())) {
    showError(
      nameField,
      nameError,
      "Name must contain only letters and spaces, with at least 3 characters."
    );
  } else {
    hideError(nameField, nameError);
  }
});

phoneField.addEventListener("input", () => {
  const phoneRegex = /^[0-9]{10}$/;
  if (!phoneRegex.test(phoneField.value.trim())) {
    showError(
      phoneField,
      phoneError,
      "Phone number must contain exactly 10 digits."
    );
  } else {
    hideError(phoneField, phoneError);
  }
});

// Final Validation on Form Submission
appointmentForm.addEventListener("submit", (e) => {
  let valid = true;

  // Check all fields one last time
  if (serviceField.value === "") {
    showError(serviceField, serviceError, "Please select a service.");
    valid = false;
  }
  const today = new Date();
  const selectedDate = new Date(dateField.value);
  if (selectedDate < today.setHours(0, 0, 0, 0)) {
    showError(dateField, dateError, "The selected date cannot be in the past.");
    valid = false;
  }
  const time = timeField.value;
  const [hours, minutes] = time.split(":").map(Number);
  if (hours < 9 || hours > 17 || (hours === 17 && minutes > 0)) {
    showError(
      timeField,
      timeError,
      "Please select a time between 9:00 AM and 5:00 PM."
    );
    valid = false;
  }
  const nameRegex = /^[a-zA-Z\s]{3,}$/;
  if (!nameRegex.test(nameField.value.trim())) {
    showError(
      nameField,
      nameError,
      "Name must contain only letters and spaces, with at least 3 characters."
    );
    valid = false;
  }
  const phoneRegex = /^[0-9]{10}$/;
  if (!phoneRegex.test(phoneField.value.trim())) {
    showError(
      phoneField,
      phoneError,
      "Phone number must contain exactly 10 digits."
    );
    valid = false;
  }

  if (!valid) {
    e.preventDefault(); // Prevent form submission if invalid
  }
});
