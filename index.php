<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ontrack Rentals - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        :root {
            --primary-color: #1DB954;
            --background-color: #121212;
            --card-color: #1E1E1E;
            --text-color: #FFFFFF;
            --input-bg: #333333;
            --input-border: #555555;
            --hover-color: #1AA34A;
            --shadow-color: rgba(0, 0, 0, 0.6);
        }

        body {
            background-color: var(--background-color);
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--text-color);
            flex-direction: column;
        }

        .container {
            background-color: var(--card-color);
            width: 450px;
            padding: 2rem;
            margin: 50px auto;
            border-radius: 15px;
            box-shadow: 0 10px 25px var(--shadow-color);
        }

        .container:hover{
            transform: scale(1.05);
        }
        .form-title {
            font-size: 1.8rem;
            font-weight: bold;
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }

        .input-group {
            margin-bottom: 1.5rem;
            position: relative;
            display: flex;
            align-items: center;
            background: var(--input-bg);
            border-radius: 8px;
            padding: 10px;
        }

        .input-group i {
            color: #aaa;
            margin-right: 10px;
            transition: color 0.3s;
        }

        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--input-border);
            background: transparent;
            color: var(--text-color);
            font-size: 16px;
            border-radius: 8px;
            outline: none;
            transition: all 0.3s;
        }

        .input-group input::placeholder {
            color: #bbb;
            font-style: italic;
        }

        .btn {
            font-size: 1rem;
            padding: 12px 0;
            border-radius: 8px;
            border: none;
            width: 100%;
            background: var(--primary-color);
            color: var(--text-color);
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
        }

        .btn:hover {
            background: var(--hover-color);
            transform: translateY(-2px);
        }

        .links {
            display: flex;
            justify-content: space-between;
            padding: 0 4rem;
            margin-top: 1.5rem;
            font-weight: bold;
        }

        button {
            color: var(--primary-color);
            border: none;
            background: transparent;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s;
        }

        button:hover {
            color: var(--hover-color);
            text-decoration: underline;
        }

        .company-name {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin: 0;
        }

    </style>
</head>
<body>
    <header>
      <img src="logo.png" alt="logo" width="400px">
    </header>
    <div class="container" id="signIn">
        <h1 class="form-title">Sign In</h1>
        <form method="post" action="register.php">
          <div class="input-group">
              <i class="fas fa-envelope"></i>
              <input type="email" name="email" id="email" placeholder="Email" required>
          </div>
          <div class="input-group">
              <i class="fas fa-lock"></i>
              <input type="password" name="password" id="password" placeholder="Password" required>
          </div>
         <input type="submit" class="btn" value="Sign In" name="signIn">
        </form>

        <div class="links">
          <p>Don't have account yet?</p>
          <button id="signUpButton">Sign Up</button>
        </div>
      </div>

      <div class="container" id="signup" style="display:none;">
        <h1 class="form-title">Register</h1>
        <form method="post" action="register.php">
          <div class="input-group">
             <i class="fas fa-user"></i>
             <input type="text" name="fName" id="fName" placeholder="First Name" required>
          </div>
          <div class="input-group">
              <i class="fas fa-user"></i>
              <input type="text" name="lName" id="lName" placeholder="Last Name" required>
          </div>
          <div class="input-group">
              <i class="fas fa-envelope"></i>
              <input type="email" name="email" id="email" placeholder="Email" required>
          </div>
          <div class="input-group">
              <i class="fas fa-phone"></i>
              <input type="number" name="number" id="number" placeholder="Number" required>
          </div>
          <div class="input-group">
              <i class="fas fa-lock"></i>
              <input type="password" name="password" id="password" placeholder="Password" required>
          </div>
         <input type="submit" class="btn" value="Sign Up" name="signUp">
        </form>
        <div class="links">
          <p>Already Have Account ?</p>
          <button id="signInButton">Sign In</button>
        </div>
      </div>
      <script src="script.js"></script>
</body>
</html>