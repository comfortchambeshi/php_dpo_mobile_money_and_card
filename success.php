<?php

if (isset($_GET['token'])) {
    include 'classes/dpo.php';
    $dpo = new dpoPay('ref');
    //$verify = $dpo->verifyTrans($_GET['token']);

    $verify = $dpo->verifyTrans($_GET['token']);

    

   

     
    $message = "Your transaction was not succesful";
    
    if ($verify['tran_status'] == "pending") {
    $status = "pending";
    } 

    if ($verify['tran_status'] == "success") {
        $status = "success";

        $message = 'Transaction successful!';
    } 

    if ($verify['tran_status'] == "rejected") {
        $status = "rejected";
        } 
    
    }

    echo '<h1>'.$message.'</h1>';

?>
