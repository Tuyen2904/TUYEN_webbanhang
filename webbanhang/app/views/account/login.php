<?php include 'app/views/shares/header.php'; ?>

<section class="vh-100 gradient-custom d-flex align-items-center">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="card bg-dark text-white shadow-lg" style="border-radius: 15px;">
                    <div class="card-body p-5 text-center">
                        <h2 class="fw-bold mb-4 text-uppercase">Login</h2>
                        <p class="text-white-50 mb-4">Please enter your username and password!</p>

                        <form action="/webbanhang/account/checklogin" method="post">
                            <div class="form-outline mb-4">
                                <label class="form-label" for="username">Username</label>
                                <input type="text" name="username" id="username" class="form-control form-control-lg" placeholder="Enter your username" required />
                            </div>

                            <div class="form-outline mb-4">
                                <label class="form-label" for="password">Password</label>
                                <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="Enter your password" required />
                            </div>

                            <div class="d-flex justify-content-between mb-4">
                                <a href="#!" class="text-white-50">Forgot password?</a>
                                <a href="/webbanhang/account/register" class="text-white-50">Sign Up</a>
                            </div>

                            <button type="submit" class="btn btn-outline-light btn-lg px-5">Login</button>
                        </form>

                        <div class="mt-4 pt-2">
                            <p class="mb-0">Or login with:</p>
                            <div class="d-flex justify-content-center mt-3">
                                <a href="#!" class="text-white me-3"><i class="fab fa-facebook-f fa-lg"></i></a>
                                <a href="#!" class="text-white me-3"><i class="fab fa-twitter fa-lg"></i></a>
                                <a href="#!" class="text-white"><i class="fab fa-google fa-lg"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'app/views/shares/footer.php'; ?>
