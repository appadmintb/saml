<?php
/*
 * Helper for handling SAML response
*
* @author             Sébastien Gremion
* @access             public
* ****************************************************
*/
define('PASSWORD_PROTECTED', 'urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport');

require_once ('_autoload.php');
require_once ('xmlseclibs.php');
class SAMLResponse {
  private static function error($msg) {
    return array(
        'error' => true,
        'msg' => $msg 
    );
  }
  private static function wronglogin() {
    return array(
        'error' => false,
        'contact_number' => NULL 
    );
  }
  private static function logged($contact_number) {
    return array(
        'error' => false,
        'contact_number' => $contact_number 
    );
  }
  
  /**
   * Decode an SAMLResponse from the Secutix IDP
   * 
   * @param string $requestID
   * @param string $samlResponseEncoded
   * @param string $consumerServiceUrl
   * @param string $issuer
   * @param string $SAMLSchemaPath
   * @param string $idPCertPath
   * @return multitype:boolean unknown |multitype:boolean NULL
   */
  public static function decode($requestID, $samlResponseEncoded, $consumerServiceUrl, $issuer, $SAMLSchemaPath, $idPCertPath) {
    $samlResponse = base64_decode($samlResponseEncoded);
    
    if (is_null($requestID)) {
      die("SAMLResponse: Missing requestID");
    }
    if (is_null($samlResponseEncoded)) {
      die("SAMLResponse: Missing samlResponseEncoded");
    }
    if (is_null($consumerServiceUrl)) {
      die("SAMLResponse: Missing consumerServiceUrl");
    }
    if (is_null($issuer)) {
      die("SAMLResponse: Missing issuer");
    }
    if (is_null($SAMLSchemaPath)) {
      die("SAMLResponse: Missing SAMLSchemaPath");
    }
    if (is_null($idPCertPath)) {
      die("SAMLResponse: Missing idPCertPath");
    }
    
    //Validate against XSD schema
    //Avoid hugly warning in case of validation crash
    libxml_use_internal_errors(true);
    $xmlReader = new XMLReader();
    $xmlReader->XML($samlResponse);
    $xmlReader->setSchema($SAMLSchemaPath);
    while ($xmlReader->read())
      ;
    if (!$xmlReader->isValid()) {
      return SAMLResponse::error('SAMLResponse not valid compared to the SAML Schema');
    }
    libxml_clear_errors();
    libxml_use_internal_errors(false);
    
    //Create DOM representation
    $document = new DOMDocument();
    $document->loadXML($samlResponse);
    $xml = $document->firstChild;
    
    //-----------------------------Signature verification DO NOT PUT IT AFTER SAML2_Message::fromXML($xml) because modify $xml -------------------------------------
    //Identity provider key
    $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array(
        'type' => 'public' 
    ));
    $objKey->loadKey($idPCertPath, TRUE, TRUE);
    
    $signature = new XMLSecurityDSig();
    if (!$signature->locateSignature($xml)) {
      return SAMLResponse::error('The SAMLResponse doesn\'t contain any signature element');
    }
    
    /* Canonicalize the XMLDSig SignedInfo element in the message. */
    $signature->canonicalizeSignedInfo();
    
    try {
      $retVal = $signature->validateReference();
      if (!$retVal) {
        return SAMLResponse::error('Reference Validation Failed during SAMLResponse treatement');
      }
    } catch (Exception $e) {
      return SAMLResponse::error('Error during reference validation of the SAMLResponse');
    }
    
    /* Check the signature. */
    if (!$signature->verify($objKey)) {
      return SAMLResponse::error("Invalid signature in the SAMLResponse");
    }
    
    //--------------------------------------------------------------------------------------------------------------------------------------------------------------
    

    //Response in object
    try {
      $response = SAML2_Message::fromXML($xml);
    } catch (Exception $e) {
      return SAMLResponse::error('Not a correct SAMLResponse was received');
    }
    
    if (!($response instanceof SAML2_Response)) {
      return SAMLResponse::error('Not a SAMLResponse received');
    }
    
    //Check on the data
    if ($response->getIssuer() == NULL) {
      return SAMLResponse::error('The issuer of the SAMLResponse cannot be null');
    }
    
    if ($response->getInResponseTo() !== $requestID) {
      return SAMLResponse::error('The SAMLResponse is unsollicited :\nFound : ' . $response->getInResponseTo() . '\nExpected : ' . $requestID);
    }
    
    if (!$response->isSuccess()) {
      return SAMLResponse::wronglogin();
    }
    
    //Get the assertion
    $assertions = $response->getAssertions();
    if (count($assertions) == 0) {
      return SAMLResponse::error('A assertion should be present in the SAMLResponse');
    }
    
    $assert = $assertions[0];
    if ($assert->getAuthnContext() == NULL) {
      return SAMLResponse::error('No AuthnStatement or AuthnContext specified in the SAMLResponse');
    }
    
    if ($assert->getAuthnContext() != PASSWORD_PROTECTED) {
      return SAMLResponse::error('Unexpected authentification context (expected :' . PASSWORD_PROTECTED . ', received : ' . $assert->getAuthnContext() . ')');
    }
    
    if ($assert->getValidAudiences() == NULL || count($assert->getValidAudiences()) != 1) {
      return SAMLResponse::error('An audience restriction must be specified in the SAMLResponse');
    }
    
    $audienceArray = $assert->getValidAudiences();
    $firstAudience = $audienceArray[0];
    if ($firstAudience != $issuer) {
      return SAMLResponse::error('Message not meant for this service (received first audience : ' . $firstAudience . ')');
    }
    
    $subjConfirms = $assert->getSubjectConfirmation();
    if ($subjConfirms == NULL || count($subjConfirms) == 0) {
      return SAMLResponse::error('No subject confirmation in the SAMLResponse');
    }
    
    $subj = $subjConfirms[0];
    
    $subjData = $subj->SubjectConfirmationData;
    if ($subjData == NULL) {
      return SAMLResponse::error('No subject confirmation data in the SAMLResponse');
    }
    
    if ($subjData->Recipient == NULL || $subjData->Recipient != $consumerServiceUrl) {
      return SAMLResponse::error('No or wrong recipient for the assertion (received : ' . $subjData->Recipient . ')');
    }
    
    if ($subjData->NotOnOrAfter == NULL || $subjData->NotOnOrAfter < time()) {
      return SAMLResponse::error('Expired assertion (expiration date : ' . $subjData->NotOnOrAfter . ')');
    }
    
    if ($assert->getAttributes() == NULL || count($assert->getAttributes()) == 0) {
      return SAMLResponse::error('No attribute defined in the SAMLResponse');
    }
    
    $attributes = $assert->getAttributes();
    if (!array_key_exists('role', $attributes)) {
      return SAMLResponse::error('No role defined in the SAMLResponse');
    }
    
    $role = $attributes['role'];
    if (count($role) == 0) {
      return SAMLResponse::error('No value for the role in the SAMLResponse');
    }
    
    if ($role[0] != 'webUser' && $role[0] != 'webUserPro') {
      return SAMLResponse::error('Wrong role in the SAMLResponse expected webUser or webUserPro, get ' . $role[0]);
    }
    
    //Get the name ID
    $nameid = $assert->getNameId();
    
    //Check that a name id has been specified
    if ($nameid == NULL) {
      return SAMLResponse::error('No contact number in the assertion');
    }
    
    $contact_number = $nameid['Value'];
    
    //Return success
    return SAMLResponse::logged($contact_number);
  }
}