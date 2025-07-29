<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="../bootstrap5/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../css/login.css" />
  <title>BRGY GO</title>
</head>
<body>
  <div class="container" id="container">

    <!-- Sign Up Form -->
    <div class="form-container sign-up-container">
      <div class="py-1 text-center">
        <span class="BRGY">BRGY</span><span class="GO">GO</span>
      </div>
      <form id="signupForm" action="../database/signup_db.php" method="POST">
        <h1 class="text-center mt-2">Create Account</h1>
        <input type="email" placeholder="Email" name="email" class="form-control mt-3" required />

        <div class="position-relative mt-3">
				  <input type="password" name="password" class="form-control" placeholder="Password" id="signupPassword" required>
				  <i class="bi bi-eye-slash-fill fs-4" id="toggleSignupPassword"></i>
				  <small id="passwordStrength" class="text-muted ms-1"></small>
				</div>

				<div class="position-relative mt-1 mb-1">
				  <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" id="confirmPassword" required>
				  <i class="bi bi-eye-slash-fill fs-4" id="toggleConfirmPassword"></i>
				  <small id="confirmError" class="text-danger ms-1 d-block" style="min-height: 18px; visibility: hidden;">
					  Passwords do not match
					</small>
				</div>

        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="termsCheck" name="toa" required>
          <label class="form-check-label" for="termsCheck" style="font-size: 12px;">
            I agree to the <a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a>.
          </label>
        </div>

        <button class="btn w-100 mt-3 mb-2">Sign Up</button>
        <p>Already have an account? <a href="#" id="signIn">Sign in</a></p>
      </form>
    </div>

    <!-- Sign In Form -->
    <div class="form-container sign-in-container">
      <div class="py-3 text-center">
        <span class="BRGY">BRGY</span><span class="GO">GO</span>
      </div>
      <form action="../database/login_db.php" method="POST">
        <h1 class="text-center mt-2">Log In</h1>
        <input type="email" name="email" placeholder="Enter your email" class="form-control mt-4" required />

        <div class="position-relative mt-3">
          <input type="password" name="password" class="form-control" placeholder="Password" id="signinPassword" required>
          <i class="bi bi-eye-slash-fill fs-4" id="toggleSigninPassword"></i>
        </div>

        <a href="#" class="d-block mt-2 small">Forgot Password?</a>
        <button class="btn w-100 mt-3 mb-2">Log In</button>
        <p>Don't have an account? <a href="#" id="signUp">Sign up</a></p>
      </form>
    </div>

    <!-- Overlay -->
    <div class="overlay-container">
      <div class="overlay">
        <div class="overlay-panel overlay-right">
          <img src="../image/Logo.png" alt="Logo" class="logo"/>
        </div>
        <div class="overlay-panel overlay-left">
          <img src="../image/Logo.png" alt="Logo" class="logo"/>
        </div>
      </div>
    </div>
  </div>

  <script>
    const container = document.getElementById('container');
    document.getElementById('signUp').addEventListener('click', () => {
      container.classList.add("right-panel-active");
    });
    document.getElementById('signIn').addEventListener('click', () => {
      container.classList.remove("right-panel-active");
    });

    // Toggle password visibility functions
    function togglePassword(inputId, toggleId) {
      const input = document.getElementById(inputId);
      const toggle = document.getElementById(toggleId);
      toggle.addEventListener("click", () => {
        const type = input.getAttribute("type") === "password" ? "text" : "password";
        input.setAttribute("type", type);
        toggle.classList.toggle("bi-eye");
        toggle.classList.toggle("bi-eye-slash-fill");
      });
    }

    togglePassword("signupPassword", "toggleSignupPassword");
    togglePassword("confirmPassword", "toggleConfirmPassword");
    togglePassword("signinPassword", "toggleSigninPassword");

  const signupPassword = document.getElementById("signupPassword");
  const confirmPassword = document.getElementById("confirmPassword");
  const passwordStrength = document.getElementById("passwordStrength");
  const confirmError = document.getElementById("confirmError");

  function checkPasswordStrength(password) {
  signupPassword.classList.remove("is-valid", "is-invalid", "is-medium");

  if (password.length > 8) {
    passwordStrength.textContent = "Strong password";
    passwordStrength.style.color = "green";
    signupPassword.classList.add("is-valid");
  } else if (password.length >= 6) {
    passwordStrength.textContent = "Medium strength";
    passwordStrength.style.color = "orange";
    signupPassword.classList.add("is-medium");
  } else {
    passwordStrength.textContent = "Weak password (min 6 characters)";
    passwordStrength.style.color = "red";
    signupPassword.classList.add("is-invalid");
  }
}


  function validateConfirmPassword() {
  if (confirmPassword.value === "") {
    confirmPassword.classList.remove("is-valid", "is-invalid");
    confirmError.style.visibility = "hidden";
    return;
  }

  if (confirmPassword.value === signupPassword.value) {
    confirmPassword.classList.remove("is-invalid");
    confirmPassword.classList.add("is-valid");
    confirmError.style.visibility = "hidden";
  } else {
    confirmPassword.classList.remove("is-valid");
    confirmPassword.classList.add("is-invalid");
    confirmError.style.visibility = "visible";
  }
}

    signupPassword.addEventListener("input", () => {
      checkPasswordStrength(signupPassword.value);
      validateConfirmPassword();
    });

    confirmPassword.addEventListener("input", validateConfirmPassword);

    const signupForm = document.getElementById("signupForm");
    signupForm.addEventListener("submit", function (e) {
      validateConfirmPassword();
      if (confirmPassword.classList.contains("is-invalid")) {
        e.preventDefault(); // Stop form from submitting
      }
    });
  </script>
</body>
</html>
