<style>
.button {
  background-color: green;
  border: none;
  color: white;
  padding: 10px 22px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  cursor: pointer;
}
.button:hover {background-color: red;}
</style>
<?php 
	//kết nối đến CSDL
	include('./controller/connectToDatabase.php');
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	$name = $_SESSION['user_name'];
	if($name == "admin"){
		$sql_select_all_action = " SELECT gia_phong_tro.user_name, gia_phong_tro.IDPhongTro, gia_phong_tro.KieuVeSinh, gia_phong_tro.TieuDe, gia_phong_tro.DienTich, gia_phong_tro.GiaChoThue, gia_phong_tro.ThoiGianDang AS ThoiGian, dia_chi_phong_tro.DiaChi, dia_chi_phong_tro.TenChuTro, dia_chi_phong_tro.Sdt FROM gia_phong_tro, dia_chi_phong_tro WHERE gia_phong_tro.IDPhongTro=dia_chi_phong_tro.IDPhongTro ";
	}
	else{
		$sql_select_all_action = 'SELECT gia_phong_tro.user_name, gia_phong_tro.IDPhongTro, gia_phong_tro.KieuVeSinh, gia_phong_tro.TieuDe, gia_phong_tro.DienTich, gia_phong_tro.GiaChoThue, gia_phong_tro.ThoiGianDang AS ThoiGian, dia_chi_phong_tro.DiaChi, dia_chi_phong_tro.TenChuTro, dia_chi_phong_tro.Sdt FROM gia_phong_tro, dia_chi_phong_tro WHERE gia_phong_tro.IDPhongTro=dia_chi_phong_tro.IDPhongTro AND gia_phong_tro.user_name = "' .$name. '"';
	}

	if(!isset($_GET['sorting_time']) && !isset($_GET['sorting_price'])) {
		$sql_select_all_action = $sql_select_all_action. 'ORDER BY gia_phong_tro.ThoiGianDang DESC';
	}

	if(isset($_GET['sorting_time'])) { //lấy giá trị (nếu có) của phần sắp xếp phòng trọ theo thời gian và thêm vào câu lệnh sql
		if($_GET['sorting_time'] == "Mới nhất") {
			$sql_select_all_action = $sql_select_all_action. 'ORDER BY gia_phong_tro.ThoiGianDang DESC';
		} else if($_GET['sorting_time'] == "Cũ nhất") {
			$sql_select_all_action = $sql_select_all_action. 'ORDER BY gia_phong_tro.ThoiGianDang ASC';
		}
	}
	if(isset($_GET['sorting_price'])) { //Lấy giá trị (nếu có) của phần sắp xếp phòng trọ theo giá và thêm vào câu lệnh sql
		if($_GET['sorting_price'] == "Rẻ nhất") {
			$sql_select_all_action = $sql_select_all_action. 'ORDER BY gia_phong_tro.GiaChoThue ASC';
		} else if($_GET['sorting_price'] == "Đắt nhất") {
			$sql_select_all_action = $sql_select_all_action. 'ORDER BY gia_phong_tro.GiaChoThue DESC';
		}
	}
	$row_of_results = 0;
	if($result_all = mysqli_query($conn, $sql_select_all_action)) {
		$row_of_results = mysqli_num_rows($result_all); //Số lượng căn phòng đã đăng
	}


	$result_per_page = 10; //Số lượng bài đăng của một trang

	$number_of_pages = ceil($row_of_results/$result_per_page); //Số trang hiển thị

	//Kiểm tra nếu trang không có biến page thì là trang số 1
	if(!isset($_GET['page'])) {
		$page = 1;
	} else {
		$page = $_GET['page'];
	}

	//Kết quả đầu tiên trả về của trang
	$this_page_first_result = ($page-1)*$result_per_page;

	//Câu lệnh sql để hiển thị các phòng của mỗi trang
	$sql_select_each_page = $sql_select_all_action. ' LIMIT ' .$this_page_first_result. ',' .$result_per_page;
	$result_each_page = mysqli_query($conn, $sql_select_each_page);



	//Hiển thị các phòng tương ứng
	while($row = mysqli_fetch_assoc($result_each_page)) {
?>
		<div class="col-xs-12">
			<div class="row">
				<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6">
					<a href="ChiTietCanPhong.php?id=<?php echo $row['IDPhongTro']; ?>" class="thumbnail">
						<?php
							$sql_select_image = 'SELECT DuongDan FROM hinh_anh_phong_tro WHERE IDPhongTro=' .$row['IDPhongTro']. ' LIMIT 1';
							$result_img = mysqli_query($conn, $sql_select_image);
							if(mysqli_num_rows($result_img) > 0) {
								while ($row_img = mysqli_fetch_assoc($result_img)) {
									echo '<img src="' .$row_img['DuongDan']. '" style="width: 100%; height: 180px;">';
								}
							}
							else echo '<img src="images/icon-acount.png" style="width: 100%; height: 100%;">';
						?>
					</a>
				</div>
				<div class="col-lg-9col-md-8 col-sm-8 col-xs-12">
					<div class="row">
						<a href="ChiTietCanPhong.php?id=<?php echo $row['IDPhongTro']; ?>" class="col-xs-12 link simple_room_info_line">
							<h3 style="margin-top: 10px;"><?php echo $row['TieuDe']; ?></h3>
						</a>
						<b class="col-xs-12 simple_room_info_line"> 
							<span style="color: green;">Địa chỉ: </span> 
							<span><?php echo $row['DiaChi']; ?></span>
						</b>
						<b class="col-sm-6 col-xs-12 simple_room_info_line">
							<span style="color: green">Diện tích: </span>
							<span><?php echo $row['DienTich']; ?> m<sup>2</sup></span>
						</b>
						<b class="col-sm-6 col-xs-12 simple_room_info_line">
							<span style="color: green;">Vệ sinh: </span>
							<span><?php echo $row['KieuVeSinh']; ?></span>
						</b>
						<b class="col-sm-6 col-xs-12 simple_room_info_line">
							<span style="color: green;">Tên chủ trọ: </span>
							<span><?php echo $row['TenChuTro']; ?></span>
						</b>
						<b class="col-sm-6 col-xs-12 simple_room_info_line">
							<span style="color: green;">Sđt liên hệ: </span>
							<span><?php echo $row['Sdt']; ?></span>
						</b>
						<b class="col-sm-6 col-xs-12 simple_room_info_line">
							<span style="color: green;">Giá: </span>
							<span><?php echo $row['GiaChoThue']; ?> đồng/tháng</span>
						</b>
						<b class="col-sm-6 col-xs-12 simple_room_info_line">
							<span style="color: green;">Tài khoản đăng: </span>
							<span><?php echo $row['user_name']; ?></span>
						</b>
						<b class="col-lg-6 col-xs-12 simple_room_info_line">
							<a href="deleteRoom.php?id=<?php echo $row['IDPhongTro']; ?>" onclick="return  confirm('Bạn chắc chắn muốn xóa bài đăng này chứ?')"><button class="button">Xóa bài</button></a>
						</b>
						<p class="col-lg-12 col-xs-12 text-right simple_room_info_line" style="color: gray">
							<?php 
								$ThoiGian = $row['ThoiGian'];
								$now =  date('Y-m-d H:i:s');
								$diff = strtotime($now) - strtotime($ThoiGian);
								if($diff < 60) {
									echo round($diff) ;
									echo " giây trước";
								} else if($diff < 60*60) {
									echo round($diff/60);
									echo " phút trước";
								} else if($diff < 60*60*24) {
									echo round($diff/60/60);
									echo " giờ trước";
								} else if($diff < 60*60*24*30) {
									echo round($diff/60/60/24);
									echo " ngày trước";
								} else if($diff < 60*60*24*30*12) {
									echo round($diff/60/60/24/30);
									echo " tháng trước";
								} else {
									echo round($diff/60/60/24/30/12);
									echo " năm trước";
								}
							?> 
						</p>
					</div>
				</div>
			</div>
		</div> <!-- Hết 1 bài đăng -->
		<?php
	}
?>