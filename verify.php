<?php

if (isset($_GET['token'])) {
    include 'classes/dpo.php';
    $dpo = new dpoPay('ref');
    //$verify = $dpo->verifyTrans($_GET['token']);

    $verify = $dpo->verifyTrans($_GET['token']);

    

   

    
    
    if ($verify['tran_status'] == "pending") {
    $status = "pending";
    } 

    if ($verify['tran_status'] == "success") {
        $status = "success";
    } 

    if ($verify['tran_status'] == "rejected") {
        $status = "rejected";
        } 
    echo json_encode(['status' => $status]);
    }

?>
