<?php
    require_once('./docusign-php-client/autoload.php');
    // DocuSign account credentials & Integrator Key
    $username = "tl.ngoc@bmi-system.com";
    $password = "tlngoc091085";
    $integrator_key = "[INTEGRATOR_KEY]";
    // DocuSign environment we are using
    $host = "https://demo.docusign.net/restapi";
    // create a new DocuSign configuration and assign host and header(s)
    $config = new DocuSign\eSign\Configuration();
    $config->setHost($host);
    $config->addDefaultHeader("X-DocuSign-Authentication", "{\"Username\":\"" . $username . "\",\"Password\":\"" . $password . "\",\"IntegratorKey\":\"" . $integrator_key . "\"}");
    // instantiate a new docusign api client
    $apiClient = new DocuSign\eSign\ApiClient($config);
    // we will first make the Login() call which exists in the AuthenticationApi...
    $authenticationApi = new DocuSign\eSign\Api\AuthenticationApi($apiClient);
    // optional login parameters
    $options = new \DocuSign\eSign\Api\AuthenticationApi\LoginOptions();
    // call the login() API
    $loginInformation = $authenticationApi->login($options);
    // parse the login results
    if(isset($loginInformation) && count($loginInformation) > 0)
    {
        // note: defaulting to first account found, user might be a 
        // member of multiple accounts
        $loginAccount = $loginInformation->getLoginAccounts()[0];
        if(isset($loginInformation))
        {
            $accountId = $loginAccount->getAccountId();
            if(!empty($accountId))
            {
                echo "Account ID = $accountId\n";
            }
        }
    }
?>    