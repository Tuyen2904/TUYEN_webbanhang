<?php
// Require các file cần thiết để sử dụng database, model
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');

class ProductController
{
    private $productModel; // Biến lưu trữ instance của ProductModel để truy cập dữ liệu sản phẩm
    private $provinceModel; // Biến lưu trữ dữ liệu địa chỉ
    private $db; // Biến lưu trữ kết nối database

    // Hàm khởi tạo: Thiết lập kết nối database và khởi tạo ProductModel
    public function __construct()
    {
        $this->db = (new Database())->getConnection(); // Lấy kết nối từ class Database
        $this->productModel = new ProductModel($this->db); // Khởi tạo ProductModel với kết nối database
        
    }
    // API lấy quận/huyện theo province_id (dùng cho AJAX)
    public function getDistricts($province_id) {
        header('Content-Type: application/json'); // Trả về dữ liệu dạng JSON
        $districts = $this->provinceModel->getDistrictsByProvince($province_id);
        echo json_encode($districts); // Gửi danh sách quận/huyện
        exit;
    }
    // Hiển thị danh sách tất cả sản phẩm
    public function index()
    {
        $products = $this->productModel->getProducts(); // Lấy danh sách sản phẩm từ ProductModel
        include 'app/views/product/list.php'; // Hiển thị giao diện danh sách sản phẩm
    }

    // Hiển thị chi tiết một sản phẩm dựa trên ID
    public function show($id)
    {
        $product = $this->productModel->getProductById($id); // Lấy thông tin sản phẩm theo ID
        if ($product) {
            include 'app/views/product/show.php'; // Nếu tìm thấy, hiển thị giao diện chi tiết sản phẩm
        } else {
            echo "Không thấy sản phẩm."; // Thông báo nếu không tìm thấy sản phẩm
        }
    }

    // Hiển thị form thêm sản phẩm mới
    public function add()
    {
        $categories = (new CategoryModel($this->db))->getCategories(); // Lấy danh sách danh mục từ CategoryModel
        include_once 'app/views/product/add.php'; // Hiển thị giao diện form thêm sản phẩm
    }

    // Cập nhật số lượng sản phẩm trong giỏ hàng
    public function updateCart()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantity'])) { // Kiểm tra yêu cầu POST và dữ liệu quantity
            foreach ($_POST['quantity'] as $id => $quantity) { // Duyệt qua mảng số lượng gửi từ form
                if (!is_numeric($quantity) || $quantity < 1) { // Kiểm tra số lượng hợp lệ
                    $quantity = 1; // Nếu không hợp lệ, đặt về 1
                }
                if (isset($_SESSION['cart'][$id])) { // Nếu sản phẩm tồn tại trong giỏ hàng
                    $_SESSION['cart'][$id]['quantity'] = (int)$quantity; // Cập nhật số lượng mới
                }
            }
            header('Location: /webbanhang/Product/cart'); // Chuyển hướng về trang giỏ hàng
        } else {
            echo "Yêu cầu không hợp lệ."; // Thông báo nếu yêu cầu không đúng
        }
    }

    // Xóa một sản phẩm khỏi giỏ hàng dựa trên ID
    public function removeFromCart($id)
    {
        if (!$id || !is_numeric($id)) { // Kiểm tra ID hợp lệ
            echo "ID sản phẩm không hợp lệ.";
            return;
        }
        if (isset($_SESSION['cart'][$id])) { // Nếu sản phẩm tồn tại trong giỏ hàng
            unset($_SESSION['cart'][$id]); // Xóa sản phẩm khỏi giỏ hàng
        }
        header('Location: /webbanhang/Product/cart'); // Chuyển hướng về trang giỏ hàng
    }

    // Lưu sản phẩm mới vào database
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Kiểm tra yêu cầu POST
            $name = $_POST['name'] ?? ''; // Lấy tên sản phẩm từ form
            $description = $_POST['description'] ?? ''; // Lấy mô tả
            $price = $_POST['price'] ?? ''; // Lấy giá
            $category_id = $_POST['category_id'] ?? null; // Lấy ID danh mục
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) { // Kiểm tra file hình ảnh
                $image = $this->uploadImage($_FILES['image']); // Upload hình ảnh nếu có
            } else {
                $image = ""; // Nếu không có hình ảnh, để trống
            }
            $result = $this->productModel->addProduct($name, $description, $price, $category_id, $image); // Thêm sản phẩm vào database
            if (is_array($result)) { // Nếu có lỗi (trả về mảng lỗi)
                $errors = $result;
                $categories = (new CategoryModel($this->db))->getCategories(); // Lấy lại danh mục để hiển thị form
                include 'app/views/product/add.php'; // Hiển thị lại form với lỗi
            } else {
                header('Location: /webbanhang/Product'); // Chuyển hướng về danh sách sản phẩm nếu thành công
            }
        }
    }

    // Hiển thị form chỉnh sửa sản phẩm
    public function edit($id)
    {
        $product = $this->productModel->getProductById($id); // Lấy thông tin sản phẩm theo ID
        $categories = (new CategoryModel($this->db))->getCategories(); // Lấy danh sách danh mục
        if ($product) {
            include 'app/views/product/edit.php'; // Hiển thị form chỉnh sửa nếu tìm thấy sản phẩm
        } else {
            echo "Không thấy sản phẩm."; // Thông báo nếu không tìm thấy
        }
    }

    // Cập nhật thông tin sản phẩm vào database
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Kiểm tra yêu cầu POST
            $id = $_POST['id']; // Lấy ID sản phẩm
            $name = $_POST['name']; // Lấy tên mới
            $description = $_POST['description']; // Lấy mô tả mới
            $price = $_POST['price']; // Lấy giá mới
            $category_id = $_POST['category_id']; // Lấy ID danh mục mới
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) { // Kiểm tra file hình ảnh mới
                $image = $this->uploadImage($_FILES['image']); // Upload hình ảnh nếu có
            } else {
                $image = $_POST['existing_image']; // Giữ hình ảnh cũ nếu không upload mới
            }
            $edit = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image); // Cập nhật sản phẩm
            if ($edit) {
                header('Location: /webbanhang/Product'); // Chuyển hướng về danh sách nếu thành công
            } else {
                echo "Đã xảy ra lỗi khi lưu sản phẩm."; // Thông báo nếu có lỗi
            }
        }
    }

    // Xóa sản phẩm khỏi database
    public function delete($id)
    {
        if ($this->productModel->deleteProduct($id)) { // Xóa sản phẩm theo ID
            header('Location: /webbanhang/Product'); // Chuyển hướng về danh sách nếu thành công
        } else {
            echo "Đã xảy ra lỗi khi xóa sản phẩm."; // Thông báo nếu có lỗi
        }
    }

    // Hàm riêng: Upload hình ảnh sản phẩm
    private function uploadImage($file)
    {
        $target_dir = "uploads/"; // Thư mục lưu trữ hình ảnh
        if (!is_dir($target_dir)) { // Kiểm tra và tạo thư mục nếu chưa tồn tại
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($file["name"]); // Đường dẫn file đích
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION)); // Lấy định dạng file
        $check = getimagesize($file["tmp_name"]); // Kiểm tra file có phải hình ảnh không
        if ($check === false) {
            throw new Exception("File không phải là hình ảnh.");
        }
        if ($file["size"] > 10 * 1024 * 1024) { // Kiểm tra kích thước file (tối đa 10MB)
            throw new Exception("Hình ảnh có kích thước quá lớn.");
        }
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") { // Kiểm tra định dạng cho phép
            throw new Exception("Chỉ cho phép các định dạng JPG, JPEG, PNG và GIF.");
        }
        if (!move_uploaded_file($file["tmp_name"], $target_file)) { // Di chuyển file vào thư mục uploads
            throw new Exception("Có lỗi xảy ra khi tải lên hình ảnh.");
        }
        return $target_file; // Trả về đường dẫn file đã upload
    }

    // Thêm sản phẩm vào giỏ hàng
    public function addToCart($id)
    {
        $product = $this->productModel->getProductById($id); // Lấy thông tin sản phẩm theo ID
        if (!$product) {
            echo "Không tìm thấy sản phẩm.";
            return;
        }
        if (!isset($_SESSION['cart'])) { // Nếu giỏ hàng chưa tồn tại, khởi tạo
            $_SESSION['cart'] = [];
        }
        if (isset($_SESSION['cart'][$id])) { // Nếu sản phẩm đã có trong giỏ
            $_SESSION['cart'][$id]['quantity']++; // Tăng số lượng lên 1
        } else { // Nếu sản phẩm chưa có
            $_SESSION['cart'][$id] = [ // Thêm sản phẩm mới vào giỏ
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image
            ];
        }
        header('Location: /webbanhang/Product/cart'); // Chuyển hướng về trang giỏ hàng
    }

    // Hiển thị giỏ hàng
    public function cart()
    {
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : []; // Lấy dữ liệu giỏ hàng từ session
        include 'app/views/product/cart.php'; // Hiển thị giao diện giỏ hàng
    }

    // Hiển thị form thanh toán
    public function checkout()
    {
        
        include 'app/views/product/checkout.php'; // Hiển thị giao diện thanh toán
    }

    // Xử lý đơn hàng khi thanh toán
    public function processCheckout()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $phone = $_POST['phone'];
            $province_id = $_POST['province']; // ID tỉnh
            $district_id = $_POST['district']; // ID quận
            $address_detail = $_POST['address_detail'];
    
            // Lấy tên tỉnh và quận từ database
            $province_stmt = $this->db->prepare("SELECT name FROM provinces WHERE id = :id");
            $province_stmt->bindParam(':id', $province_id);
            $province_stmt->execute();
            $province_name = $province_stmt->fetchColumn();
    
            $district_stmt = $this->db->prepare("SELECT name FROM districts WHERE id = :id");
            $district_stmt->bindParam(':id', $district_id);
            $district_stmt->execute();
            $district_name = $district_stmt->fetchColumn();
    
            // Ghép địa chỉ đầy đủ
            $full_address = "$address_detail, $district_name, $province_name";
    
            if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
                echo "Giỏ hàng trống.";
                return;
            }
    
            $this->db->beginTransaction();
            try {
                $query = "INSERT INTO orders (name, phone, address) VALUES (:name, :phone, :address)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':address', $full_address);
                $stmt->execute();
                $order_id = $this->db->lastInsertId();
    
                $cart = $_SESSION['cart'];
                foreach ($cart as $product_id => $item) {
                    $query = "INSERT INTO order_details (order_id, product_id, quantity, price) 
                             VALUES (:order_id, :product_id, :quantity, :price)";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':order_id', $order_id);
                    $stmt->bindParam(':product_id', $product_id);
                    $stmt->bindParam(':quantity', $item['quantity']);
                    $stmt->bindParam(':price', $item['price']);
                    $stmt->execute();
                }
    
                unset($_SESSION['cart']);
                $this->db->commit();
                header('Location: /webbanhang/Product/orderConfirmation');
            } catch (Exception $e) {
                $this->db->rollBack();
                echo "Đã xảy ra lỗi khi xử lý đơn hàng: " . $e->getMessage();
            }
        }
    }
    public function search() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['keyword'])) {
            $keyword = trim($_GET['keyword']); // Lấy từ khóa và loại bỏ khoảng trắng thừa
            if (!empty($keyword)) {
                $products = $this->productModel->searchProducts($keyword); // Tìm sản phẩm
            } else {
                $products = $this->productModel->getProducts(); // Nếu không có từ khóa, lấy tất cả
            }
        } else {
            $products = $this->productModel->getProducts(); // Mặc định lấy tất cả nếu không có yêu cầu
        }
        include 'app/views/product/list.php'; // Hiển thị kết quả trong list.php
    }

    // Hiển thị trang xác nhận đơn hàng
    public function orderConfirmation()
    {
        include 'app/views/product/orderConfirmation.php'; // Hiển thị giao diện xác nhận đơn hàng
    }
}
?>