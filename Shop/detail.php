<?php
    require "database.php";
    require "Classes/User.php";
    require "Classes/But.php";
	require "Classes/QuaDeBan.php";
    require "Classes/QuaTangPhaLe.php";
    require "Classes/GauBong.php";
    require "Classes/SanhSu.php";
    require "Classes/TienIch.php";
    require "Classes/MocKhoa.php";
    require "Classes/DoMyNghe.php";
	session_start();

    $sql = "SELECT * from Product";
    $result = $db->query($sql)->fetch_all() ;

    /*==========================================================Dang nhap=================================================*/
    $sql2 = "SELECT * from user";
    $result2 = $db->query($sql2)->fetch_all();
    $check=true;

    if (isset($_POST['login'])){
        $username = addslashes($_POST['accountLogin']);
        $password = addslashes($_POST['passLogin']);
        if (!$username || !$password) {
            ?>
            <script>
                alert("Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu!");
            </script>
            <?php
        }
        for($i = 0; $i < count($result2); $i++) { 
            if($username==$result2[$i][2] && $password==$result2[$i][3]){
                $check=true;
                if($result2[$i][4]=="admin"){
                    $log=true;
                    $_SESSION['log'] = $log;
                    $_SESSION['name'] = $username;
                    break;
                }
                else{
                    $log=false;
                    $_SESSION['log'] = $log;
                    $_SESSION['name'] = $username;
                    break;
                }
            }
            else{
                $check=false;
            }
        }
        if ($check==false){
            ?>
            <script>
                alert("Tên đăng nhập hoặc mật khẩu không đúng!");
            </script>
            <?php
        }
    }

    /*===========================================================Dang ki===================================================*/
    if (isset($_POST['register'])){
    $username = addslashes($_POST['nameRegister']);
    $useraccount = addslashes($_POST['accountRegister']);
    $password = addslashes($_POST['passRegister']);
    if (!$username || !$useraccount || !$password) {
        echo "Please input all information. <a href='javascript: history.go(-1)'>Trở lại</a>";
        exit;
    }
    for($i = 0; $i < count($result2); $i++) { 
        if($username==$result2[$i][1] && $useraccount==$result2[$i][2] && $password==$result2[$i][3]){
            $check=true;
            ?>
                <script>
                    alert("Tài khoản đã tồn tại!");
                </script>
                <?php
        }
        else{
            $check=false;
        }
    }
    if ($check==false){
        $pos="user";
        $sql2 = "INSERT into User values(null,'".$username."','".$useraccount."','".$password."','".$pos."')";
        $db->query($sql2);
        ?>
        <script>
            alert("Đăng kí thành công!");
        </script>
        <?php
    }
    } 
    /*========================================================Log out======================================================*/   
    if(isset($_POST['logout'])){
        unset($_SESSION['name']);
        $_SESSION['log']=false;
        header("index.php");
    }

    $vitri= $_GET['detail']; 
    if ($_SESSION['name']!="") {
        $sql1 = "SELECT * from cart where cart.idUser=(select id from User where username ='".$_SESSION['name']."')";
        $result1 = $db->query($sql1)->fetch_all();
    }
    
    /*===================================================Them vao doi tuong================================================*/
	$Products = array();
	for($i = 0; $i < count($result); $i++) {
		$Product = $result[$i];
		if($Product[4] == 'QUÀ ĐỂ BÀN'){
			array_push($Products, new QuaDeBan($Product[0], $Product[1], $Product[2], $Product[3], $Product[5]));
		}
		else 
		if($Product[4] == 'QUÀ TẶNG PHA LÊ'){
			array_push($Products, new QuaTangPhaLe($Product[0], $Product[1], $Product[2], $Product[3], $Product[5]));
        }
        else 
		if($Product[4] == 'GẤU BÔNG'){
			array_push($Products, new GauBong($Product[0], $Product[1], $Product[2], $Product[3], $Product[5]));
        }
        else 
		if($Product[4] == 'MÓC KHÓA'){
			array_push($Products, new MocKhoa($Product[0], $Product[1], $Product[2], $Product[3], $Product[5]));
        }
        else 
		if($Product[4] == 'BÚT'){
			array_push($Products, new But($Product[0], $Product[1], $Product[2], $Product[3], $Product[5]));
        }
        else 
		if($Product[4] == 'TIỆN ÍCH'){
			array_push($Products, new TienIch($Product[0], $Product[1], $Product[2], $Product[3], $Product[5]));
        }
        else 
		if($Product[4] == 'ĐỒ MỸ NGHỆ'){
			array_push($Products, new DoMyNghe($Product[0], $Product[1], $Product[2], $Product[3], $Product[5]));
        }
        else 
		if($Product[4] == 'SÀNH SỨ'){
			array_push($Products, new SanhSu($Product[0], $Product[1], $Product[2], $Product[3], $Product[5]));
		}
    }
    /*===============================================User them san pham vao gio hang======================================*/    
    if(isset($_POST["insert_cart"])){
        if(($_SESSION['name'])==""){
         ?>
          <script>
            alert("Bạn chưa đăng nhập! Đăng nhập để thêm sản phẩm vào giỏ hàng!");
          </script>
         <?php
        }
        else{
            $i=$_POST["insert_cart"]-1;     
            $id=$i+1;
            $check=false;
            $sql0 = "SELECT id from User where username ='".$_SESSION['name']."'";
            $ktraUser=$db->query($sql0)->fetch_all();
            $idUser= $ktraUser[0][0];

            echo $idUser;

            for($j = 0; $j < count($result1); $i++) {
                if ($result1[$j][1]==$id){
                    $check=true;
                    $sql1 = "UPDATE cart SET quantity = ".($result1[$j][5]+1).", total=".($result1[$j][5]+1)*($result1[$j][4])." WHERE id_pr=".$id;
                    $db->query($sql1);
                }
                else{
                    break;
                    }
            }      

            if($check==false){
                $img=$result[$i][2];
                $price=$result[$i][3];
                $name=$result[$i][1];
                $quantity=1;
                $total=$price*$quantity;
                ECHO $idUser;
                $sql1 = "INSERT into cart values(null,".$id.",'".$img."','".$name."',".$price.",".$quantity.",".$total.",".$idUser.")";
                $db->query($sql1);  
            }
        }
    }
  
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>My Shopping Cart</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.2/css/all.css" integrity="sha384-/rXc/GQVaYpyDdyxK+ecHPVYJSN9bmVFBvjA/9eOB+pb3F2w2N6fc5qB9Ew5yIns" crossorigin="anonymous">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.2/css/all.css" integrity="sha384-/rXc/GQVaYpyDdyxK+ecHPVYJSN9bmVFBvjA/9eOB+pb3F2w2N6fc5qB9Ew5yIns" crossorigin="anonymous">
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <style>
            * {
                margin: 0;
                padding: 0;
            }    
            .header-container {
                background: brown;
                height: 40px;
            }      
            .container_header {
                margin: 0 100px 20px 100px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .header_left {
                align-content: center;
                align-items: center;
                color: white;
                margin-top: 5px;
            }         
            .header_right {
                display: flex;
                justify-content: space-between;
                align-content: center;
            }
            .items img {
                width: 20px;
                height: 20px;
                margin-top: 5px;
                margin-bottom: 5px;
            }
            /*============================*/
            .clearfix:after {
                display: block;
                clear: both;
            }
            .wrapper {
                width: 100%;
                background: white;
                display: flex;
                justify-content: space-between;
                box-shadow: 0px 3px 3px #ccc;
            }
            /*----- Phần menu -----*/           
            .menu {
                position: relative;
                margin: 0px auto;
                background: white;
                height: 50px;
                margin-top: 20px;
                margin-bottom: 5px;
                font-weight:bold;
            }       
            .menu li {
                margin: 0px;
                list-style: none;
                font-family: 'Ek Mukta';
            }         
            .menu a {
                transition: all linear 0.15s;
                color: black;
                text-decoration: none;
            }
            .menu .arrow {
                font-size: 11px;
                line-height: 0%;
            }
            /*----- css cho phần menu cha -----*/         
            .menu > ul > li {
                display: inline-block;
                position: relative;
                font-size: 15px;
            }
            .menu > ul > li > a {
                padding: 10px 40px;
                display: inline-block;
                color: black;
            }       
            .menu > ul > li:hover > a,
            .menu > ul > .current-item > a {
                color: mediumblue;
            }
            /*----css cho menu con----*/
            .menu li:hover .sub-menu {
                z-index: 1;
                opacity: 1;
            }       
            .sub-menu {
                width: 160%;
                padding: 5px 0px;
                position: absolute;
                top: 100%;
                left: 0px;
                z-index: -1;
                opacity: 0;
                transition: opacity linear 0.15s;
                box-shadow: 0px 2px 3px rgba(0, 0, 0, 0.2);
                background: white;
            }         
            .sub-menu li {
                display: block;
                font-size: 15px;
            }        
            .sub-menu li a {
                padding: 10px 30px;
                color: orangered;
                display: block;
            }      
            .sub-menu li button {
                padding: 10px 30px;
                color: orangered;
                display: block;
            }       
            .sub-menu li a:hover,
            .sub-menu .current-item a,
            .sub-menu li button:hover {
                color: mediumblue;
            }
            /*===========================*/          
            #searchbox {
                width: 240px;
            }
            #searchbox input {
                outline: none;
            }      
            input:focus::-webkit-input-placeholder {
                color: transparent;
            }       
            input:focus:-moz-placeholder {
                color: transparent;
            }
            input:focus::-moz-placeholder {
                color: transparent;
            }      
            #searchbox input[type="text"] {
                background: url(http://2.bp.blogspot.com/-xpzxYc77ack/VDpdOE5tzMI/AAAAAAAAAeQ/TyXhIfEIUy4/s1600/search-dark.png) no-repeat 10px 13px #f2f2f2;
                border: 2px solid #f2f2f2;
                font: bold 12px Arial, Helvetica, Sans-serif;
                color: #6A6F75;
                width: 160px;
                padding: 14px 17px 12px 30px;
                -webkit-border-radius: 5px 0px 0px 5px;
                -moz-border-radius: 5px 0px 0px 5px;
                border-radius: 5px 0px 0px 5px;
                text-shadow: 0 2px 3px #fff;
                -webkit-transition: all 0.7s ease 0s;
                -moz-transition: all 0.7s ease 0s;
                -o-transition: all 0.7s ease 0s;
                transition: all 0.7s ease 0s;
            }
            #searchbox input[type="text"]:focus {
                background: #f7f7f7;
                border: 2px solid #f7f7f7;
                width: 200px;
                padding-left: 10px;
            }   
            #button-submit {
                background: url(http://4.bp.blogspot.com/-slkXXLUcxqg/VEQI-sJKfZI/AAAAAAAAAlA/9UtEyStfDHw/s1600/slider-arrow-right.png) no-repeat;
                margin-left: -40px;
                border-width: 0px;
                width: 43px;
                height: 45px;
            }
            /*==============================*/
                 
             .top_body_content {
                display: flex;
                justify-content: space-between;
                margin-top: 20px;
                margin-left: 50px;
                margin-right: 50px;
                align-items: center;
            }    
            .top_body_content a {
                text-decoration: none;
            }   
            
            /*===========Doi tac va lien he=====================*/
            .dt-grid-container {
                display: grid;
                grid-template-columns: auto auto auto auto auto auto;
                grid-gap: 5px;
                margin-left: 50px;
                margin-right: 50px;
            }     
            .thumbnail {
                width: 180px;
                height: 150px;
                overflow: hidden;
                border: none;
            }   
            .thumbnail img {
                width: 100%;
                height: 100%;
                transition-duration: 0.3s;
            }   
            .thumbnail img:hover {
                transform: scale(1.2);
            }
            /*================================*/   
            .numbertext {
                display: flex;
            }
            /*==========================*/
            .detail_container{
                border: solid;
                border-color:lightgray;
                border-width: 1px;
                margin-left: 100px;
                margin-right: 100px;
                box-shadow: 3px 3px 3px darkgrey;
                margin-top: 50px;
                display: flex;
                justify-content: space-between;
                margin-bottom: 50px;
            }

            .flex1{
               flex: 1;
               text-align: center;
               align-items: center;
               margin-bottom: 30px;
            }
            .flex2{
                margin-left: 150px;
                margin-top: 20px;
                flex: 1;
            }
            .detail_add{
                margin-top: 50px; 
                margin-left: 100px; 
                border-style: solid;
                border-color:firebrick; 
                border-radius:5px; 
                border-width: 1px;
            }
            .detail_add:hover{
                background:firebrick;
                border-color: black;
                color: white;
            }
            /*=============================*/
            
        </style>
    </head>
    <body>
        <div class="header-container">
            <!--===============================================HEAD====================================================-->
            <div class="container_header">
                <div class="header_left">
                    <p>
                        <span>Địa chỉ:</span>101B Lê Hữu Trác, Phước Mỹ, Sơn Trà, Đà Nẵng
                    </p>
                </div>
                <div class="header_right">
                    <div class="items">
                        <img src="Souvernir/fb.png">
                    </div>
                    &emsp;
                    <div class="items">
                        <img src="Souvernir/gplus.png">
                    </div>
                    &emsp;
                    <div class="items">
                        <img src="Souvernir/linkedin.png">
                    </div>
                    &emsp;
                    <div class="items">
                        <img src="Souvernir/stumbleupon.png">
                    </div>
                    &emsp;
                    <div class="items">
                        <img src="Souvernir/twitter.png">
                    </div>
                    &emsp;
                    <div class="items">
                        <img src="Souvernir/zing.png">
                    </div>
                   
                </div>
            </div>
            <!--===============================================NAME SHOP================================================-->
            <nav class="navbar navbar-dark primary-color">
                <div style="position:relative; margin: auto;">
                    <img src="Souvernir/souvernirlogo.png" style="width: 300px; height: 80px;">
                </div>
            </nav>
            <!--===============================================MENU BAR=================================================-->
            <div class="wrapper">
                        <nav class="menu" style="float: left;">
                            <ul class="clearfix">
                                <li>
                                    <a href="index.php">TRANG CHỦ</a>
                                </li>
                                <li>
                                    <a href="#">SẢN PHẨM <span class="arrow">&#9660;</span></a>
                                    <ul class="sub-menu">
                                        <li><a href="#">QUÀ ĐỂ BÀN</a></li>
                                        <li><a href="#">QUÀ TẶNG PHA LÊ</a></li>
                                        <li><a href="#">GẤU BÔNG</a></li>
                                        <li><a href="#">MÓC KHÓA</a></li>
                                        <li><a href="#">BÚT</a></li>
                                        <li><a href="#">TIỆN ÍCH</a></li>
                                        <li><a href="#">ĐỒ MĨ NGHỆ</a></li>
                                        <li><a href="#">SẢN PHẨM SÀNH SỨ</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="introduce.php">GIỚI THIỆU</a>
                                </li>
                                <li>
                                    <a href="#">LIÊN HỆ</a>
                                </li>
                            </ul>
                            </nav>
                                <div class="menu" style="float: right;">
                                <ul class="clearfix">
                                    <li>
                                        <img src="Souvernir/taikhoan.png" style="width: 30px; height: 30px;">
                                        <ul class="sub-menu" style="width: 200px;">
                                        <?php 
                                        error_reporting(0);
                                        if($_SESSION['name']==''){ ?>
                                            <li>
                                                <button data-toggle="modal" data-target="#modalLoginForm" style="border: none; font-size: 16px;">ĐĂNG NHẬP</button>
                                            </li>
                                            <li>
                                                <button data-toggle="modal" data-target="#modalRegisterForm" style="border: none; font-size: 16px;">ĐĂNG KÍ</button>
                                            </li>
                                        <?php }
                                        else{?>
                                            <li>
                                                <p style="text-align: center; background:dodgerblue;"><i class="fas fa-user" style="font-size:25px;color:tomato"></i>
                                                    <?php echo $_SESSION['name'];?>
                                                </p>
                                            </li>
                                            <li>
                                                <form action="" method="POST">
                                                    <button style="border: none; font-size: 16px;text-align: center;" name="logout">ĐĂNG XUẤT</button>
                                                </form>
                                            </li>
                                        <?php }?>
                                    </ul>
                                </li>
                                &emsp;
                                <li>
                                    <img src="Souvernir/giohang.png">
                                    <ul class="sub-menu" style="width: 200px;">
                                        <li>
                                            <form action="cart.php" method="" style="text-align: right;">
                                                <button style="border: none; "><i class="fas fa-shopping-cart" style="color:firebrick;"></i>GIỎ HÀNG</button>
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <form id="searchbox"  action="search.php" method="get">
                                        <input name="search" type="text" size="15" placeholder="Enter keywords..." />
                                        <input id="button-submit" name="ok" type="submit"/>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>


            <!-- ================================HIEN THI DANH SACH SAN PHAM TRONG GIO HANG=========================== -->
            <div class="detail_container">
                <div class="flex1">
                <img src= "<?php echo $Products[$vitri]->getImagePath();?>"style="margin-top: 20px;
                margin-left: 10px ;width: 550px; height: 450px">
                </div>
                <div class="flex2">
                <h3 style="text-align: center; color:brown; margin-bottom: 50px">CHI TIẾT SẢN PHẨM</h3>
                <p style="font-weight: bold">Tên sản phẩm: <a style="text-decoration: none; color:darkblue; font-weight: normal"><?php echo $Products[$vitri]->name;?></a></p>
                <p style="font-weight: bold">Giá: <a style="text-decoration: none; color:darkblue; font-weight: normal"><?php echo $Products[$vitri]->getPrice()."VND";?></a></p>
                <p style="font-weight: bold">Loại sản phẩm: <a style="text-decoration: none; color:darkblue; font-weight: normal"><?php echo $Products[$vitri]->getType();?></a></p>
                <p style="font-weight: bold">Mô tả chi tiết: <a style="text-decoration: none; color:darkblue; font-weight: normal"><?php echo $Products[$vitri]->detail;?></a></p>
                <form action="" method="post">
                    <button name="insert_cart" class="detail_add" value="<?php echo $Products[$vitri]->id;?>"> THÊM VÀO GIỎ HÀNG <i class="fa fa-shopping-cart" style="font-size:20px;color:red"></i></button>
                </form>    
            </div>
            </div>
        <!--============================================DOI TAC VA LIEN KET===============================================-->
        <div class="top_body_content">
                    <hr width="30%" height="10px" align="center; color:black;">
                    <h2>ĐỐI TÁC & <a href=# style="color: red;">LIÊN KẾT</a></h2>
                    <hr width="30%" height="10px" align="center; color:black;">
                </div>
                <div class="dt-grid-container">
                    <div class="thumbnail">
                        <img src="Souvernir/Doitac/1.jpg">
                    </div>
                    <div class="thumbnail">
                        <img src="Souvernir/Doitac/2.jpg">
                    </div>
                    <div class="thumbnail">
                        <img src="Souvernir/Doitac/3.jpg">
                    </div>
                    <div class="thumbnail">
                        <img src="Souvernir/Doitac/4.jpg">
                    </div>
                    <div class="thumbnail">
                        <img src="Souvernir/Doitac/5.jpg">
                    </div>
                    <div class="thumbnail">
                        <img src="Souvernir/Doitac/6.jpg">
                    </div>
                </div>    
        <!-- =================================================Footer================================================== -->
        <hr style="color: white; margin-top: 0px; margin-bottom: 0px;">
        <footer class="page-footer font-small special-color-dark pt-4" style="background-color:cornsilk;">
            <div class="container">
                <ul class="list-unstyled list-inline text-center">
                    <li class="list-inline-item">
                        <a class="btn-floating btn-fb mx-1" style="color:royalblue;">
                            <i class="fab fa-facebook-f"> </i>
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a class="btn-floating btn-tw mx-1" style="color:skyblue;">
                            <i class="fab fa-twitter"> </i>
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a class="btn-floating btn-gplus mx-1">
                            <i class="fab fa-google-plus-g" style="color:tomato;"> </i>
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a class="btn-floating btn-li mx-1">
                            <i class="fab fa-linkedin-in" style="color:navy;"> </i>
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a class="btn-floating btn-dribbble mx-1">
                            <i class="fab fa-dribbble" style="color:mediumvioletred;"> </i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="footer-copyright text-center py-3">© 2019 Dinh Hoa:
                <a href="index.php"> SouvernirShop.com</a>
            </div>
        </footer>
        <div class="header-container">
        </div>
    </div>
        <!--===================================================FORM DANG NHAP================================================-->
        <div class="modal fade" id="modalLoginForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="" method="post">
                        <div class="modal-header text-center">
                            <h4 class="modal-title w-100 font-weight-bold" style="color:firebrick">ĐĂNG NHẬP</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>      
                        <div class="modal-body mx-3">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1" style="width: 100px;"><i class="fa fa-user"></i> Account</span>
                                </div>
                                <input type="text" class="form-control" aria-label="account" aria-describedby="basic-addon1" name="accountLogin">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1" style="width: 100px;"><i class="fas fa-lock prefix grey-text"></i> Password</span>
                                </div>
                                <input type="password" class="form-control" aria-label="account" aria-describedby="basic-addon1" name="passLogin">
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-center">
                            <button class="btn btn-default" style="border-style:solid; background:firebrick;color:floralwhite; font-size:12px" name="login">OK</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--=============================================================FORM DANG KI=========================================-->
        <div class="modal fade" id="modalRegisterForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="" method="post">
                        <div class="modal-header text-center">
                            <h4 class="modal-title w-100 font-weight-bold" style="color:firebrick">ĐĂNG KÍ</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body mx-3">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1" style="width: 100px;"><i class="fa fa-user"></i>Name</span>
                                </div>
                                <input type="text" class="form-control" aria-label="account" aria-describedby="basic-addon1" name="nameRegister">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1" style="width: 100px;"><i class="fa fa-user"></i>Account</span>
                                </div>
                                <input type="text" class="form-control" aria-label="account" aria-describedby="basic-addon1" name="accountRegister">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1" style="width: 100px;"><i class="fas fa-lock prefix grey-text"></i> Password</span>
                                </div>
                                <input type="password" class="form-control" aria-label="account" aria-describedby="basic-addon1" name="passRegister">
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-center">
                            <button class="btn btn-default" style="border-style:solid; background:firebrick;color:floralwhite; font-size:12px" name="register">OK</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>                                                                                                                                                         