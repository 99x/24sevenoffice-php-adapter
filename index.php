<?php
      include 'config.php';
      session_start();
   
      $options = array ('trace' => true );
      $params ["credential"]["Username"] = $username;
      $encodedPassword = md5(mb_convert_encoding($password, 'utf-16le', 'utf-8'));
      $params ["credential"]["Password"] = $encodedPassword;
      $params ["credential"]["ApplicationId"] = $applicationid;

      $params ["credential"]["IdentityId"] = "00000000-0000-0000-0000-000000000000";

      try {
          $authentication = new SoapClient ( "https://api.24sevenoffice.com/authenticate/v001/authenticate.asmx?wsdl", $options );
          // log into 24SevenOffice if we don't have any active session. No point doing this more than once.
          $login = true;
          if (!empty($_SESSION['ASP.NET_SessionId']))
          {
              $authentication->__setCookie("ASP.NET_SessionId", $_SESSION['ASP.NET_SessionId']);
              try
              {
                   $login = !($authentication->HasSession()->HasSessionResult);
              }
              catch ( SoapFault $fault ) 
              {
                  $login = true;
              }
          }
          if( $login )
          {
              $result = ($temp = $authentication->Login($params));
              // set the session id for next time we call this page
              $_SESSION['ASP.NET_SessionId'] = $result->LoginResult;
              // each seperate webservice need the cookie set
              $authentication->__setCookie("ASP.NET_SessionId", $_SESSION['ASP.NET_SessionId']);
              //print_r($authentication);

              // throw an error if the login is unsuccessful
              if($authentication->HasSession()->HasSessionResult == false)
                  throw new SoapFault("0", "Invalid credential information.");
          }
          // To get current identity:
          $identity = json_encode($authentication->GetIdentity());

          // To connect to a 24seven webservice:
           if($_GET['type']=='new'){
              $data = json_decode(file_get_contents('php://input'),true );
              $name = $data['Name'];
              $email = $data['Email'];
              $org_number = $data['Account_ID'];
              
              // Field Validations
              if(empty($name) || empty($email) || empty($org_number)){
                $res = array("status"=>0, "data"=> "Please send all the required fields", "identity"=>$identity);
                echo json_encode($res);
                die();
              }

              $response_method = addCompany($name, $email, $org_number);

              if($response_method->SaveCompaniesResult->Company->APIException){
                $res = array("status"=>0, "data"=> $response_method->SaveCompaniesResult->Company->APIException->Message, "identity"=>$identity);
              }else{
                $res = array("status"=>1, "data"=> $response_method->SaveCompaniesResult->Company, "identity"=>$identity);
              }
           }

          echo json_encode($res);

      } 
      catch ( SoapFault $fault ) 
      {
          echo 'Exception: ' . $fault->getMessage();
      }

      // Add a new Company to the CRM with the basic fields
      function addCompany($name, $email, $org_number){
          $options = array ('trace' => true );
          $CompanyService = new SoapClient ( "https://api.24sevenoffice.com/CRM/Company/V001/CompanyService.asmx?WSDL", $options );
          $CompanyService->__setCookie("ASP.NET_SessionId", $_SESSION['ASP.NET_SessionId']);
          
          $company["companies"][0]['Name'] = $name;
          $company["companies"][0]['Type'] = "Business";
          $company["companies"][0]['OrganizationNumber'] = $org_number;
          $company["companies"][0]['EmailAddresses']["Invoice"]["Value"] = $email;
          $company["companies"][0]['EmailAddresses']["Invoice"]["Name"] = $email;
          $company["companies"][0]['EmailAddresses']["Invoice"]["Description"] = $email;

          $company["companies"][0]['EmailAddresses']["Work"]["Value"] = $email;
          $company["companies"][0]['EmailAddresses']["Work"]["Name"] = $email;
          $company["companies"][0]['EmailAddresses']["Work"]["Description"] = $email;

          $NewCompanyResult = ($CompanyService->SaveCompanies($company));

          return $NewCompanyResult;
      }

   ?>