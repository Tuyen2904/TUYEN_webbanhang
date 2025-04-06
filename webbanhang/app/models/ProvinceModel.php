<?php
class ProvinceModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Lấy tất cả tỉnh/thành phố
    public function getProvinces() {
        $query = "SELECT * FROM provinces";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Lấy quận/huyện theo province_id
    public function getDistrictsByProvince($province_id) {
        $query = "SELECT * FROM districts WHERE province_id = :province_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':province_id', $province_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
?>