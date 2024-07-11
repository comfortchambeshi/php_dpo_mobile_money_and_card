<?php 

include '../classes/dpo.php';

$order_id = "12345678";

$dpo = new dpoPay($order_id);
if (isset($_POST['pay_btn'])) {


  $payment_option =  $_POST['payment_option'];
  $amount = $_POST['amount'];

  if ($payment_option == "mobile_money") {
    
    $phone_number = $_POST['phone_num'];

    $mno = "AirtelZM";

    if ($_POST['network'] == 'mtn') {
      $mno = "MTNZM";
    }




$pay = $dpo->chargeTokenMobileMoney($mno,substr($phone_number, 1), $amount, 'zambia', 'ZMW');

   if ($pay['isSuccess']) {
    //echo '<p>Complete your transaction by entering a PIN</p>';
   }else
   {
    echo '<p>Error, transaction can not be made. Please refresh the page and try again!</p>';
    exit();
   }

  }




 
  

}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway:300,400,500">
     <title>Payment form</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway+Dots">
    <link rel="stylesheet" href="../assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="../assets/fonts/ionicons.min.css">
    <link rel="stylesheet" href="../assets/css/Footer-Clean.css">
    <link rel="stylesheet" href="../assets/css/Highlight-Blue.css">
    <link rel="stylesheet" href="../assets/css/Login-Form-Clean.css">
    <link rel="stylesheet" href="../assets/css/Navigation-Clean.css">
    <link rel="stylesheet" href="../assets/css/News-article-for-homepage-by-Ikbendiederiknl.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body>

<body id="page-top">

<script src="https://code.jquery.com/jquery-2.2.4.js" integrity="sha256-iT6Q9iMJYuQiMWNd9lDyBUStIq/8PuOW33aOqmvFpqI=" crossorigin="anonymous"></script>
    <div class="news-block" style="padding-top: 0px;">
        <section class="login-clean">
            <div class="intro">
                <h4 class="text-center text-info" ><strong>Do not close this page until it automatically redirects you!</strong></h4>
                
            </div>
            <form id="content" action="" method="post" style="width: 500px;max-width: 100%;" >

             <h3 id="status"></h3>
            </form>
        </section>
    </div>


<?php 


?>
											

   



<script>
  // function to check status using AJAX
  function checkStatus() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', '../verify.php?token=<?php echo $pay['token'];?>');
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onload = function() {
      if (xhr.status === 200) {
        try {
          const response = JSON.parse(xhr.responseText);
          console.log(response);
          handleStatus(response.status);
        } catch (error) {
          console.error(error);
          setTimeout(checkStatus, 5000); // wait 5 seconds and check again
        }
      } else {
        // handle error
        alert('Error checking status.');
      }
    };
    xhr.send();
  }

  // handle the status received from verify.php
  function handleStatus(status) {
    if (status === 'success') {
      // if status is approved, display message and redirect
      document.getElementById('status').innerHTML = '<h4 class="text-success"><i class="bi bi-calendar-check-fill"></i> Transaction processed successfully!</h4>';
      setTimeout(function() {
        window.location.href = '../success.php?token=<?php echo $pay['token'];?> '; // replace with the URL you want to redirect to
      }, 5000); // wait 5 seconds and redirect
    } else if (status === 'rejected') {
      // if status is rejected, display message and redirect
      document.getElementById('status').innerHTML = '<h4 class="text-danger">Transaction failed!</h4>';
      setTimeout(function() {
        window.location.href = '../success.php?token=?token=<?php echo $pay['token'];?> '; // replace with the URL you want to redirect to
      }, 5000); // wait 5 seconds and redirect
    } else {
      // if status is not approved or rejected, display pending message
      document.getElementById('status').innerHTML = '<div class="text-center"><h2 style="font-size:100px;" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></h2><br>A push request has been forwarded to your mobile number in order to initiate the payment through the payment service provider of your choice. It will prompt for your mobile PIN to complete the payment process.</div>';
      setTimeout(checkStatus, 5000); // wait 5 seconds and check again
    }
  }

  // call checkStatus function when document is ready
  document.addEventListener('DOMContentLoaded', function() {
    checkStatus();
  });
</script>

  </body>
</html>







