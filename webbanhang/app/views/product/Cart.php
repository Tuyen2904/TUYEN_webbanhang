<?php 
// Bao gồm file header từ thư mục shares để hiển thị phần đầu trang (thường chứa HTML header, CSS, menu, v.v.)
include 'app/views/shares/header.php'; 

?>
<h1>Giỏ hàng</h1>

<?php 
// Kiểm tra xem giỏ hàng có trống không (dựa trên biến session $_SESSION['cart'])
if (empty($_SESSION['cart'])): 
?>
    <!-- Thông báo khi giỏ hàng trống -->
    <p>Giỏ hàng của bạn đang trống.</p>
    <!-- Liên kết quay lại danh sách sản phẩm -->
    <a href="/webbanhang/Product/index" class="btn btn-primary">Quay lại danh sách sản phẩm</a>
<?php 
// Nếu giỏ hàng không trống, hiển thị nội dung bên dưới
else: 
?>
    <!-- Form để cập nhật giỏ hàng: Gửi yêu cầu POST đến action updateCart -->
    <form action="/webbanhang/Product/updateCart" method="POST">
        <!-- Bảng hiển thị các sản phẩm trong giỏ hàng với class Bootstrap "table" -->
        <table class="table">
            <thead>
                <tr>
                    <th>Hình ảnh</th> 
                    <th>Tên sản phẩm</th> 
                    <th>Giá</th> 
                    <th>Số lượng</th> 
                    <th>Tổng</th> 
                    <th>Thao tác</th> 
                </tr>
            </thead>
            <tbody>
                <?php 
                // Vòng lặp foreach: Duyệt qua mảng $_SESSION['cart'] để hiển thị từng sản phẩm
                foreach ($_SESSION['cart'] as $id => $item): 
                ?>
                    <tr>
    <!-- Hiển thị hình ảnh sản phẩm -->
    <td>
        <img src="/webbanhang/uploads/<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>" 
             alt="Product Image" style="max-width: 100%; height: auto;">
    </td>
    <!-- Hiển thị tên sản phẩm, mã hóa để tránh XSS -->
    <td><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></td>
    <!-- Hiển thị giá đơn vị, thêm đơn vị VND -->
    <td><?php echo htmlspecialchars($item['price'], ENT_QUOTES, 'UTF-8'); ?> VND</td>
    <!-- Ô nhập số lượng: Cho phép người dùng chỉnh sửa số lượng -->
    <td>
        <input type="number" 
               name="quantity[<?php echo $id; ?>]" 
               value="<?php echo $item['quantity']; ?>" 
               min="1" 
               class="form-control" 
               style="width: 70px;">
    </td>
    <!-- Hiển thị tổng tiền của sản phẩm (giá x số lượng) -->
    <td><?php echo $item['price'] * $item['quantity']; ?> VND</td>
    <!-- Các nút thao tác -->
    <td>
        <!-- Nút cập nhật: Gửi form để cập nhật số lượng sản phẩm này -->
        <button type="submit" class="btn btn-warning btn-sm">Cập nhật</button>
        <!-- Nút xóa: Liên kết đến action removeFromCart với xác nhận trước khi xóa -->
        <a href="/webbanhang/Product/removeFromCart/<?php echo $id; ?>" 
           class="btn btn-danger btn-sm" 
           onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?');">Xóa</a>
    </td>
</tr>

                <?php 
                // Kết thúc vòng lặp foreach
                endforeach; 
                ?>
            </tbody>
        </table>
        <!-- Nút cập nhật toàn bộ: Gửi form để cập nhật tất cả số lượng trong giỏ hàng -->
        <button type="submit" class="btn btn-primary">Cập nhật toàn bộ giỏ hàng</button>
        <!-- Liên kết đến trang thanh toán -->
        <a href="/webbanhang/Product/checkout" class="btn btn-success">Thanh toán</a>
        <!-- Liên kết quay lại danh sách sản phẩm để tiếp tục mua sắm -->
        <a href="/webbanhang/Product/index" class="btn btn-secondary">Tiếp tục mua sắm</a>
    </form>
<?php 
// Kết thúc điều kiện if-else
endif; 
?>

<?php 
// Bao gồm file footer từ thư mục shares để hiển thị phần cuối trang (thường chứa HTML footer, script JS, v.v.)
include 'app/views/shares/footer.php'; 
?>