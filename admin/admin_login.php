<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ServiceHub</title>
    <link rel="stylesheet" href="../css/style.css">
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


    </style>
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-up-container">
            <form action="admin_signup.php" method="POST">
                <h1>Create Account</h1>
                <input type="text" name="name" placeholder="Name" required />
                <input type="text" name="phone" placeholder="Phone Number" required />
                <!-- <input type="text" name="location" placeholder="Location" required /> -->
                <input type="email" name="email" placeholder="Email" required />
                <input type="password" name="password" placeholder="Password" required />
                <button type="submit">Sign Up</button>
            </form>
        </div>

        <div class="form-container sign-in-container">
            <form action="admin_signin.php" method="POST">
                <h1>Sign In</h1>
                <input type="email" name="email" placeholder="Email" required />
                <input type="password" name="password" placeholder="Password" required />
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
                    <h1>Hello, Admin!</h1>
                    <p>Please enter your credentials to sign in and access the admin dashboard.</p>
                    <!-- <button class="ghost" id="signUp">Sign Up</button> -->
                </div>
            </div>
        </div>
    </div>

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
    document.querySelector('.close').onclick = function() {
        document.getElementById('signupModal').style.display = "none";
    }

    // Close modal when clicking outside the modal content
    window.onclick = function(event) {
        if (event.target == document.getElementById('signupModal')) {
            document.getElementById('signupModal').style.display = "none";
        }
    }

    signUpButton.addEventListener('click', () => {
        container.classList.add("right-panel-active");
    });

    signInButton.addEventListener('click', () => {
        container.classList.remove("right-panel-active");
    });

    // Check if the message exists in the URL parameters
    window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('message');
    if (message) {
        openModal(message);
    }
};

</script>

</body>
</html>
