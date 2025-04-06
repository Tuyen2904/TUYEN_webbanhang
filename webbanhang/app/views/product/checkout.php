<?php include 'app/views/shares/header.php'; ?>
<h1>Thanh toán</h1>

<form method="POST" action="/webbanhang/Product/processCheckout">
    <div class="form-group">
        <label for="name">Họ tên:</label>
        <input type="text" id="name" name="name" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="phone">Số điện thoại:</label>
        <input type="text" id="phone" name="phone" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="province">Tỉnh/Thành phố:</label>
        <select id="province" name="province" class="form-control" required>
            <option value="">Chọn tỉnh/thành phố</option>
            <?php foreach ($provinces as $province): ?>
                <option value="<?php echo $province->id; ?>">
                    <?php echo htmlspecialchars($province->name, ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="district">Quận/Huyện:</label>
        <select id="district" name="district" class="form-control" required>
            <option value="">Chọn quận/huyện</option>
        </select>
    </div>
    <div class="form-group">
        <label for="address_detail">Chi tiết địa chỉ (số nhà, đường):</label>
        <textarea id="address_detail" name="address_detail" class="form-control" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Thanh toán</button>
</form>
<a href="/webbanhang/Product/cart" class="btn btn-secondary mt-2">Quay lại giỏ hàng</a>

<!-- JavaScript với AJAX -->
<script>
document.getElementById('province').addEventListener('change', function() {
    const provinceId = this.value;
    const districtSelect = document.getElementById('district');
    districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';

    if (provinceId) {
        // Gửi yêu cầu AJAX đến server
        fetch(/webbanhang/Product/getDistricts/${provinceId})
            .then(response => response.json())
            .then(districts => {
                districts.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.id;
                    option.text = district.name;
                    districtSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Lỗi:', error));
    }
});
</script>

<?php include 'app/views/shares/footer.php'; ?>