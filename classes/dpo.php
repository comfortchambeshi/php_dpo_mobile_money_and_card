<?php

class dpoPay
{
    private static $endpoint_url = "https://secure.3gdirectpay.com/API/v6/";

    private static $CompanyToken = "your_company_token";

    private static $serviceType = "service_type_code";

    public static $ref;

    function __construct($ref) {
        dpoPay::$ref = $ref;
      }

    

    //Create a DPO token
    public  static function CreateChargeToken($RedirectURL, $BackURL, $ServiceDescription, $PaymentAmount, $PaymentCurrency)



    {
        $ServiceDate = date('Y-m-d H:i:s');
        $endpoint = dpoPay::$endpoint_url;
        $xmlData = "<?xml version=\"1.0\" encoding=\"utf-8\"?><API3G><CompanyToken>".dpoPay::$CompanyToken."</CompanyToken><Request>createToken</Request><Transaction><PaymentAmount>".$PaymentAmount."</PaymentAmount><PaymentCurrency>".$PaymentCurrency."</PaymentCurrency><CompanyRef>".dpoPay::$ref."</CompanyRef><RedirectURL>".$RedirectURL."</RedirectURL><BackURL>".$BackURL."</BackURL><CompanyRefUnique>0</CompanyRefUnique><PTL>5</PTL></Transaction><Services><Service><ServiceType>".dpoPay::$serviceType."</ServiceType><ServiceDescription>".$ServiceDescription."</ServiceDescription><ServiceDate>".$ServiceDate."</ServiceDate></Service></Services></API3G>";

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

       

        return ["message"=>$result, "response"=>$response];
    }

    //Charge with credit card(chargeTokenCreditCard)

    public  static function chargeTokenCreditCard($CreditCardNumber, $CreditCardExpiry, $CreditCardCVV, $CardHolderName, $amount, $PaymentCurrency)



    {

        //Generate a transaction token
        $transToken = self::CreateChargeToken("https://webhook.site/c2b49c9c-c70f-4408-a5ee-2b95abcf97dc", "https://webhook.site/c2b49c9c-c70f-4408-a5ee-2b95abcf97dc", "Pay product", $amount, $PaymentCurrency);
        if ($transToken['response']->Result == 000) {
            $ServiceDate = date('Y-m-d H:i:s');
            $endpoint = dpoPay::$endpoint_url;
            $xmlData = '<?xml version="1.0" encoding="utf-8"?> <API3G> <CompanyToken>'.dpoPay::$CompanyToken.'</CompanyToken> <Request>chargeTokenCreditCard</Request> <TransactionToken>'.$transToken->TransToken.'</TransactionToken> <CreditCardNumber>'.$CreditCardNumber.'</CreditCardNumber> <CreditCardExpiry>'.$CreditCardExpiry.'</CreditCardExpiry> <CreditCardCVV>'.$CreditCardCVV.'</CreditCardCVV> <CardHolderName>'.$CardHolderName.'</CardHolderName> <ChargeType></ChargeType> <ThreeD> <Enrolled>Y</Enrolled> <Paresstatus>Y</Paresstatus> <Eci>05</Eci> <Xid>DYYVcrwnujRMnHDy1wlP1Ggz8w0=</Xid> <Cavv>mHyn+7YFi1EUAREAAAAvNUe6Hv8=</Cavv> <Signature>_</Signature> <Veres>AUTHENTICATION_SUCCESSFUL</Veres> <Pares>eAHNV1mzokgW/isVPY9GFSCL0EEZkeyg7</Pares> </ThreeD> </API3G>';

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

          

           if ($response->Code != 000) 
           {
            echo '<h1>Error, there was a problem fetching from our provider. Kndly try again after some seconds!</h1>';
            //exit();
           }
           return $response;

            
         }else
         {
            echo "Error, can not create Transaction token. Result: <b>".$transToken->ResultExplanation."</b>";
            //exit();
         } 

        
    }


    //Charge mobile money

    public  static function chargeTokenMobileMoney($mno,$phone,$amount, $country, $PaymentCurrency)



    {

        //Generate a transaction token
        $transToken = self::CreateChargeToken("https://webhook.site/54e2e771-bbc5-4818-bc1a-b0920dd1d797", "https://webhook.site/54e2e771-bbc5-4818-bc1a-b0920dd1d797", "Pay product", $amount, $PaymentCurrency);
        if ($transToken['response']->Result == 000) {
            $ServiceDate = date('Y-m-d H:i:s');
            $endpoint = dpoPay::$endpoint_url;
            $xmlData = '<?xml version="1.0" encoding="UTF-8"?> <API3G> <CompanyToken>'.dpoPay::$CompanyToken.'</CompanyToken> <Request>ChargeTokenMobile</Request> <TransactionToken>'.$transToken['response']->TransToken.'</TransactionToken> <PhoneNumber>'.$phone.'</PhoneNumber> <MNO>'.$mno.'</MNO> <MNOcountry>'.$country.'</MNOcountry> </API3G>';

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


        return ["result"=>$result,"token"=>$transToken['response']->TransToken, 'isSuccess'=>$isSUccess];


          
            
            
            
            
            
            


            
            



            

           


            
        }else
        {
            echo $transToken["message"];
            exit();
        }

        
    }


    //Verify transaction
        //Charge mobile money

public  static function verifyTrans($token)



        {
    
           
                $ServiceDate = date('Y-m-d H:i:s');
                $endpoint = self::$endpoint_url;
                $xmlData = '<?xml version="1.0" encoding="UTF-8"?> <API3G> <CompanyToken>'.dpoPay::$CompanyToken.'</CompanyToken> <Request>verifyToken</Request> <TransactionToken>'.$token.'</TransactionToken>  </API3G>';
    
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

                

                if ($response->Result == 000) 
                {
                    
                    $tran_status = "success";
                }
               

                elseif($response->Result == 904 || $response->Result == 903 )
                {
                    $tran_status = "rejected";

                }

                else
                {
                    $tran_status = "pending";

                }


                return ["tran_status"=>$tran_status];

           
           

            
         }
     
        }
      
       






