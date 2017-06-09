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
              print_r($authentication);

              // throw an error if the login is unsuccessful
              if($authentication->HasSession()->HasSessionResult == false)
                  throw new SoapFault("0", "Invalid credential information.");
          }
          // To get current identity:
           print_r($authentication->GetIdentity());

          // To connect to a 24seven webservice:

          // START NEW COMPANY Params
          $CompanyService = new SoapClient ( "https://api.24sevenoffice.com/CRM/Company/V001/CompanyService.asmx?WSDL", $options );
          $CompanyService->__setCookie("ASP.NET_SessionId", $_SESSION['ASP.NET_SessionId']);
          
          $company["companies"][0]['Name'] ="FROM EARNSHARK";
          $company["companies"][0]['Type'] ="Business";
          $company["companies"][0]['OrganizationNumber'] ="EARNSHARK ID";
          $company["companies"][0]['EmailAddresses']["Invoice"]["Value"] = "EMAIL";
          $company["companies"][0]['EmailAddresses']["Invoice"]["Name"] = "EMAIL";
          $company["companies"][0]['EmailAddresses']["Invoice"]["Description"] = "EMAIL";

          $company["companies"][0]['EmailAddresses']["Work"]["Value"] = "EMAIL";
          $company["companies"][0]['EmailAddresses']["Work"]["Name"] = "EMAIL";
          $company["companies"][0]['EmailAddresses']["Work"]["Description"] = "EMAIL";

          $NewCompanyResult = ($CompanyService->SaveCompanies($company));

          print_r($NewCompanyResult);
          // END NEW COMPANY Params
      } 
      catch ( SoapFault $fault ) 
      {
          echo 'Exception: ' . $fault->getMessage();
      }
   ?>