<?php
require_once('app/config/database.php');
require_once('app/models/AccountModel.php');

class AccountController
{
    private $accountModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->accountModel = new AccountModel($this->db);
    }

    public function register()
    {
        include_once 'app/views/account/register.php';
    }

    public function login()
    {
        include_once 'app/views/account/login.php';
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'] ?? '';
            $fullName = $_POST['fullname'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirmpassword'] ?? '';
            $errors = [];

            // Kiểm tra dữ liệu nhập vào
            if (empty($username)) {
                $errors['username'] = "Vui lòng nhập Username!";
            }
            if (empty($fullName)) {
                $errors['fullname'] = "Vui lòng nhập Họ tên!";
            }
            if (empty($password)) {
                $errors['password'] = "Vui lòng nhập mật khẩu!";
            }
            if ($password != $confirmPassword) {
                $errors['confirmPass'] = "Mật khẩu xác nhận không khớp!";
            }

            // Kiểm tra username đã tồn tại chưa
            $account = $this->accountModel->getAccountByUsername($username);
            if ($account) {
                $errors['account'] = "Tài khoản này đã được đăng ký!";
            }

            if (count($errors) > 0) {
                include_once 'app/views/account/register.php';
            } else {
                $password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $result = $this->accountModel->save($username, $fullName, $password);

                if ($result) {
                    header('Location: /webbanhang/account/login');
                    exit;
                }
            }
        }
    }

    public function logout()
    {
        unset($_SESSION['username']);
        unset($_SESSION['role']);
        header('Location: /webbanhang/product');
    }

    public function checkLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $account = $this->accountModel->getAccountByUsername($username);

            if ($account) {
                $pwd_hashed = $account->password;

                // Kiểm tra mật khẩu
                if (password_verify($password, $pwd_hashed)) {
                    session_start();
                    $_SESSION['username'] = $account->username;
                    header('Location: /webbanhang/product');
                    exit;
                } else {
                    echo "Mật khẩu không đúng.";
                }
            } else {
                echo "Không tìm thấy tài khoản.";
            }
        }
    }
}
?>
