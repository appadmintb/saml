<?php
/**
 * Helper for SAMLRequest creation
 *
 * @author             SGO, BBA
 * @access             public
 * ****************************************************
 */
require_once ('_autoload.php');
require_once ('xmlseclibs.php');

define('USERNAME_FIELD_NAME', 'username');
define('PASSWORD_FIELD_NAME', 'password');
define('STRUCTURE_CODE_FIELD_NAME', 'structureCode');
define('PROVIDER_CODE_FIELD_NAME', 'provider');
define('FACEBOOK_PROVIDER_CODE', 'FACEBOOK');
define('GOOGLE_PROVIDER_CODE', 'GOOGLE');
define('TWITTER_PROVIDER_CODE', 'TWITTER');
class SAMLRequest {
  
  /**
   * Build a SAMLRequest
   * @param boolean $forceAuth
   * @param string $consumerServiceUrl url where the SAML response will be sent
   * @param string $issuer identifier of the service provider  
   * @param string $passPhrase passphrase for the private key used for encryption
   * @param string $privKeyPath path to the private key
   * @param string $certicatePath path to the public certificate corresponding to the private key
   * @return multitype:string
   */
  public static function build($forceAuth, $consumerServiceUrl, $issuer, $passPhrase, $privKeyPath, $certicatePath) {
    
    //Create SAML Request
    $ar = new SAML2_AuthnRequest();
    
    $id = $ar->getId();
    
    if (is_null($consumerServiceUrl)) {
      die("SAMLRequest: Missing consumerServiceUrl");
    }
    if (is_null($issuer)) {
      die("SAMLRequest: Missing issuer");
    }
    if (is_null($privKeyPath)) {
      die("SAMLRequest: Missing privKeyPath");
    }
    if (is_null($certicatePath)) {
      die("SAMLRequest: Missing certicatePath");
    }
    
    $ar->setForceAuthn($forceAuth);
    $ar->setAssertionConsumerServiceURL($consumerServiceUrl);
    $ar->setIssuer($issuer);
    
    //Signature
    //Key
    $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array(
        'type' => 'private' 
    ));
    $objKey->passphrase = $passPhrase;
    $objKey->loadKey($privKeyPath, TRUE);
    
    $ar->setSignatureKey($objKey);
    
    //Put key info -> certificate value
    $ar->setCertificates(array(
        file_get_contents($certicatePath) 
    ));
    
    //Redirect to form to send SAMLRequest
    $msgStr = $ar->toSignedXML();
    $msgStr = $msgStr->ownerDocument->saveXML($msgStr);
    $encode64 = base64_encode($msgStr);
    
    return array(
        'requestEncoded64' => $encode64,
        'id' => $id 
    );
  }
  public static function getUsernameFieldName() {
    return USERNAME_FIELD_NAME;
  }
  public static function getPasswordFieldName() {
    return PASSWORD_FIELD_NAME;
  }
  public static function getStructureCodeFieldName() {
    return STRUCTURE_CODE_FIELD_NAME;
  }
  public static function getProviderCodeFieldName() {
    return PROVIDER_CODE_FIELD_NAME;
  }
  public static function getFacebookProviderCode() {
    return FACEBOOK_PROVIDER_CODE;
  }
  public static function getGoogleProviderCode() {
    return GOOGLE_PROVIDER_CODE;
  }
  public static function getTwitterProviderCode() {
    return TWITTER_PROVIDER_CODE;
  }
}