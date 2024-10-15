<?php
include("session.php");

$user_query = mysqli_query($con, "SELECT * FROM users WHERE user_id = '$userid'");
$user_data = mysqli_fetch_assoc($user_query);
$profile_img = $user_data['img'] ? $user_data['img'] : 'default_profile.png';
$fname = $user_data['firstname']; 
$lname = $user_data['lastname'];
if (isset($_POST['save'])) {
	
   $error_message = ""; 
    $success_message = "";

    $sql = "UPDATE users SET firstname = '$fname', lastname='$lname' WHERE user_id='$userid'";
    if (mysqli_query($con, $sql)) {
		$fname = $_POST['first_name'];
		$lname = $_POST['last_name'];
        $success_message = "Records were updated successfully.";
    } else {
        $error_message = "ERROR: Could not execute $sql. " . mysqli_error($con);
    }
    
    if (!empty($_POST['current_password'])) {
         $current_password = mysqli_real_escape_string($con, $_POST['current_password']);
         $current_password_hash = md5($current_password);
         if ($current_password_hash != $user_data['password']) {
             $error_message = "Invalid Current Password";
         } else if (!empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
              $new_password = mysqli_real_escape_string($con, $_POST['new_password']);
              $confirm_password = mysqli_real_escape_string($con, $_POST['confirm_password']);
              if ($new_password == $confirm_password) {
                  $new_password_hash = md5($new_password);
                  $sql = "UPDATE users SET password = '$new_password_hash' WHERE user_id='$userid'";
                  if (mysqli_query($con, $sql)) {
                      $success_message = "Password updated successfully.";
                  } else {
                      $error_message = "ERROR: Could not update password. " . mysqli_error($con);
                  }
              } else {
                  $error_message = "New password and confirmation do not match.";
              }
         }
    }

}

if (isset($_POST['but_upload'])) {
    $name = $_FILES['file']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["file"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $extensions_arr = array("jpg", "jpeg", "png", "gif");

    if (in_array($imageFileType, $extensions_arr)) {
        $query = "UPDATE users SET img = '$name' WHERE user_id='$userid'";
        if (mysqli_query($con, $query)) {
            move_uploaded_file($_FILES['file']['tmp_name'], $target_dir . $name);
            $success_message = "Profile image updated successfully.";
        } else {
            $error_message = "ERROR: Could not execute $query. " . mysqli_error($con);
        }

        header("Refresh: 0"); // Refresh the page to show the new profile image
    } else {
        $error_message = "Invalid file format. Please upload JPG, JPEG, PNG, or GIF images.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Expense Manager - Dashboard</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet">

    <!-- jQuery (Make sure this is included) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Feather JS for Icons -->
    <script src="js/feather.min.js"></script>

    <style>
        .try {
            font-size: 28px; /* Adjust the font size as needed */
            color: #333;    /* Adjust the color as needed */
            padding: 15px 65px 5px 0px;   /* Adjust the padding as needed */
        }
    </style>
</head>

<body>

    <div class="d-flex" id="wrapper">

        <!-- Sidebar -->
        <div class="border-right" id="sidebar-wrapper">
            <div class="user">
                <a href="index.php">
                    <img class="img img-fluid rounded-circle" src="uploads/<?php echo $profile_img; ?>" width="120">
                </a>
				<h5><?php echo $fname . ' ' . $lname; ?></h5>
                <p><?php echo $useremail ?></p>
            </div>
            <div class="sidebar-heading">Management</div>
            <div class="list-group list-group-flush">
                <a href="index.php" class="list-group-item list-group-item-action "><span data-feather="home"></span> Dashboard</a>
                <a href="add_expense.php" class="list-group-item list-group-item-action "><span data-feather="plus-square"></span> Add Expenses</a>
                <a href="manage_expense.php" class="list-group-item list-group-item-action "><span data-feather="dollar-sign"></span> Manage Expenses</a>
                <a href="expensereport.php" class="list-group-item list-group-item-action"><span data-feather="file-text"></span> Expense Report</a>
            </div>
            <div class="sidebar-heading">Settings</div>
            <div class="list-group list-group-flush">
                <a href="profile.php" class="list-group-item list-group-item-action sidebar-active"><span data-feather="user"></span> Profile</a>
                <a href="logout.php" class="list-group-item list-group-item-action "><span data-feather="power"></span> Logout</a>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">

            <nav class="navbar navbar-expand-lg navbar-light  border-bottom">
                <button class="toggler" type="button" id="menu-toggle" aria-expanded="false">
                    <span data-feather="menu"></span>
                </button>
                <div class="col-md-12 text-center">
                    <h3 class="try">Update Profile</h3>
                </div>
            </nav>

            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-md-6">

                        <!-- Display Error Message -->
                        <?php if (!empty($error_message)): ?>
                            <div id="error-alert" class="alert alert-danger">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Display Success Message if no errors -->
                        <?php if (empty($error_message) && !empty($success_message)): ?>
                            <div id="success-alert" class="alert alert-success">
                                <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Profile Image Update Form -->
                        <form class="form" method="post" action="" enctype='multipart/form-data'>
                            <div class="text-center mt-3">
                                <img src="uploads/<?php echo $profile_img; ?>" class="text-center img img-fluid rounded-circle avatar" width="120" alt="Profile Picture">
                            </div>
                            <div class="input-group col-md mb-3 mt-3">
                                <input type="file" name="file" class="file-upload">
                                <button class="btn btn-block btn-md btn-primary" style="border-radius:0%;" name="but_upload" type="submit">Upload Image</button>
                            </div>
                        </form>

                        <!-- Profile Details and Password Reset Form -->
                        <form class="form" action="" method="post" id="registrationForm" autocomplete="off">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <div class="col-md">
                                            <label for="first_name">First name</label>
                                            <input type="text" class="form-control" name="first_name" id="first_name" placeholder="First Name" value="<?php echo $fname; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <div class="col-md">
                                            <label for="last_name">Last name</label>
                                            <input type="text" class="form-control" name="last_name" id="last_name" value="<?php echo $lname; ?>" placeholder="Last Name">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" name="email" id="email" value="<?php echo $useremail; ?>" disabled>
                                </div>
                            </div>

                            <!-- Password Reset Fields -->
                            <div class="form-group">
                                <div class="col-md">
                                    <label for="current_password">Current Password</label>
                                    <input type="password" class="form-control" name="current_password" id="current_password" placeholder="Current Password">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md">
                                    <label for="new_password">New Password</label>
                                    <input type="password" class="form-control" name="new_password" id="new_password" placeholder="New Password">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md">
                                    <label for="confirm_password">Confirm New Password</label>
                                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm New Password">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md">
                                    <br>
                                    <button class="btn btn-block btn-md btn-success" style="border-radius:0%;" name="save" type="submit">Save Changes</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- Bootstrap core JavaScript -->
    <script src="js/jquery.slim.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/Chart.min.js"></script>
    <!-- Menu Toggle Script -->
    <script>
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });
    </script>
    <script>
        feather.replace()
    </script>

    <!-- Script to hide the alert after 5 seconds -->
    <script type="text/javascript">
        $(document).ready(function() {
			setTimeout(function() {
            document.querySelector('#error-alert').remove();
            }, 5000);
			setTimeout(function() {
			document.querySelector('#success-alert').remove();
            }, 5000);
          
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            var readURL = function(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('.avatar').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            $(".file-upload").on('change', function() {
                readURL(this);
            });
        });
    </script>

</body>

</html>
