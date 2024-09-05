<?php

/*---------------------------------------------
API SENIORIALES
Auteur : CAPLAIN THOMAS (Vertuoz)
Version : 2.2 / 170918
Contact : thomas@vertuoz.fr


CHANGELOG
---------
V 2.3 210918
- Ajout de méthode sendFileByMail

V 2.2 170918
- Ajout de méthodes de gestion de fichiers distants uploadFile,getDownloadLinkFile,deleteFile

V 2.1 301014
- Correction faille manager avec ajout fonctionnement session
- Redirection pour supprimer paramétre

V 2.0 (version majeure) - 040113
- Ajout test serveur API, connexion serveur de secours.
- Ajout destruction de la session sur erreur.
- Passage de la cle de l'application au manager sur erreur (usages futurs)
- Ajout fonction mise � jour distante

V 1.6.1 - 280312
- Ajout ReplyToMail, ReplyToName & CopyEmail a la fonction d'envoi d'email

V 1.6 - 010112
- Prise en charge des messages de services

V 1.5 - 221211
- Correction bug securite session (ajout controle variable sessions "token user" propre a l'application)
- Changement fonctionnement erreur (envoi de code d'erreur plutot que texte)

V 1.4 - 151111
- Ajout de la methode getCmsContentDistantUrl
- Ajout du parametre backUrl a methode userCheckLogin permettant de forcer une url de retour

V 1.3.1 - 071111
- Ajout parametre nbLu sur getCmsContent et getMode

V 1.3 - 191011
- Prise en charge de la gestion de contenu
- Ajout des methodes getCmsContentsType, getCmsContent, getCmsContentsList

V 1.2 - 080611
- Modification fonction initBatch avec test de frequence d'execution du batch
- Correction si parametre message manquant sur fonction updateBatch.
- Ajout info page courante sur pingUser

V 1.1 - 030611
- Modification fonction de log pour autoriser l'appel a la methode m�me si non connecte
- Ajout de l'appel a la methode pingUser pour une utilisation future de tra�age

V 1.0.1 - 240211
- Correction bug methode "soapCheckErrors"

V 1.0 - 160211
- Creation de l'API

------------------------------------------------*/

@include("nusoap/nusoap.php");

class ApiSenioriales {

    public static $applicationKey  = "OFFRESCOMM21"; // IMPORTANT
    public static $secureKey       = "H!Uj0,89ij"; // NE PAS MODIFIER
    public static $version         = "1.0";
    public  $client         = null;

    public  $authUser        = null;
    public  $application     = null;

    private $mainBaseUrl     = "https://manager.senioriales.com";
    private $rescueBaseUrl   = "https://manager2.senioriales.com";
    private $loginUrl        = "/login";
    private $logoutUrl       = "/logout";
    private $errorUrl        = "/error";

    //Parametres de connexion au web service
    private $soapServerUrl   = "/api/soap?wsdl";
    private $wsdlAuthUser    = "senioriales";
    private $wsdlAuthPwd     = "Gj,9!f.n";
    private $soapError       = null;
    private $soapResult      = null;

    /*------------------------------------------------------------------------------------------------
     * METHODE : apiSenioriales
     * PARAMETRES : aucun
     * DESCRIPTION : Constructeur de l'application,
     * verification des parametres, de l'acces au web service
     * et de l'etat de l'application
     * RETOUR : aucun
     *------------------------------------------------------------------------------------------------*/
    public function ApiSenioriales() {

        @session_start();

        //Verification extension curl
        if(!in_array ('curl', get_loaded_extensions())) {
            $this->destroySession();
            header("location:".$this->mainBaseUrl.$this->errorUrl."?aspask=".ApiSenioriales::$applicationKey."&code=PHPCONFIGCURL");
            die();
        }

        if(strlen(ApiSenioriales::$applicationKey) != 12) {
            $this->destroySession();
            header("location:".$this->mainBaseUrl.$this->errorUrl."?aspask=".ApiSenioriales::$applicationKey."&code=WRONGAPPKEY");
            die();
        }

        //Determination de l'URL du serveur manager toutes les 2 minutes
        if(!array_key_exists("APISENIORIALESSERVERBASE", $_SESSION) || (array_key_exists("APISENIORIALESSERVERBASE", $_SESSION) && time() - $_SESSION['APISENIORIALESSERVERBASETMS'] >= 120)) {

            if(!$this->checkUrl($this->mainBaseUrl.$this->soapServerUrl))
                $this->mainBaseUrl = $this->rescueBaseUrl;

            $_SESSION['APISENIORIALESSERVERBASE'] = $this->mainBaseUrl;
            $_SESSION['APISENIORIALESSERVERBASETMS'] = time();
        }
        else
            $this->mainBaseUrl = $_SESSION['APISENIORIALESSERVERBASE'];

        $this->client = new nusoap_client($this->mainBaseUrl.$this->soapServerUrl,true);
        $this->client->setCredentials($this->wsdlAuthUser, $this->wsdlAuthPwd, 'basic');

        $this->soapCheckErrors();

        //Chargement des parametres de l'application / Determination de l'acces http au fichier de l'API
        $basePath        = preg_replace("/([^\/]+\.php$)/","",$_SERVER['PHP_SELF']);
        $clientApiPath   = preg_replace("/.+(".str_replace("/","\/",$basePath).".*)/","$1",realpath(__FILE__));
        $apiLocationUrl  = (isset($_SERVER['HTTPS']) && strlen($_SERVER['HTTPS']) > 0)?"https://".$_SERVER['SERVER_NAME']:"http://".$_SERVER['SERVER_NAME'];
        $apiLocationUrl .= $clientApiPath;

         $this->application = $this->soapDoRequest("initApplication", array("cle" => ApiSenioriales::$applicationKey, "api_location_url" => ApiSenioriales::$version."##".$apiLocationUrl));

        if(is_array($this->application)) {
            if($this->application['status'] == 0) {
                $this->destroySession();
                header("location:".$this->mainBaseUrl.$this->errorUrl."?aspask=".ApiSenioriales::$applicationKey."&code=OFFLINE");
                die();
            }
        }
        else {
            $this->destroySession();
            header("location:".$this->mainBaseUrl.$this->errorUrl."?aspask=".ApiSenioriales::$applicationKey."&code=WRONGAPPPARAMETERS");
            die();
        }
    }

    private function checkUrl($url=null,$timeout=3) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($httpcode >= 200 && $httpcode < 300)
            return true;
        else
            return false;
    }

    /*------------------------------------------------------------------------------------------------
     * METHODE : getKeyForId
     * PARAMETRES : $userId = l'id de l'utilisateur
     * DESCRIPTION : Retourne la cle de securite de l'utilisateur
     * RETOUR : la cle de securite de l'utilisateur
     *------------------------------------------------------------------------------------------------*/
    public static function getKeyForUserId($userId) {
        return md5($userId.ApiSenioriales::$secureKey);
    }

    /*------------------------------------------------------------------------------------------------
     * METHODE : getKeyForId
     * PARAMETRES : $userId = l'id de l'utilisateur
     * DESCRIPTION : Retourne la cle de securite de l'utilisateur
     * RETOUR : la cle de securite de l'utilisateur
     *------------------------------------------------------------------------------------------------*/
    private function getUserApplicationToken() {

        if(is_null($this->authUser)) {
            $this->destroySession();
            header("location:".$this->mainBaseUrl.$this->errorUrl."?aspask=".ApiSenioriales::$applicationKey."&code=USERAPPTOKEN");
            die();
        }
        else
            return md5(ApiSenioriales::getKeyForUserId($this->authUser['id']).ApiSenioriales::$applicationKey.ApiSenioriales::$secureKey);
    }

    /*------------------------------------------------------------------------------------------------
     * METHODE : soapCheckErrors
     * PARAMETRES : aucun
     * DESCRIPTION : Verifie si l'appel au Web Service retourne des erreurs (methode privee)
     * RETOUR : aucun
     *------------------------------------------------------------------------------------------------*/
    private function soapCheckErrors() {

        $this->soapError = $this->client->getError();

        if($this->soapError)
            die("API SENIORIALES ERROR : ".$this->soapError);
        else if ($this->client->fault)
            die("API SENIORIALES ERROR : ". $this->soapResult);
    }

    /*------------------------------------------------------------------------------------------------
     * METHODE : soapDoRequest
     * PARAMETRES : $method = la methode a appeler - $parameters = le tableau de parametres (optionel)
     * DESCRIPTION : Appel une methode du Web Service, documentations des WS disponibles ici -> http://manager.seniorales.com/api/soap
     * RETOUR : Renvoi le resultat de l'appel
     *------------------------------------------------------------------------------------------------*/
    public function soapDoRequest($method,$parameters=null) {
        $this->soapResult = $this->client->call($method, $parameters, "urn:seniorialeswsdl");

        $this->soapCheckErrors();

        return $this->soapResult;
    }

    /*------------------------------------------------------------------------------------------------
     * METHODE : userCheckLogin
     * PARAMETRES : backUrl = force une url de retour.
     * DESCRIPTION : Verifie la connexion de l'utilisateur ou si l'utilisateur est en session,
     * le cas echeant redirige sur l'authentification. A appeler systematiquement apres le constructeur
     * si l'utilisateur a besoin d'�tre authentifie sur la page concernee.
     * L'utilisateur est ensuite stocke dans le tableau associatif $authUser avec les cles suivantes : id,login,nom,prenom,mail,status,cle.
     * RETOUR : aucun
     *------------------------------------------------------------------------------------------------*/
    public function userCheckLogin($backUrl=null) {

        if(!is_null($backUrl))
            $backUrl = "&backUrl=".rawurlencode($backUrl);
        else
            $backUrl = "";

        //Recuperations des parametres GET initiaux si present
        $parametersString = "";
        if(count($_GET) > 0) {
            foreach($_GET as $key=>$value)
                $parametersString .= $key."=".$value."&";
            $parametersString = "&params=".urlencode(substr($parametersString, 0, strlen($parametersString) - 1));
        }

        @session_start();

        //Verification si parametre de login retourne dans l'URL
        if(isset($_GET['aspu']) && isset($_GET['aspuk']) && isset($_GET['aspak']) && isset($_GET['aspask'])) {
            $result = $this->soapDoRequest("authenticateUserByUrlSession", array("loginOrMail" => urldecode($_GET['aspu']), "userKey" => $_GET['aspuk'],"applicationKey" => $_GET['aspak'], "applicationSKey" => $_GET['aspask'], "sessionId" => session_id()));

            if(is_array($result)) {
                $this->authUser = $result;

                //Verification si acces a l'application
                $result = $this->soapDoRequest("checkUserApplicationAccess", array("userId" => $this->authUser['id'], "userKey" => $this->authUser['cle'],"applicationKey" => $_GET['aspak'], "applicationSKey" => $_GET['aspask']));

                if($result == false) {
                    $this->destroySession();
                    header("location:".$this->mainBaseUrl.$this->errorUrl."?aspask=".ApiSenioriales::$applicationKey."&code=ACCESSDENIED");
                    die();
                }

                //Verification status utilisateur
                if($this->authUser['status'] == 0) {
                    $this->destroySession();
                    header("location:".$this->mainBaseUrl.$this->errorUrl."?aspask=".ApiSenioriales::$applicationKey."&code=ACCOUNTDISABLED");
                    die();
                }

                $_SESSION['__AUTH_USER__'] = urlencode(serialize($this->authUser));
                $_SESSION['__AUTH_USER_APP_TOKEN__'] = $this->getUserApplicationToken();
                $this->userLog("login");
                
                //Requête de redirection
                $redirectUrl = (strlen($_SERVER['HTTPS']) == 0) ? "http://" : "https://";        
                $redirectUrl .= $_SERVER['SERVER_NAME'];
                
                //Sous-repertoire
                $subDirectory = explode("/",$_SERVER['PHP_SELF']);
                array_pop($subDirectory);
                
                if(count($subDirectory) > 0)
                    $subDirectory = implode("/",$subDirectory);
                else
                    $subDirectory = "";
                
                $redirectUrl .= $subDirectory."/";
                
                //paramètres
                $queryString = "";
                $arrQueryString = explode("&",$_SERVER['QUERY_STRING']);
                foreach($arrQueryString as $q) {
                    if(!is_string(strstr($q, "aspu=")) && !is_string(strstr($q, "aspuk=")) && !is_string(strstr($q, "aspak=")) && !is_string(strstr($q, "aspask="))) {
                        $queryString .= $q."&";
                    }
                }
                
                if(strlen($queryString) > 0)
                    $queryString = "?".substr ($queryString, 0, strlen($queryString) - 1);

                $redirectUrl .= $queryString;

                header("location:".$redirectUrl);
            }
            else {
                header("location:".$this->mainBaseUrl.$this->loginUrl."?sid=".session_id()."&aspak=".ApiSenioriales::$applicationKey.$parametersString.$backUrl);
                die();
            }
        }
        //Verification si en session
        else if(!isset($_SESSION['__AUTH_USER__'])) {
            header("location:".$this->mainBaseUrl.$this->loginUrl."?sid=".session_id()."&aspak=".ApiSenioriales::$applicationKey.$parametersString.$backUrl);
            die();
        }
        //Si en session controle TOKEN
        else {

            $this->authUser = unserialize(urldecode($_SESSION['__AUTH_USER__']));

            //Si on est en session mais qu'on a change d'application, on verifie les droits
            if($this->getUserApplicationToken() != $_SESSION['__AUTH_USER_APP_TOKEN__']) {

                $result = $this->soapDoRequest("checkUserApplicationAccess", array("userId" => $this->authUser['id'], "userKey" => $this->authUser['cle'],"applicationKey" => ApiSenioriales::$applicationKey, "applicationSKey" => md5($this->application['id'].ApiSenioriales::$secureKey)));

                if($result == false) {
                    $this->destroySession();
                    header("location:".$this->mainBaseUrl.$this->errorUrl."?aspask=".ApiSenioriales::$applicationKey."&code=ACCESSDENIED");
                    die();
                }

                $_SESSION['__AUTH_USER_APP_TOKEN__'] = $this->getUserApplicationToken();
            }
        }

        $browser = new Browser();
        $userInfo = $_SERVER['REQUEST_URI']."##".$browser->getPlatform()."##".$browser->getBrowser()."-".$browser->getVersion()."##".$_SERVER["REMOTE_ADDR"];
        $messageService = $this->soapDoRequest("ping", array("userId" => $this->authUser['id'], "userKey" => $this->authUser['cle'], "userInfo" => $userInfo, "applicationKey" => ApiSenioriales::$applicationKey));

        if(strlen(trim($messageService)) > 0)
            echo "<script type=\"text/javascript\">alert(\"".utf8_decode($messageService)."\");</script>";
    }

    /*------------------------------------------------------------------------------------------------
     * METHODE : userLog
     * PARAMETRES :  $type = le type de log - $value = la valeur du log (si non precise prend comme valeur les infos navigateurs & OS de l'internaute)
     * DESCRIPTION : Log une action utilisateur
     * RETOUR : aucun
     *------------------------------------------------------------------------------------------------*/
    public function userLog($type, $value="default") {
        if($value == "default") {
            $browser = new Browser();
            $value = $browser->getPlatform()."##".$browser->getBrowser()."-".$browser->getVersion()."##".$_SERVER["REMOTE_ADDR"];
        }

        $userId = (!is_null($this->authUser))?$this->authUser['id']:0;

        return $this->soapDoRequest("logUserAction", array("userId" => $userId, "applicationKey" => ApiSenioriales::$applicationKey,"type" => $type, "value" => $value));
    }

    /*------------------------------------------------------------------------------------------------
     * METHODE : getUserParameter
     * PARAMETRES :  $parameterName = le nom du parametre, $userId = l'id de l'utilisateur (si null sera egal a l'utlisateur connecte) - $userKey = la cle de l'utilisateur (si null sera egal a l'utlisateur connecte)
     * DESCRIPTION : Retourne un parametre de l'utlisateur pour cette application
     * RETOUR : La valeur du parametre
     *------------------------------------------------------------------------------------------------*/
    public function getUserParameter($parameterName, $userId=null, $userKey=null) {

        if(strlen(trim($parameterName)) == 0)
            return null;

        if(is_null($userId))
            $userId = $this->authUser['id'];

        if(is_null($userKey))
            $userKey = $this->authUser['cle'];

        return $this->soapDoRequest("getUserParameter", array("userId" => $userId, "userKey" => $userKey, "applicationKey" => ApiSenioriales::$applicationKey, "parameterName" => $parameterName));
    }

    /*------------------------------------------------------------------------------------------------
     * METHODE : getUserById
     * PARAMETRES :  $userId = l'id de l'utilisateur (si null sera egal a l'utlisateur connecte) - $userKey = la cle de l'utilisateur (si null sera egal a l'utlisateur connecte)
     * DESCRIPTION : Retourne l'utilisateur sous forme de tableau associatif avec les cles suivantes : id,login,nom,prenom,mail,status,cle.
     * RETOUR : l'utilisateur sous forme de tableau
     *------------------------------------------------------------------------------------------------*/
    public function getUserById($userId=null, $userKey=null) {

        if(is_null($userId))
            $userId = $this->authUser['id'];

        if(is_null($userKey))
            $userKey = $this->authUser['cle'];

        return $this->soapDoRequest("getUserById", array("userId" => $userId, "userKey" => $userKey));
    }

    /*------------------------------------------------------------------------------------------------
     * METHODE : initBatch
     * PARAMETRES :  $scriptName = le nom du script - $owner = la personne ayant planifie l'execution du script (optionnel) - $frequence = la frequence d'execution en secondes
     * DESCRIPTION : Declare un debut de batch
     * RETOUR : l'id du batch
     *------------------------------------------------------------------------------------------------*/
    public function initBatch($scriptName, $owner='inconnu', $frequence=0) {
        $return = $this->soapDoRequest("initBatch", array("scriptName" => $scriptName, "owner" => $owner, "frequence" => $frequence, "ip" => $_SERVER['REMOTE_ADDR']));

        if($return == 0)
            die();
        else
            return $return;
    }

    /*------------------------------------------------------------------------------------------------
     * METHODE : updateBatch
     * PARAMETRES :  $id = l'id du batch - $status = 0 si le batch est toujours en cours, 1 si terminee, 2 si erreur - $message = un message (optionnel)
     * DESCRIPTION : update le suivi d'un batch en cours d'execution
     * RETOUR : aucun
     *------------------------------------------------------------------------------------------------*/
    public function updateBatch($id, $status, $message='-') {
        $this->soapDoRequest("updateBatch", array("id" => $id, "status" => $status, "message" => $message));
    }

    /*------------------------------------------------------------------------------------------------
     * METHODE : sendMail
     * PARAMETRES :  $email = destinataire - $subject = le sujet du mail - $content = le contenu du mail
     * DESCRIPTION : Envoi un e-mail
     * RETOUR : 1 si l'envoi s'est bien deroule sinon false.
     *------------------------------------------------------------------------------------------------*/
    public function sendMail($email, $subject, $content, $copyEmail='', $replyToMail='',$replyToName='') {
        return $this->soapDoRequest("sendEMail", array("email" => $email, "subject" => $subject, "content" => $content, "copyEmail" => $copyEmail, "replyToMail" => $replyToMail, "replyToName" => $replyToName));
    }

    /*------------------------------------------------------------------------------------------------
     * METHODE : getCmsContentType
     * PARAMETRES :  aucun
     * DESCRIPTION : retourne un tableau contenant les types de contenu disponible pour cette application
     * RETOUR : un tableau associatif dont la cle = l'id du type contenu & la valeur = au libelle du type de contenu
     *------------------------------------------------------------------------------------------------*/
    public function getCmsContentsType() {
        return unserialize(urldecode($this->soapDoRequest("getCmsContentsType", array("applicationKey" => ApiSenioriales::$applicationKey))));
    }

    /*------------------------------------------------------------------------------------------------
     * METHODE : getCmsContent
     * PARAMETRES :  contentId = l'id du contenu, getMode = 'full' renvoi tout et incremente la lecture, getMode = 'list' = ne renvoi pas le contenu complet et n'incremente pas la lecture
     * DESCRIPTION : retourne un tableau contenant les types de contenu disponible pour cette application
     * RETOUR : un tableau associatif avec les cles suivantes : id, titre, contenu, tags, auteurId, auteurNom, dateMaj, utilisateurMajId, utilisateurMajNom, datePub, typeContenuId, typeContenuNom, epingle, nbLu
     *------------------------------------------------------------------------------------------------*/
    public function getCmsContent($contentId, $getMode = "full") {
        return $this->soapDoRequest("getCmsContent", array("contentId" => $contentId, "userId" => $this->authUser['id'], "getMode" => $getMode, "applicationKey" => ApiSenioriales::$applicationKey));
    }

    /*------------------------------------------------------------------------------------------------
     * METHODE : $filterSearchStringTitle = valeur si recherche sur le titre, $filterSearchStringContent = valeur si recherche dans le contenu, $filterSearchStringTags = valeur si recherche dans le tags, $filterOrderField = valeur si tri souhaite (titre-asc,titre-desc,datepub-asc,datepub-desc,datemaj-asc,datemaj-desc) , $filterTypeContentId = valeur si type contenu specifique, $forceEpingleFirst = permet d'afficher en premier les articles epingle
     * PARAMETRES :  aucun
     * DESCRIPTION : retourne les ids de contenus matchant les parametres
     * RETOUR : un tableau contenant les ids des contenus trouves
     *------------------------------------------------------------------------------------------------*/
    public function getCmsContentsList($filterSearchStringTitle="", $filterSearchStringContent="", $filterSearchStringTags="", $filterOrderField="", $filterTypeContentId="", $forceEpingleFirst="false") {
        return unserialize(urldecode($this->soapDoRequest("getCmsContentsList", array("filterSearchStringTitle" => $filterSearchStringTitle,"filterSearchStringContent" => $filterSearchStringContent,"filterSearchStringTags" => $filterSearchStringTags,"filterOrderField" => $filterOrderField,"filterTypeContentId" => $filterTypeContentId,"forceEpingleFirst" => $forceEpingleFirst,"applicationKey" => ApiSenioriales::$applicationKey))));
    }

    /*------------------------------------------------------------------------------------------------
     * METHODE : getCmsContentDistantUrl
     * PARAMETRES :  -
     * DESCRIPTION : -
     * RETOUR : -
     *------------------------------------------------------------------------------------------------*/
    public function getCmsContentDistantUrl($contentId) {
        return $this->mainBaseUrl."/content/".$contentId."-".md5(ApiSenioriales::$secureKey.$contentId).".html";
    }
    
    /*------------------------------------------------------------------------------------------------
     * METHODE : uploadFile
     * PARAMETRES :  nom réel du fichier avec extension, chemin serveur du fichier, flag de suppression de fichier
     * DESCRIPTION : pousse un fichier sur le serveur manager
     * RETOUR : clé du fichier à stocker localement, message erreur sinon
     *------------------------------------------------------------------------------------------------*/
    public function uploadFile($nomFichier, $cheminServeur, $logInfo = "", $deleteFichierLocal = false) {
        
        if(!is_file($cheminServeur))
            return "ERROR:fichier non trouvée sur le serveur";
        
        $extension  = explode(".",$nomFichier);

        if(count($extension) < 2)
            return "ERROR:le nom du fichier doit contenir son extension";
        
        $base64Fichier = base64_encode(file_get_contents($cheminServeur));
        
        if($deleteFichierLocal)
            @unlink($cheminServeur);
        
        $this->userLog("uploadFile");

        return $this->soapDoRequest("uploadFile", array("nomFichier" => $nomFichier, "base64Fichier" => $base64Fichier, "logInfo" => $logInfo, "applicationKey" => ApiSenioriales::$applicationKey));

    }
    
     /*------------------------------------------------------------------------------------------------
     * METHODE : getDownloadLinkFile
     * PARAMETRES :  clé fichier
     * DESCRIPTION : renvoit le lien de download du fichier
     * RETOUR : le lien
     *------------------------------------------------------------------------------------------------*/
    public function getDownloadLinkFile($cleFichier=null) {
        
        if(is_null($cleFichier) || (!is_null($cleFichier) && strlen(trim($cleFichier)) == 0))
            return "ERROR:la cle du fichier n'est pas renseignée.";

        return "https://manager.senioriales.com/d/".$cleFichier;

    }
    
    /*------------------------------------------------------------------------------------------------
     * METHODE : supprime un fichier sur le serveur manager
     * PARAMETRES :  clé fichier
     * DESCRIPTION : supprime un fichier
     * RETOUR : renvoit 0 ou 1 si la suppression s'est bien passé
     *------------------------------------------------------------------------------------------------*/
    public function deleteFile($cleFichier=null) {
        
        if(is_null($cleFichier) || (!is_null($cleFichier) && strlen(trim($cleFichier)) == 0))
            return "ERROR:la cle du fichier n'est pas renseignée.";
        
        $this->userLog("deleteFile");

        return $this->soapDoRequest("deleteFile", array("cleFichier" => $cleFichier, "applicationKey" => ApiSenioriales::$applicationKey));

    }

    /*------------------------------------------------------------------------------------------------
     * METHODE : envoi un fichier existant du serveur maanger
     * PARAMETRES :  clé fichier, email valide, sujet (optionnel), contenu du mail (optionnel)
     * DESCRIPTION : envoi un fichier
     * RETOUR : renvoit 0 ou 1 si la suppression s'est bien passé et un code d'erreur sinon
     *------------------------------------------------------------------------------------------------*/
    public function sendFileByMail($cleFichier=null, $email, $subject="Envoi de fichier Senioriales", $content="") { ///($cleFichier, $email, $subject, $content, $applicationKey) {

        if(is_null($cleFichier) || (!is_null($cleFichier) && strlen(trim($cleFichier)) == 0))
            return "ERROR:la cle du fichier n'est pas renseignée.";

        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
            return "ERROR:adresse e-mail invalide.";

        $this->userLog("sendFileByMail");

        return $this->soapDoRequest("sendFileByMail", array("cleFichier" => $cleFichier, "email" => $email, "subject" => $subject, "content" => $content, "applicationKey" => ApiSenioriales::$applicationKey));

    }

    /*------------------------------------------------------------------------------------------------
     * METHODE : logout
     * PARAMETRES : aucun
     * DESCRIPTION : Deconnexion l'utilisateur et le renvoi sur la page de deconnexion du S.I
     * RETOUR : aucun
     *------------------------------------------------------------------------------------------------*/
    public function logout() {
        $this->destroySession();
        
        @session_start();
        header("location:".$this->mainBaseUrl.$this->logoutUrl."?sid=".session_id()."&aspuk=".$this->authUser['cle']."&aspak=".ApiSenioriales::$applicationKey);
        die();
    }

    private function destroySession() {
        $_SESSION['APISENIORIALESSERVERBASE'] = null;
        $_SESSION['APISENIORIALESSERVERBASETMS'] = null;
        $_SESSION['__AUTH_USER__'] = null;
        $_SESSION['__AUTH_USER_APP_TOKEN__'] = null;
        unset($_SESSION['APISENIORIALESSERVERBASE']);
        unset($_SESSION['APISENIORIALESSERVERBASETMS']);
        unset($_SESSION['__AUTH_USER__']);
        unset($_SESSION['__AUTH_USER_APP_TOKEN__']);
        session_destroy();
        session_write_close();
    }
}

/*
 * EXEMPLES d'instantiation
 * include("<CHEMIN_DISQUE>/ApiSenioriales/ApiSenioriales.class.php");
 *
 * $apiSenioriales = new ApiSenioriales();
 * $apiSenioriales->userCheckLogin();
 *
*/

?>