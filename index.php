<?php
	/* Database connection settings */
	include_once 'db.php';
	$data1 = '';
	$data2 = '';
    $data3 = '';
	$buildingName = '';
    $selectedDay = isset($_GET['DATE']) ? $_GET['DATE'] : '';
	$query = "SELECT `id`, `kelem_t`, `suhu_u`, `durasi_penyiraman`, `reading_time` FROM `insert_sensor`";
	if (!empty($selectedDay)) {
		$query .= "WHERE DATE(reading_time) = '$selectedDay'" ;
	}
    //$result = mysqli_query($conn, "SELECT * FROM id21964031_maritumbuhbersamaaa.id");
	$runQuery = mysqli_query($conn, $query);
    if (!$runQuery) {
        die("Query failed: " . mysqli_error($conn));
    }
	while ($row = mysqli_fetch_array($runQuery)) {
	  $data1 = $data1 . '"'. $row['kelem_t'] .'",';
      $data2 = $data2 . '"'. $row['suhu_u'] .'",';
      $data3 = $data3 . '"'. $row['durasi_penyiraman'].'",';
    $buildingName = $buildingName . '"'. ucwords($row['reading_time']) .'",';
	}

	$data1 = trim($data1,",");
	$data2 = trim($data2,",");
    $data3 = trim($data3,",");
    $data4 = trim($data4,",");
	$buildingName = trim($buildingName,",");

    $latestDataQuery = "SELECT `kelem_t`, `suhu_u`, `durasi_penyiraman`, `reading_time` FROM `insert_sensor` ORDER BY `id` DESC LIMIT 1";
	$latestDataResult = mysqli_query($conn, $latestDataQuery);

	while ($row = mysqli_fetch_array($latestDataResult)) {
		$latestSuhu = $row['suhu_u'];
		$latestKelem = $row['kelem_t'];
        $latestDurasi = $row['durasi_penyiraman'];
		$latestReadingTime = $row['reading_time'];
	}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>

    <title>BioGrow</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-leaf"></i>
                </div>
                <div class="sidebar-brand-text mx-3">BioGrow</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="index.html">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item">
                <a class="nav-link" href="tables.html">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Fuzzy Logic</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <!-- <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow"> -->

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>
                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content --> 
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4 m-4">
                        <!-- <h1 class="h3 mb-0 text-gray-800">Dashboard</h1> -->
                        <img src="img/Logo_UnivLampung.png" alt="deskripsi_gambar" style="display:flex; margin-left: 45%; margin-right: 50%; height: 100px; width: 100px;">
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Suhu -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-3">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-m font-weight-bold text-success text-uppercase mb-2">
                                                Suhu</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $latestSuhu;?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-temperature-high fa-2x" style="color:black";></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Kelembaban Tanah -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-3">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-m font-weight-bold text-success text-uppercase mb-2">Kelembaban Tanah 
                                            </div>
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-auto">
                                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $latestKelem; ?></div>
                                                </div>
                                                <div class="col">
                                                    <div class="progress progress-sm mr-2">
                                                        <div class="progress-bar bg-gradient-success" role="progressbar"
                                                            style="width: <?php echo $latestKelem; ?>%" aria-valuenow="50" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto"> 
                                            <i class="fas fa-water fa-2x text-bla-300" style="color:black";></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Durasi Penyiraman -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-3">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-m font-weight-bold text-success text-uppercase mb-2">
                                                Durasi Penyiraman</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $latestDurasi; ?> detik</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="far fa-clock fa-2x text-black-300" style="color:black";></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Content Row -->

                    <div class="row">

                        <!-- Area Chart -->
                        <div class="col-xl-12 col-lg-8">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <!-- <<div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Kelembaban Tanah & Suhu</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Dropdown Header:</div>
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                        </div> 
                                    </div>
                                </div> -->
                            <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="myChart"></canvas>
                                    </div>
                                </div>    
                            </div>
                            <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
                            <!-- <script src="chart-are-demo.js"></script> -->
                            
                            <div class="container" style="margin-top: 2rem; background: White;">
                                <h1 style="color: Black;  text-align: center; font-size: 24px;">Kelembaban Tanah & Suhu</h1>       
                                <canvas id="chart" style="width: 100%; height: 65vh; background: transparent; border: 1px solid black; margin-top: 10px;"></canvas>
                                <script>
                               		var ctx = document.getElementById("chart").getContext('2d');
                                    var myChart = new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                    labels: [<?php echo $buildingName; ?>],
                                        datasets: 
                                        [{
                                            label: 'kelembaban tanah',
                                            data: [<?php echo $data1; ?>],
                                            bbackgroundColor: 'transparent',
                                            borderColor:'rgba(255,99,132)',
                                            borderWidth: 1
                                        },

                                        {
                                            label: 'suhu',
                                            data: [<?php echo $data2; ?>],
                                            backgroundColor: 'transparent',
                                            borderColor:'rgba(0,255,255)',
                                            borderWidth: 1	
                                        }]
                                    },
                                
                                    options: {
                                        scales: {scales:{yAxes: [{beginAtZero: false}], xAxes: [{autoskip: true, maxTicketsLimit: 20}]}},
                                        tooltips:{mode: 'index'},
                                        legend:{display: true, position: 'top', labels: {fontColor: 'black', fontSize: 16}}
                                        }
                                     });
                                </script>
                            </div>
                    </div>                 
                        <div class="col-lg-6 mb-4"></div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span style="color:black; font-size: 16px;">Copyright &copy; BioGrow 2024</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

   

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

</body>

</html>