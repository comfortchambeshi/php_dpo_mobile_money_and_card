<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway:300,400,500">
     <title>Payment form</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway+Dots">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/fonts/ionicons.min.css">
    <link rel="stylesheet" href="assets/css/Footer-Clean.css">
    <link rel="stylesheet" href="assets/css/Highlight-Blue.css">
    <link rel="stylesheet" href="assets/css/Login-Form-Clean.css">
    <link rel="stylesheet" href="assets/css/Navigation-Clean.css">
    <link rel="stylesheet" href="assets/css/News-article-for-homepage-by-Ikbendiederiknl.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>

<body id="page-top">
    
    
    

<div class="container bg" style="margin-top: {% if request|is_pc %}27px;{% else %}80px;{% endif %} margin-bottom:50px;">
        <div class="card bg-white border-info border rounded shadow-lg">
            <div class="card-body">
                <h6 class="text-center text-black-50 card-subtitle mb-2"><img class="img-thumbnail" style="width:10%" src="{{item.thumbnail}}"></h6>
                <p class="text-center text-dark card-text">
                  </p><h4 class="text-center">Payment item name</h4>
                  <h5 class="text-center">
                  </h5><p></p>
                <div class="table-responsive">
                   
                  

                    </div>
                <hr style="margin-top: 0px;">
                <form action="inc/submit.inc.php" method="POST">

                <div class="mb-3">
                    <input class="form-control" type="number" placeholder="Amount" name="amount">
                </div>
                 
 
                <div class="mb-3">
                  <select required name="payment_option" id="test" class="form-select" aria-label="Default select example">
                    <option required="" selected="" disabled="" value="">Payment method</option>
                    <option onclick"showdiv()"="" value="mobile_money">Mobile money</option>
                    <option onclick"showdiv()"="" value="card">Card</option>
                 
                  </select>
                 </div>

                 <div class="mb-3">
                  <select name="currency" id="test" required="" class="form-select" aria-label="Default select example">
                    <option selected="" value="ZMW">ZMW</option>
                 
                  </select>
                 </div>

                <!-- Mobile money form-->
                <div class="card bg-light" id="hidden_div1" style="display: block; padding: 5px;">
                    <div class="card-header"><h5>Mobile money</h5></div>
                     <div class="row">
                  
                     <div class="col-md-12">
                        <label for="cc-name" class="form-label">Network</label>
                        <select name="network" class="form-select">
                            <option value="mtn">MTN</option>
                            <option value="airtel">Airtel</option>
                        </select>
                        <small class="text-muted">Mobile money network</small>
                        <div class="invalid-feedback">
                          Name on card is required
                        </div>
                       <label for="cc-name" class="form-label">10 digit number</label>
                       <input name="phone_num" maxlength="10" type="number" class="form-control" id="cc-name" placeholder="">
                       <small class="text-muted">Your mobile money phone number</small>
                       <div class="invalid-feedback">
                         Name on card is required
                       </div>
                    </div>
                 
         
                    
                   </div>
                 </div>
                <!-- Card form-->

<div class="card bg-light" id="hidden_div2" style="display: none;padding:5px;">
<div class="card-header"><h5>Card details</h5></div>
 <div class="row">

 <div class="col-md-6">
     
   <label for="cc-name" class="form-label">Name on card</label>
   <input type="text" class="form-control" id="cc-name" placeholder="">
   <small class="text-muted">Full name as displayed on card</small>
   <div class="invalid-feedback">
     Name on card is required
   </div>
</div>


 <div class="col-6">
   <label for="cc-number" class="form-label">Credit card number</label>
   <input name="card_number" type="number" class="form-control" id="cc-number" placeholder="">
   <div class="invalid-feedback">
     Credit card number is required
   </div>
 </div>

 <div class="col-md-6">
   <label for="cc-expiration" class="form-label">Expiration</label>
   <div class="row">
    <div class="col-md-6">
      <input name="card_expiry_month" type="number" class="form-control" id="cc-expiration" placeholder="Month">
    </div>
    <div class="col-md-6">
      <input name="card_expiry_year" type="number" class="form-control" id="cc-expiration" placeholder="Year">
    </div>

  </div>

   
   
   <div class="invalid-feedback">
     Expiration date required
   </div>
 </div>

 <div class="col-md-3">
   <label for="cc-cvv" class="form-label">CVV</label>
   <input name="card_cvv" type="number" class="form-control" id="cc-cvv" placeholder="">
   <div class="invalid-feedback">
     Security code required
   </div>
 </div>
</div>
</div>
</div>
                
              



              <p class="text-center">
                <button name="pay_btn" class="btn btn-success" type="submit">Pay Now</button>

                </form>
              
              </p>
  
            </div>            </div> 
        
    
											




											
											
												


											


 


								



<!--
  Js function for mobile money payment

-->
<script>
  var select = document.getElementById('test'),
  onChange = function(event) {
    var shown = this.options[this.selectedIndex].value == 'mobile_money';
    var shown2 = this.options[this.selectedIndex].value == 'card';
  
      document.getElementById('hidden_div1').style.display = shown ? 'block' : 'none';
    document.getElementById('hidden_div2').style.display = shown2 ? 'block' : 'none';
    
  };
  
  
  if (select.addEventListener) {
      select.addEventListener('change', onChange, false);
  } else {
    select.attachEvent('onchange', function() {
      onChange.apply(select, arguments);
    });
  }
</script></body>