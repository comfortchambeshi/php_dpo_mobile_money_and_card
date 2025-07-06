<?php 

include '../classes/dpo.php';

$order_id = "12345678";

$dpo = new dpoPay($order_id);
if (isset($_POST['pay_btn'])) {

  $payment_option =  $_POST['payment_option'];
  $amount = $_POST['amount'];

  // Validate amount
  if (empty($amount) || !is_numeric($amount) || $amount <= 0) {
    echo '<p>Error: Please enter a valid amount!</p>';
    exit();
  }

  // Validate payment option
  if (empty($payment_option)) {
    echo '<p>Error: Please select a payment method!</p>';
    exit();
  }

  if ($payment_option == "mobile_money") {
    
    $phone_number = $_POST['phone_num'];

    $mno = "AirtelZM";

    if ($_POST['network'] == 'mtn') {
      $mno = "MTNZM";
    }

    $pay = $dpo->chargeTokenMobileMoney($mno,substr($phone_number, 1), $amount, 'zambia', 'ZMW');

    if ($pay['isSuccess']) {
      //echo '<p>Complete your transaction by entering a PIN</p>';
    } else {
      echo '<p>Error, transaction can not be made. Please refresh the page and try again!</p>';
      exit();
    }

  } elseif ($payment_option == "card") {
    
    // Validate card fields
    if (empty($_POST['card_number']) || empty($_POST['card_holder_name']) || 
        empty($_POST['card_expiry_month']) || empty($_POST['card_expiry_year']) || 
        empty($_POST['card_cvv'])) {
      echo '<p>Error: Please fill in all card details!</p>';
      exit();
    }
    
    $card_number = str_replace(' ', '', $_POST['card_number']); // Remove spaces
    $card_holder_name = trim($_POST['card_holder_name']);
    $card_expiry_month = str_pad($_POST['card_expiry_month'], 2, '0', STR_PAD_LEFT);
    $card_expiry_year = $_POST['card_expiry_year'];
    $card_cvv = $_POST['card_cvv'];
    
    // Basic validation
    if (strlen($card_number) < 13 || strlen($card_number) > 19) {
      echo '<p>Error: Invalid card number length!</p>';
      exit();
    }
    
    if ($card_expiry_month < 1 || $card_expiry_month > 12) {
      echo '<p>Error: Invalid expiry month!</p>';
      exit();
    }
    
    if (strlen($card_cvv) < 3 || strlen($card_cvv) > 4) {
      echo '<p>Error: Invalid CVV!</p>';
      exit();
    }
    
    // Format expiry date as MMYY
    $card_expiry = $card_expiry_month . substr($card_expiry_year, -2);
    
    $pay = $dpo->chargeTokenCreditCard($card_number, $card_expiry, $card_cvv, $card_holder_name, $amount, 'ZMW');
    
    if ($pay['isSuccess']) {
      //echo '<p>Processing your card payment...</p>';
    } else {
      $errorMsg = isset($pay['errorMessage']) ? $pay['errorMessage'] : 'Unknown error';
      echo '<p>Error: Card payment failed - ' . htmlspecialchars($errorMsg) . '</p>';
      echo '<p>Please check your card details and try again.</p>';
      exit();
    }

  } else {
    echo '<p>Error: Please select a valid payment method!</p>';
    exit();
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
// Only show the status checking JavaScript if payment was initiated successfully
if (isset($pay) && $pay['isSuccess']) {
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
        window.location.href = '../success.php?token=<?php echo $pay['token'];?>'; // replace with the URL you want to redirect to
      }, 5000); // wait 5 seconds and redirect
    } else if (status === 'rejected') {
      // if status is rejected, display message and redirect
      document.getElementById('status').innerHTML = '<h4 class="text-danger">Transaction failed!</h4>';
      setTimeout(function() {
        window.location.href = '../success.php?token=<?php echo $pay['token'];?>'; // replace with the URL you want to redirect to
      }, 5000); // wait 5 seconds and redirect
    } else {
      // if status is not approved or rejected, display pending message
      <?php if ($_POST['payment_option'] == 'mobile_money') { ?>
      document.getElementById('status').innerHTML = '<div class="text-center"><h2 style="font-size:100px;" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></h2><br>A push request has been forwarded to your mobile number in order to initiate the payment through the payment service provider of your choice. It will prompt for your mobile PIN to complete the payment process.</div>';
      <?php } else { ?>
      document.getElementById('status').innerHTML = '<div class="text-center"><h2 style="font-size:100px;" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></h2><br>Processing your card payment. Please wait while we verify your transaction...</div>';
      <?php } ?>
      setTimeout(checkStatus, 5000); // wait 5 seconds and check again
    }
  }

  // call checkStatus function when document is ready
  document.addEventListener('DOMContentLoaded', function() {
    checkStatus();
  });
</script>

<?php 
} else {
  // If no payment was initiated or it failed, show an error message
  echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
      document.getElementById("status").innerHTML = "<h4 class=\"text-danger\">Payment initialization failed. Please go back and try again.</h4>";
      setTimeout(function() {
        window.history.back();
      }, 3000);
    });
  </script>';
}
?>

  </body>
</html>







