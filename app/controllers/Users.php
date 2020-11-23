<?php

class Users extends Controller {
    public function __construct(){
        $this->userModel = $this->model('User');
    }

    // Register User
    public function register(){
        // Check for post
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Process form

            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'name_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            $flagError = false;
            // Validate Name
            if(empty($data['name'])){
                $data['name_err'] = 'Please enter name';
                $flagError = true;
            }

            // Validate Email
            if(empty($data['email'])){
                $data['email_err'] = 'Please enter email';
                $flagError = true;
            }else{
                // Check email
                if($this->userModel->findUserByEmail($data['email'])){
                    $data['email_err'] = 'Email is already taken';
                }
            }

            // Validate Password
            if(empty($data['password'])){
                $data['password_err'] = 'Please enter password';
                $flagError = true;
            }else if(strlen($data['password']) < 6){
                $data['password_err'] = 'Password must be at least 6 characters';
                $flagError = true;
            }

            // Validate Confirm Password
            if(empty($data['confirm_password'])){
                $data['confirm_password_err'] = 'Please enter confirm password';
                $flagError = true;
            } else {
                if($data['password'] != $data['confirm_password']){
                    $data['confirm_password_err'] = 'Password do not match';
                    $flagError = true;
                }
            }

            
            // Make sure errors are empty
            if(!$flagError){
                echo 'SUCCESS';
                // Hash Password
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                // Register User
                if($this->userModel->register($data)){
                    flash('register_success', 'You are registered and can login');
                    redirect('users/login');
                }else{
                    die('Something went wrong');
                }
            }else{
                // Load view with error
                $this->view('users/register', $data);
            }

        }else{
            // Init data
            $data = [
                'name' => '',
                'email' => '',
                'password' => '',
                'confirm_password' => '',
                'name_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];
            // Load view
            $this->view('users/register', $data);
        }
    }

    // Login User
    public function login(){
        // Check for post
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Process form
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'email_err' => '',
                'password_err' => ''
            ];

            $flagError = false;
            // Validate Email
            if(empty($data['email'])){
                $data['email_err'] = 'Please enter email';
                $flagError = true;
            }

            // Validate Password
            if(empty($data['password'])){
                $data['password_err'] = 'Please enter password';
                $flagError = true;
            }

            // Check for user/email
            if($this->userModel->findUserByEmail($data['email'])){
                // User found
                
            }else{
                // User not found
                $data['email_err'] = 'No user found';
                $flagError = true;
            }

            // Make sure errors are empty
            if(!$flagError){
                echo 'SUCCESS';
                // Check and set logged in user
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);
                if($loggedInUser){
                    // Create Session
                    $this->createUserSession($loggedInUser);
                }else{
                    $data['password_err'] = 'Password incorrect';
                    $this->view('users/login', $data);
                }
            }else{
                // Load view with error
                $this->view('users/login', $data);
            }

        }else{
            // Init data
            $data = [
                'email' => '',
                'password' => '',
                'email_err' => '',
                'password_err' => ''
            ];
            // Load view
            $this->view('users/login', $data);
        }
    }

    public function createUserSession($user){
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->name;

        redirect('posts');
    }

    // Logout User
    public function logout(){
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        session_destroy();
        redirect('users/login');
    }

}

?>