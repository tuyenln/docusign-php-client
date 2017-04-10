<?php

require_once('vendor/autoload.php');

class DocuSignSample
{
    public function signatureRequestFromTemplate()
    {
        $username = "tl.ngoc@bmi-system.com";
        $password = "tlngoc091085";
        $integrator_key = "f1f99579-05a4-4e51-afff-deea4accaac6";     

        // change to production (www.docusign.net) before going live
        $host = "https://demo.docusign.net/restapi";

        // var_dump($host);die;

        // create configuration object and configure custom auth header
        $config = new DocuSign\eSign\Configuration();
        $config->setHost($host);
        $config->addDefaultHeader("X-DocuSign-Authentication", "{\"Username\":\"" . $username . "\",\"Password\":\"" . $password . "\",\"IntegratorKey\":\"" . $integrator_key . "\"}");

        // instantiate a new docusign api client
        $apiClient = new DocuSign\eSign\ApiClient($config);
        // var_dump($apiClient);
        $accountId = null;
        
        try 
        {
            //*** STEP 1 - Login API: get first Account ID and baseURL
            $authenticationApi = new DocuSign\eSign\Api\AuthenticationApi($apiClient);
            // var_dump($authenticationApi);die;
            $options = new \DocuSign\eSign\Api\AuthenticationApi\LoginOptions();
            $loginInformation = $authenticationApi->login($options);

            if(isset($loginInformation) && count($loginInformation) > 0)
            {
                $loginAccount = $loginInformation->getLoginAccounts()[0];
                $host = $loginAccount->getBaseUrl();
                $host = explode("/v2",$host);
                $host = $host[0];
	
                // UPDATE configuration object
                $config->setHost($host);
		
                // instantiate a NEW docusign api client (that has the correct baseUrl/host)
                $apiClient = new DocuSign\eSign\ApiClient($config);
                var_dump($apiClient);
	
                if(isset($loginInformation))
                {
                    $accountId = $loginAccount->getAccountId();
                    if(!empty($accountId))
                    {
                        //*** STEP 2 - Signature Request from a Template
                        // create envelope call is available in the EnvelopesApi
                        $envelopeApi = new DocuSign\eSign\Api\EnvelopesApi($apiClient);
                        // assign recipient to template role by setting name, email, and role name.  Note that the
                        // template role name must match the placeholder role name saved in your account template.
                        $templateRole = new  DocuSign\eSign\Model\TemplateRole();
                        $templateRole->setEmail("[SIGNER_EMAIL]");
                        $templateRole->setName("[SIGNER_NAME]");
                        $templateRole->setRoleName("[ROLE_NAME]");             

                        // instantiate a new envelope object and configure settings
                        $envelop_definition = new DocuSign\eSign\Model\EnvelopeDefinition();
                        $envelop_definition->setEmailSubject("[DocuSign PHP SDK] - Signature Request Sample");
                        $envelop_definition->setTemplateId("[TEMPLATE_ID]");
                        $envelop_definition->setTemplateRoles(array($templateRole));
                        
                        // set envelope status to "sent" to immediately send the signature request
                        $envelop_definition->setStatus("sent");

                        // optional envelope parameters
                        $options = new \DocuSign\eSign\Api\EnvelopesApi\CreateEnvelopeOptions();
                        $options->setCdseMode(null);
                        $options->setMergeRolesOnDraft(null);

                        // create and send the envelope (aka signature request)
                        $envelop_summary = $envelopeApi->createEnvelope($accountId, $envelop_definition, $options);
                        if(!empty($envelop_summary))
                        {
                            echo "$envelop_summary";
                        }
                    }
                }
            }
        }
        catch (DocuSign\eSign\ApiException $ex)
        {
            echo "Exception: " . $ex->getMessage() . "\n";
        }
    }
}

$a =  new DocuSignSample();
var_dump($a->signatureRequestFromTemplate());

?>