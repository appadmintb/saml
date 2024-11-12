<?php 
    require './sso/sso/SAMLRequest.class.php';
    require './sso/sso/SAMLResponse.class.php';

    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization, X-TB-Access-Token, Access-Control-Allow-Headers, X-TB-Auth-Token");
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    }    

    if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
        header('HTTP/1.1 200 OK');
        exit();
    }

    class RequestSaml{
        
        public $forceAuth = true;
        
        //public $consumerServiceUrl = 'https://stagingapi.tuboleta.com/index.php/userOnboarding/loginSecutix';
        //public $consumerServiceUrl = 'https://wonderful-forest-0b5b6f91e.5.azurestaticapps.net';
        // public $issuer = 'SsoTuboleta';
        public $issuer = 'SsoTuboletaTST';
        //Test
        public $consumerServiceUrl = 'https://vet.app-tuboleta.com/AppWeb/Login/Auth';
        //public $issuer = '634742479';
        //End Test
        public $passPhrase = '';
        public $privKeyPath = 'wild.tuboleta.com-2020-07-31-130631.pkey';
        public $certicatePath = 'wild.tuboleta.com-2020-07-31-130631.pem';
        public $SAMLSchemaPath = 'sso/sso/schema/saml-schema-protocol-2.0.xsd';
        public $idPCertPath = 'sso/identity-provider.cert.pem';


        public function build(){
            $request = new SAMLRequest();
            // $forceAuth = true;
            // $consumerServiceUrl = 'https://tuboleta.com/login';
            // $issuer = 'SsoTuboleta';
            // $passPhrase = '';
            // $privKeyPath = 'wild.tuboleta.com-2020-07-31-130631.pkey';
            // $certicatePath = 'wild.tuboleta.com-2020-07-31-130631.pem';
            $response = $request->build($this->forceAuth, $this->consumerServiceUrl, $this->issuer, $this->passPhrase, $this->privKeyPath, $this->certicatePath);
            
            echo json_encode($response);
            
            // $requestId = $SAMLRequest['id'];
            // $requestEncoded64 = 'PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48c2FtbDJwOlJlc3BvbnNlIHht
            // bG5zOnNhbWwycD0idXJuOm9hc2lzOm5hbWVzOnRjOlNBTUw6Mi4wOnByb3RvY29sIiBJRD0iYTAz
            // OTc4MTU0LWY5MzMtNDhhNS04YWJjLTZmY2E5ZDJjNjU3YSIgSW5SZXNwb25zZVRvPSJfM2Y2YWQ5
            // ZWQ4NzAxYTQ1Y2Y1YmQxYjhlZjI3OGQ3NWI0NTZhNDAxNzJiIiBJc3N1ZUluc3RhbnQ9IjIwMjAt
            // MDgtMDVUMTU6MTE6MDcuMDI1WiIgVmVyc2lvbj0iMi4wIj48c2FtbDI6SXNzdWVyIHhtbG5zOnNh
            // bWwyPSJ1cm46b2FzaXM6bmFtZXM6dGM6U0FNTDoyLjA6YXNzZXJ0aW9uIj5pZGVudGl0eS1wcm92
            // aWRlcjwvc2FtbDI6SXNzdWVyPjxkczpTaWduYXR1cmUgeG1sbnM6ZHM9Imh0dHA6Ly93d3cudzMu
            // b3JnLzIwMDAvMDkveG1sZHNpZyMiPjxkczpTaWduZWRJbmZvPjxkczpDYW5vbmljYWxpemF0aW9u
            // TWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMS8xMC94bWwtZXhjLWMxNG4j
            // Ii8+PGRzOlNpZ25hdHVyZU1ldGhvZCBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDAv
            // MDkveG1sZHNpZyNyc2Etc2hhMSIvPjxkczpSZWZlcmVuY2UgVVJJPSIjYTAzOTc4MTU0LWY5MzMt
            // NDhhNS04YWJjLTZmY2E5ZDJjNjU3YSI+PGRzOlRyYW5zZm9ybXM+PGRzOlRyYW5zZm9ybSBBbGdv
            // cml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvMDkveG1sZHNpZyNlbnZlbG9wZWQtc2lnbmF0
            // dXJlIi8+PGRzOlRyYW5zZm9ybSBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDEvMTAv
            // eG1sLWV4Yy1jMTRuIyIvPjwvZHM6VHJhbnNmb3Jtcz48ZHM6RGlnZXN0TWV0aG9kIEFsZ29yaXRo
            // bT0iaHR0cDovL3d3dy53My5vcmcvMjAwMC8wOS94bWxkc2lnI3NoYTEiLz48ZHM6RGlnZXN0VmFs
            // dWU+andRUmdYeTRxcEpMTHZEYTJyRGJFSUVCNXk0PTwvZHM6RGlnZXN0VmFsdWU+PC9kczpSZWZl
            // cmVuY2U+PC9kczpTaWduZWRJbmZvPjxkczpTaWduYXR1cmVWYWx1ZT5mTVhEZll0aFBUNmJLSnhk
            // R1pDY09TMW04OEdrM3o2WmplbWdWVjYwcGJ0NU1DdmZmT3B5SzYzMU41b0Y4VEtsSEpCbnNENXoy
            // QmdLRnRmelh4QW0xOTRIbzJTODdhaHQ5WHVad1RFelRGQkwvQjBvbk1tbEZ3N3pQWW02U1U1d2k1
            // aVhUMnc0aHNBU0hwckhLcUh5RnUrNmxSZU8vcnplc2dvL0JOV3FQNXc9PC9kczpTaWduYXR1cmVW
            // YWx1ZT48ZHM6S2V5SW5mbz48ZHM6S2V5VmFsdWU+PGRzOlJTQUtleVZhbHVlPjxkczpNb2R1bHVz
            // PmllaWN3WGZ6aUJvY3BOek9udUFPQVJuTS9nc0xINW01R3BuYzdIOVNQU2RaQ0loaGFBeUl4K3RM
            // N1FJMWdwNmx5MmNxK1JwNlpBSmsKaHZNeDd5Rm9LaHdmL3NmZEZnd2JhMXFTMk52TUZLZXNJUVZr
            // MTRUZUIyM2NkOW1JMnpUTDBlQ002YzFPQVFhbXI2KzRzNDZQOEU1bApZUXBKTHVhbDZHNUVXL2RN
            // dG9jPTwvZHM6TW9kdWx1cz48ZHM6RXhwb25lbnQ+QVFBQjwvZHM6RXhwb25lbnQ+PC9kczpSU0FL
            // ZXlWYWx1ZT48L2RzOktleVZhbHVlPjwvZHM6S2V5SW5mbz48L2RzOlNpZ25hdHVyZT48c2FtbDJw
            // OlN0YXR1cz48c2FtbDJwOlN0YXR1c0NvZGUgVmFsdWU9InVybjpvYXNpczpuYW1lczp0YzpTQU1M
            // OjIuMDpzdGF0dXM6VW5rbm93blByaW5jaXBhbCIvPjwvc2FtbDJwOlN0YXR1cz48L3NhbWwycDpS
            // ZXNwb25zZT4=';
            // $SAMLSchemaPath = 'sso/sso/schema/saml-schema-protocol-2.0.xsd';
            // $idPCertPath = 'sso/identity-provider.cert.pem';

        }

        public function decode($requestId,$requestEncoded64){
            $responseSaml = new SAMLResponse();
            $response = $responseSaml->decode($requestId,$requestEncoded64,$this->consumerServiceUrl, $this->issuer, $this->SAMLSchemaPath, $this->idPCertPath);
            echo json_encode($response);
        }
    }

    $requestSaml = new RequestSaml();
    if(isset($_POST['action'])){
        $action = $_POST['action'];
        switch($action){
            case 'build':
                $requestSaml->build();
            break;
        }
    }else if(isset($_POST['SAMLResponse']) && isset($_POST['RelayState'])){
        $requestId = $_POST['RelayState'];
        $requestEncoded64 = $_POST['SAMLResponse'];
        $requestSaml->decode($requestId,$requestEncoded64);
    }else{
        echo json_encode(['code' => 102, 'error' => 'No Action Received']);
    }

    
?>