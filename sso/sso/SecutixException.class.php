<?php
/**
 * Standard tnts class for managing exceptions
 */
define('EXCEPTION_PORTAL_SYSTEM_ERROR',1);
class SecutixException extends Exception{

    public $originalInvocations = array(null);
    public $uniqueIdentifier = null;

    public $option_parameters;	//array of values that comes from the context
    protected $_severity_log; //severy for the log message : info, warning, error, debug
    protected $_system_message; //system message for the log

    public function __construct($severity_level="error",$message,$optional_parameters=null){
        $this->_severity_log = $severity_level;
        $this->option_parameters = $optional_parameters;
        $this->formatExceptionMessage($message,$optional_parameters);
        $this->mapErrorMessageType($this->_severity_log,$this->_system_message);
        parent::__construct($this->_system_message,EXCEPTION_PORTAL_SYSTEM_ERROR);
    }

    /**
     * Format the message according to the optional_parameters gived
     * @param string $message
     * @param mixed[] $optional_parameters
     * @return string
     */
    protected function formatExceptionMessage($message,$optional_parameters){
        if((!is_null($optional_parameters))&&(!empty($optional_parameters))){
            $this->_system_message=vsprintf($message,$optional_parameters);
        }
        else
        $this->_system_message=$message;
    }

    /**
     * Log into the application file according to the different message types
     * @param $severity : error type (info, warning, error)
     * @param $message : error message to log
     * @return true
     */
    protected function mapErrorMessageType($severity,$message){
        $log_str = get_class($this);
        switch($severity){
            case "debug" :
                print($message);
                break;
            case "info":
                print($message);
                break;
            case "warning":
                print($message);
                break;
            case "error":
                print($message);
                break;
            case "fatal":
                print($message);
                break;
            default: //error
                print($message);
        }
    }
}

//shared Exception among services

class WrongUserActionException extends SecutixException {
    public function __construct($option_parameters){
        parent::__construct('error', 'Wrong user action at [%s], with parameters : [%s]', $option_parameters);
    }
}

class CacheEnableServiceWrapperUndefinedMethodException extends SecutixException{
    public function __construct($option_parameters){
        parent::__construct("error","Undefined method [%s], with parameters : [%s]",$option_parameters);
    }
}

class UnknowFileFormatException extends SecutixException{
    public function __construct($option_parameters){
        parent::__construct("error","Unknown file format for file [%s]",$option_parameters);
    }
}

class InvalidActionUrlException extends SecutixException {
    public function __construct(){
        parent::__construct("warning","Invalid action url");
    }
}

class CannotLoadBasicClassException extends SecutixException{
    public function __construct($option_parameters){
        parent::__construct("error","Cannot load basic class with name=[%s] and type=[%s] at %s:%s",$option_parameters);
    }
}

class CannotLoadBasicModuleException extends SecutixException{
    public function __construct($option_parameters){
        parent::__construct("error","Cannot load basic module with name=[%s] and type=[%s]",$option_parameters);
    }
}

class CannotLoadPageException extends SecutixException{
    public function __construct($option_parameters){
        parent::__construct("error","Cannot load page with id=[%s]",$option_parameters);
    }
}

class InvalidFilterStateException extends SecutixException{
    public function __construct($option_parameters){
        parent::__construct("error","Invalid state [%s] for current filter",$option_parameters);
    }
}

class UnknownHessianErrorException extends SecutixException{
    /**
     * Constructor
     * @param string $message Message to trace
     * @param array $optional_parameters Optional parameters (Optional)
     * @return None
     */
    public function __construct($message, $optional_parameters=null){
        parent::__construct("error", "Unknown Hessian error (see Hessian log for more info): " . $message, $optional_parameters);
    }
}

class TimeoutResponseHessianErrorException extends SecutixException{
	public function __construct(){
		parent::__construct("error","Socket timeout while contacting the webservice");
	}
}

class UnknownSoapErrorException extends SecutixException{
    /**
     * Constructor
     * @param string $message Message to trace
     * @param array $optional_parameters Optional parameters (Optional)
     * @return None
     */
    public function __construct($message, $optional_parameters=null){
        parent::__construct("fatal", "Unknown SOAP error: " . $message . " ", $optional_parameters);
    }
}

//Exception happenning in tnco
class EntityNotFoundException extends SecutixException{
    public function __construct($option_parameters=null){
            parent::__construct("error","The contact entity was not found.",$option_parameters);
    }
}

class InvalidContactException extends SecutixException{
    public function __construct($option_parameters=null){
            parent::__construct("error","Invalid contact ",$option_parameters);
    }
}

class IllegalArgumentException extends SecutixException{
    public function __construct($option_parameters=null){
            parent::__construct("error","Illegal argument are passed (null or wrong type) ",$option_parameters);
    }
}

class DataAccessException extends SecutixException{
    public function __construct($option_parameters=null){
            parent::__construct("error","Data access error ",$option_parameters);
    }
}

class ArithmeticException extends SecutixException {
    /**
     * Constructor
     * @param array $optional_parameters Optional parameters (Optional)
     * @return None
     */
    public function __construct($optional_parameters=null){
        parent::__construct("error", "Error in arithmetic ", $optional_parameters);
    }
}

class NonUniqueResultException extends SecutixException{
    public function __construct($option_parameters=null){
            parent::__construct("error","There are more than one contact matching the information ",$option_parameters);
    }
}
class ItemAvailabilityException extends SecutixException{
    public function __construct($option_parameters=null){
         parent::__construct("error","There is not enough availability ",$option_parameters);
    }
}
/**
 * This abstract class should be used for all exception triggered when a business occurs.
 * Thus all business error can be centralized in one point since their behaviors are similars
 *
 * @author ejo
 *
 */
class BusinessException extends SecutixException{

	protected $labelKey;

	/**
	 * The constructor get the standard SecutixException parameters but also a labelKey that can be used afterward to translate a message to the end user
	 *
	 * @param String $message
	 * @param String $label
	 * @param array $optional_parameters
	 * @param String $severity_level
	 * @return void
	 */
	public function __construct($message, $labelKey, $optional_parameters=null, $severity_level="warning"){
			$this->labelKey = $labelKey;
            parent::__construct($severity_level, $message, $optional_parameters);
    }

    /**
     * Retrieves the label key stored by this exception
     *
     * @return String
     */
    public function getLabelKey(){
    	return $this->labelKey;
    }
}
