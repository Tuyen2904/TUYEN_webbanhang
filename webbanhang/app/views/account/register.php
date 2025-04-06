<?php include 'app/views/shares/header.php'; ?>

<div class="container mt-5">
    <div class="card mx-auto shadow-lg" style="max-width: 500px;">
        <div class="card-header bg-primary text-white text-center">
            <h3>Đăng ký tài khoản</h3>
        </div>
        <div class="card-body p-4">
            <?php
            if (isset($errors)) {
                echo "<ul class='alert alert-danger'>";
                foreach ($errors as $err) {
                    echo "<li>$err</li>";
                }
                echo "</ul>";
            }
            ?>

            <form action="/webbanhang/account/save" method="post">
                <div class="form-group mb-3">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Nhập tên đăng nhập">
                </div>
                <div class="form-group mb-3">
                    <label for="fullname">Họ và tên</label>
                    <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Nhập họ và tên">
                </div>
                <div class="form-group mb-3">
                    <label for="password">Mật khẩu</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu">
                </div>
                <div class="form-group mb-3">
                    <label for="confirmpassword">Xác nhận mật khẩu</label>
                    <input type="password" class="form-control" id="confirmpassword" name="confirmpassword" placeholder="Nhập lại mật khẩu">
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-success px-4">Đăng ký</button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center">
            <small>Đã có tài khoản? <a href="/webbanhang/account/login" class="text-primary">Đăng nhập</a></small>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>
