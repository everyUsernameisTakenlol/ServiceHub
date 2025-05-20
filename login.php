<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ServiceHub</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
		.modal {
			display: none;
			position: fixed;
			z-index: 9999;
			left: 0;
			top: 0;
			width: 100%;
			height: 100%;
			background-color: rgba(0, 0, 0, 0.6);
			padding-top: 0; 
		}

		.modal-content {
			background-color: #fefefe;
			margin: 0;
			padding: 30px;
			border: 1px solid #888;
			width: 50%; 
			max-width: 500px; 
			box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); 
			border-radius: 8px; 
			
			
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%); 
		}

		.close {
			color: #aaa;
			float: right;
			font-size: 28px;
			font-weight: bold;
			cursor: pointer;
		}

		.close:hover,
		.close:focus {
			color: black;
			text-decoration: none;
		}
        
    .password-container {
        position: relative;
        margin-bottom: 15px;
    }

    .password-container {
        width: 285px;
        padding: 5; /* Space inside the input box */
        font-size: 16px; /* Font size for text */
        /*border: 2px solid #ccc; /* Border color and thickness */
       /* border-radius: 4px; /* Rounded corners */
        /*box-sizing: border-box; /* Ensure padding and border are included in total width */
        outline: none; /* Remove default blue outline */
        transition: border-color 0.3s ease; /* Smooth transition for hover/focus effects */
      
    }
    

    .password-container input[type="password"]:focus {
        border-color: black; /* Highlight color on focus*/
      }   

    .password-container img {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
    .error-message {
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }
</style>


    
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-up-container">
            <form action="signup.php" method="POST" onsubmit="return validatePasswords()">
                <h1>Create Account</h1>
                <input type="text" name="name" placeholder="Name" required />
                <input type="tel" name="phone" placeholder="Phone Number" required />
                <input type="text" name="location" placeholder="Location" required />
                <input type="email" name="email" placeholder="Email" required />
                <div class="password-container">
                    <input type="password" id="signupPassword" name="password" placeholder="Password" required />
                    <img id="toggleSignupPassword" src="img/show.png" alt="Show Password" />
                    
                </div>
                <div class="password-container"style="margin-top: -15px;">
                    <input type="password" id="confirmPassword" name="confirm_password" placeholder="Confirm Password" required />
                    <img id="toggleConfirmPassword" src="img/show.png" alt="Show Password" />
                    <div id="confirmPasswordError" class="error-message"></div>
                    <div id="passwordError" class="error-message"></div>
                </div>
                <button type="submit">Sign Up</button>
            </form>
        </div>

        <div class="form-container sign-in-container">
            <form action="signin.php" method="POST">
                <h1>Sign In</h1>
                <input type="email" name="email" placeholder="Email" required />
                <div class="password-container">
                    <input type="password" id="signinPassword" name="password" placeholder="Password" required />
                    <img id="toggleSigninPassword" src="img/show.png" alt="Show Password" />
                </div>
                <button type="submit">Sign In</button>
            </form>
        </div>

        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>To keep connected with us please login with your personal info</p>
                    <button class="ghost" id="signIn">Sign In</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello, User!</h1>
                    <p>Sign up today for easy access to top-rated home services whenever you need them.</p>
                    <button class="ghost" id="signUp">Sign Up</button>
                </div>
            </div>
        </div>
    </di>

    <!-- Modal HTML structure -->
<div id="signupModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h1 id="modalMessage"></h1>
    </div>
</div>

<script>
        const signUpButton = document.getElementById('signUp');
        const signInButton = document.getElementById('signIn');
        const container = document.getElementById('container');

        // Modal open function
        function openModal(message) {
            document.getElementById('modalMessage').textContent = message;
            document.getElementById('signupModal').style.display = "block";
        }

        // Close modal when clicking the close button
        document.querySelector('.close').onclick = function () {
            document.getElementById('signupModal').style.display = "none";
        };

        // Close modal when clicking outside the modal content
        window.onclick = function (event) {
            if (event.target == document.getElementById('signupModal')) {
                document.getElementById('signupModal').style.display = "none";
            }
        };

        signUpButton.addEventListener('click', () => {
            container.classList.add("right-panel-active");
        });

        signInButton.addEventListener('click', () => {
            container.classList.remove("right-panel-active");
        });

        // Function to toggle password visibility
        function togglePasswordVisibility(inputId, toggleId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(toggleId);

            toggleIcon.addEventListener('click', function () {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleIcon.src = 'img/invisible.png';
                    toggleIcon.alt = 'Hide Password';
                } else {
                    passwordInput.type = 'password';
                    toggleIcon.src = 'img/show.png';
                    toggleIcon.alt = 'Show Password';
                }
            });
        }

        // Apply password visibility toggle
        togglePasswordVisibility('signupPassword', 'toggleSignupPassword');
        togglePasswordVisibility('confirmPassword', 'toggleConfirmPassword');
        togglePasswordVisibility('signinPassword', 'toggleSigninPassword');

        // Function to validate passwords
        function validatePasswords() {
            const password = document.getElementById('signupPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const passwordError = document.getElementById('passwordError');
            const confirmPasswordError = document.getElementById('confirmPasswordError');
            let valid = true;

            // Reset previous error messages
            passwordError.textContent = '';
            confirmPasswordError.textContent = '';

            // Password length validation
            if (password.length < 8 || password.length > 15) {
                passwordError.textContent = "Password must be between 8 to 15 characters.";
                valid = false;
            }

            // Password match validation
            if (password !== confirmPassword) {
                confirmPasswordError.textContent = "Password did not match.";
                valid = false;
            }

            return valid;
        }

        // Check if the message exists in the URL parameters
        window.onload = function () {
            const urlParams = new URLSearchParams(window.location.search);
            const message = urlParams.get('message');
            if (message) {
                openModal(message);
            }
        };

        // Modal open function
        function openModal(message) {
            document.getElementById('modalMessage').textContent = message;
            document.getElementById('signupModal').style.display = "block";
        }

        // Close modal when clicking the close button
        document.querySelector('.close').onclick = function () {
            document.getElementById('signupModal').style.display = "none";
        };

        // Close modal when clicking outside the modal content
        window.onclick = function (event) {
            if (event.target == document.getElementById('signupModal')) {
                document.getElementById('signupModal').style.display = "none";
            }
        };
    </script>

</body>
</html>
