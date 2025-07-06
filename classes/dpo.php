<?php

class dpoPay
{
    private static $endpoint_url = "https://secure.3gdirectpay.com/API/v6/";

    // Configuration: Set to true for testing, false for production
    private static $isTestMode = false;

    // Testing credentials
    private static $testCompanyToken = "";
    private static $testServiceType = "";

    // Production credentials
    private static $prodCompanyToken = "";
    private static $prodServiceType = "";

    // Get current credentials based on mode
    private static function getCompanyToken() {
        return self::$isTestMode ? self::$testCompanyToken : self::$prodCompanyToken;
    }

    private static function getServiceType() {
        return self::$isTestMode ? self::$testServiceType : self::$prodServiceType;
    }

    public static $ref;

    // Method to set test mode
    public static function setTestMode($testMode = true) {
        self::$isTestMode = $testMode;
    }
    
    // Method to check if in test mode
    public static function isTestMode() {
        return self::$isTestMode;
    }

    function __construct($ref)
    {
        dpoPay::$ref = $ref;
    }
    //Create a DPO token
    public  static function CreateChargeToken($RedirectURL, $BackURL, $ServiceDescription, $PaymentAmount, $PaymentCurrency)



    {
        $ServiceDate = date('Y-m-d H:i:s');
        $endpoint = dpoPay::$endpoint_url;
        $xmlData = "<?xml version=\"1.0\" encoding=\"utf-8\"?><API3G><CompanyToken>" . self::getCompanyToken() . "</CompanyToken><Request>createToken</Request><Transaction><PaymentAmount>" . $PaymentAmount . "</PaymentAmount><PaymentCurrency>" . $PaymentCurrency . "</PaymentCurrency><CompanyRef>" . dpoPay::$ref . "</CompanyRef><RedirectURL>" . $RedirectURL . "</RedirectURL><BackURL>" . $BackURL . "</BackURL><CompanyRefUnique>0</CompanyRefUnique><PTL>5</PTL></Transaction><Services><Service><ServiceType>" . self::getServiceType() . "</ServiceType><ServiceDescription>" . $ServiceDescription . "</ServiceDescription><ServiceDate>" . $ServiceDate . "</ServiceDate></Service></Services></API3G>";

        $ch = curl_init();

        if (!$ch) {
            die("Couldn't initialize a cURL handle");
        }
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);

        $result = curl_exec($ch);
        curl_close($ch);

        // Parse the XML response using SimpleXML 
        $response = simplexml_load_string($result);
        return ["message" => $result, "response" => $response];
    }

    //Charge with credit card(chargeTokenCreditCard)

    public  static function chargeTokenCreditCard($CreditCardNumber, $CreditCardExpiry, $CreditCardCVV, $CardHolderName, $amount, $PaymentCurrency)

    {
        
        //Generate a transaction token
        $transToken = self::CreateChargeToken("https://lora.co.zm/checkout/success", "https://lora.co.zm/checkout/error", "Pay product", $amount, $PaymentCurrency);
        $resp = 'Declined';
   
        if ($transToken['response']->Result == 000) {
            $ServiceDate = date('Y-m-d H:i:s');
            $endpoint = dpoPay::$endpoint_url;
       
            // For basic card payments without 3D Secure, remove the ThreeD section
            $xmlData = '<?xml version="1.0" encoding="utf-8"?> 
            <API3G> 
            <CompanyToken>' . self::getCompanyToken() . '</CompanyToken> 
            <Request>chargeTokenCreditCard</Request> 
            <TransactionToken>' . $transToken['response']->TransToken . '</TransactionToken> 
            <CreditCardNumber>' . $CreditCardNumber . '</CreditCardNumber> 
            <CreditCardExpiry>' . $CreditCardExpiry . '</CreditCardExpiry> 
            <CreditCardCVV>' . $CreditCardCVV . '</CreditCardCVV> 
            <CardHolderName>' . $CardHolderName . '</CardHolderName> 
            </API3G>';
            
            $ch = curl_init();

            if (!$ch) {
                die("Couldn't initialize a cURL handle");
            }
            curl_setopt($ch, CURLOPT_URL, $endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);

            $result_ = curl_exec($ch);
            curl_close($ch);

            // Parse the XML response using SimpleXML         
            $result = simplexml_load_string($result_);
            $isSuccess = false;
            
            // Check if the curl_exec operation was successful
            if ($result === false) {
                die("cURL execution failed: " . curl_error($ch));
            }

            // Check the actual result code from the API response
            if (isset($result->Result) && $result->Result == '000') {
                $isSuccess = true;
            } else {
                $isSuccess = false;
            }
            
            // Add more detailed error information
            $errorMessage = '';
            if (isset($result->Result)) {
                $errorCode = (string)$result->Result;
                switch($errorCode) {
                    case '000':
                        $errorMessage = 'Success';
                        break;
                    case '999':
                        $errorMessage = 'Transaction Declined';
                        break;
                    case '901':
                        $errorMessage = 'Invalid XML';
                        break;
                    case '902':
                        $errorMessage = 'Invalid Company Token';
                        break;
                    case '903':
                        $errorMessage = 'Invalid Transaction Token';
                        break;
                    default:
                        $errorMessage = 'Error Code: ' . $errorCode;
                        break;
                }
            }
            
            $token = $transToken['response']->TransToken;
            $dataArray = array(
                "result" => $result_, 
                "token" => $token, 
                'isSuccess' => $isSuccess,
                'errorMessage' => $errorMessage,
                'errorCode' => isset($result->Result) ? (string)$result->Result : 'Unknown'
            );
            $resp = $dataArray;           
          
        }
          return($resp);
    }


    //Charge mobile money

    public  static function chargeTokenMobileMoney($mno, $phone, $amount, $country, $PaymentCurrency)
    {

        //Generate a transaction token
        $transToken = self::CreateChargeToken("https://webhook.site/54e2e771-bbc5-4818-bc1a-b0920dd1d797", "https://webhook.site/54e2e771-bbc5-4818-bc1a-b0920dd1d797", "Pay product", $amount, $PaymentCurrency);
        if ($transToken['response']->Result == 000) {
            $ServiceDate = date('Y-m-d H:i:s');
            $endpoint = dpoPay::$endpoint_url;
            $xmlData = '<?xml version="1.0" encoding="UTF-8"?> <API3G> <CompanyToken>' . self::getCompanyToken() . '</CompanyToken> <Request>ChargeTokenMobile</Request> <TransactionToken>' . $transToken['response']->TransToken . '</TransactionToken> <PhoneNumber>' . $phone . '</PhoneNumber> <MNO>' . $mno . '</MNO> <MNOcountry>' . $country . '</MNOcountry> </API3G>';

            $ch = curl_init();

            if (!$ch) {
                die("Couldn't initialize a cURL handle");
            }
            curl_setopt($ch, CURLOPT_URL, $endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);

            $result = curl_exec($ch);

            //echo $result.'<br>';
             // Check if the curl_exec operation was successful
            if ($result === false) {
                die("cURL execution failed: " . curl_error($ch));
            }
            $isSUccess = false;
            if (strpos($result, '130') !== false) {

                $isSUccess = true;
            } else {
                $isSUccess = false;
            }

            return ["result" => $result, "token" => $transToken['response']->TransToken, 'isSuccess' => $isSUccess];
        } else {
            echo $transToken["message"];
       
        }
    }


    //Verify transaction
    //Charge mobile money

    public  static function verifyTrans($token) {
        $ServiceDate = date('Y-m-d H:i:s');
        $endpoint = self::$endpoint_url;
        $xmlData = '<?xml version="1.0" encoding="UTF-8"?> <API3G> <CompanyToken>' . self::getCompanyToken() . '</CompanyToken> <Request>verifyToken</Request> <TransactionToken>' . $token . '</TransactionToken>  </API3G>';

        $ch = curl_init();

        if (!$ch) {
            die("Couldn't initialize a cURL handle");
        }
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);

        $result = curl_exec($ch);
        //echo $result;
        // Check if the curl_exec operation was successful
        if ($result === false) {
            die("cURL execution failed: " . curl_error($ch));
        }
         // Parse the XML response using SimpleXML
         $response = simplexml_load_string($result);

        $tran_status = "pending";
        
        if ($response->Result == 000) {

            $tran_status = "success";
        } elseif ($response->Result == 904 || $response->Result == 903) {
            $tran_status = "rejected";
        } else {
            $tran_status = "pending";
        }


        return ["tran_status" => $tran_status];
    }
}
