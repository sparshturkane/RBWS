<?php
header('Access-Control-Allow-Origin: *');
defined('BASEPATH') OR exit('No direct script access allowed');

class Apitest extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('api_model', 'api');
        //$this->load->library('Encryptdecrypt');
        $this->load->helper('string');// appsturf changes
        date_default_timezone_set('Asia/Calcutta');
        $this->load->database();
        $this->load->helper('url');
        $this->load->library('session');

    }

    // public function logintest(){
    //     $this->api->isUserVerified('99');
    // }
    public function processlogin() {
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $db=$this->db->conn_id;

            $arrResponse = array();

            $userName = isset($_POST['userName']) ? mysqli_real_escape_string($db,$_POST['userName']) : '';
            $password = isset($_POST['password']) ? mysqli_real_escape_string($db,$_POST['password']) : '';
            $androidKey = isset($_POST['androidKey']) ? mysqli_real_escape_string($db,$_POST['androidKey']) : '';
            $iosKey = isset($_POST['iosKey']) ? mysqli_real_escape_string($db,$_POST['iosKey']) : '';
                // $getAccessToken = $this->api->getAccessToken($userID);
                // $accessToken = $getAccessToken['accessToken'];
                // header("accessToken: $accessToken");

                //$enc_password = $this->encryptdecrypt->crypt($password);
            if ($userName != NULL && $password != NULL) {
                    //$response = $this->api->loginnew($userName, $password);
                $response = $this->api->loginNewTest($userName, $password);
                $userID = $response['userID'];
                //checking if user active
                $isUserActive = $this->api->isUserActive($userID);
                if($isUserActive){
                    $userID = $isUserActive; //if user isActive == 0 then update userID with newID in Current Variable
                }

                
                if ($userID) {
                        //check email confirmation 
                    if($this->api->isUserVerified($userID)){

                            //$userID = $response['userID'];
                        if(!empty($androidKey)){
                            $updateData['androidKey']=$androidKey;
                        }

                        if(!empty($iosKey)){
                            $updateData['iosKey']=$iosKey;
                        }
                            // $updateData['androidKey']=$androidKey;
                            // $updateData['iosKey']=$iosKey;
                        $updateData['lastLogin'] = date("Y-m-d H:i:s");

                        if(!empty($updateData)){
                            $success = $this->api->updateUser($userID,$updateData);
                        }


                        $getAccessToken = $this->api->getAccessToken($userID);
                        $accessToken = $getAccessToken['accessToken'];
                        header("accesstoken: $accessToken");

                        $userProfileData=$this->api->getUserData2($userID);
                        $socialLoginData=$this->api->getSocialLoginData($userID);
                        $getCategoryList = $this->api->getCategoryListUserData($userID);



                            // if($socialLoginData){
                            //     $updateLastLogin = $this->api->updateLastLogin($userID);
                            // }


                        $arrResponse = array('status' => 1, 'message' => 'User Login Successfull!','userData'=>$userProfileData,'interest'=>$getCategoryList);
                    }else{
                        $arrResponse = array('status' => 0, 'message' => 'Please confirm your Email address.');
                    }

                } else {
                    $arrResponse = array('status' => 0, 'message' => 'Invalid username or password.');
                }

            } else {    
                $arrResponse = array('status' => 0, 'message' => 'Username and Password are mandatory.');
            }

        } else {
            $arrResponse = array("status" => 0, "message" => "Request method not accepted");
        } 
        echo json_encode($arrResponse);
    }

    public function socialRegistration(){
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $db=$this->db->conn_id;
            $userID = isset($_POST['userID']) ? mysqli_real_escape_string($db,$_POST['userID']) : '';
            $fullName = isset($_POST['fullName']) ? mysqli_real_escape_string($db,$_POST['fullName']) : '';
            $socialID = isset($_POST['socialID']) ? mysqli_real_escape_string($db,$_POST['socialID']) : '';
            $socialType = isset($_POST['socialType']) ? mysqli_real_escape_string($db,$_POST['socialType']) : '';
            $dob = isset($_POST['dob']) ? mysqli_real_escape_string($db,$_POST['dob']) : '';
            $profilePic = isset($_POST['profilePic']) ? mysqli_real_escape_string($db,$_POST['profilePic']) : '';
            $gender = isset($_POST['gender']) ? mysqli_real_escape_string($db,$_POST['gender']) : '';
            $phoneNo = isset($_POST['phoneNo']) ? mysqli_real_escape_string($db,$_POST['phoneNo']) : '';
            $email = isset($_POST['email']) ? mysqli_real_escape_string($db,$_POST['email']) : '';
            $androidKey = isset($_POST['androidKey']) ? mysqli_real_escape_string($db,$_POST['androidKey']) : '';
            $iosKey = isset($_POST['iosKey']) ? mysqli_real_escape_string($db,$_POST['iosKey']) : '';
            $twitterHandler = isset($_POST['twitterHandler']) ? mysqli_real_escape_string($db,$_POST['twitterHandler']) : '';

            if($socialID != NULL && $socialType !=NULL){
                if($userID!=''){
                    //$arrResponse = array("status"=>0,"message"=>"sync");

                    $arrResponse = $this->socialSync($userID,$fullName,$socialID,$socialType,$dob,$profilePic,$gender,$phoneNo,$androidKey,$iosKey);
                } else {
                    //$arrResponse = array("status"=>0,"message"=>"register/login");
                    //$try = '123456';
                    $isSocialIDExists = $this->api->ifSocialIDExist($socialID);
                    if(!empty($isSocialIDExists)){
                        //$arrResponse = array("status"=>0,"message"=>"login");//after register in sync response give sync account type and id 
                        // Get userID from socialID
                        //$getUserID = $this->api->ifSocialIDExist($socialID);
                        $userID = $isSocialIDExists['userID'];
                        //$randomString = random_string('unique');
                        //$this->api->updateAccessToken($randomString,$userID);
                        if($dob!=''){
                            $dob=date('Y-m-d',strtotime($dob));
                        } else {
                            $dob = '';
                        }

                        $updateData = array();

                        $checkFullName = $this->api->checkIfFullNameExists($userID);
                        if($checkFullName){
                            if(!empty($fullName)){
                                $updateData['fullName']=$fullName;
                            }
                        }

                        $checkDob = $this->api->checkIfDobExists($userID);
                        if($checkDob){
                            if(!empty($dob)){
                                $updateData['dob']=$dob;
                            }
                        }

                        $checkProfilePic = $this->api->checkIfProfilePicExists($userID);
                        if($checkProfilePic){
                            if(!empty($profilePic)){
                                $updateData['profilePic']=$profilePic;
                            }
                        }

                        $checkGender = $this->api->checkIfGenderExists($userID);
                        if($checkGender){
                            if(!empty($gender)){
                                $updateData['gender']=$gender;
                            }
                        }

                        $checkPhoneNo = $this->api->checkIfPhoneNoExists($userID);
                        if($checkPhoneNo){
                            if(!empty($phoneNo)){
                                $updateData['phoneNo']=$phoneNo;
                            }
                        }
                        
                        if(!empty($twitterHandler)){
                            $updateData['twitterHandler']=$twitterHandler;
                        }
                        // if(!empty($fullName)){
                        //     $updateData['fullName']=$fullName;
                        // }

                        // if(!empty($dob)){
                        //     $updateData['dob']=$dob;
                        // }

                        // if(!empty($profilePic)){
                        //     $updateData['profilePic']=$profilePic;
                        // }

                        // if(!empty($gender)){
                        //     $updateData['gender']=$gender;
                        // }

                        // if(!empty($phoneNo)){
                        //     $updateData['phoneNo']=$phoneNo;
                        // }

                        // if(!empty($email)){
                        //     $updateData['email']=$email;
                        // }

                        $checkIsEmailFieldEmpty = $this->api->checkIsEmailFieldEmpty($userID);
                        if($checkIsEmailFieldEmpty){
                            // strlen($email);
                            if(!empty($email)){
                                $updateData['email']=$email;
                            }    
                        }

                        // if(!empty($androidKey)){
                        //     $updateData['androidKey']=$androidKey;
                        // }

                        // if(!empty($iosKey)){
                        //     $updateData['iosKey']=$iosKey;
                        // }

                        // $updateData['androidKey']=$androidKey;
                        // $updateData['iosKey']=$iosKey;

                        // $updateData = array(
                        //     'fullName' => $fullName,
                        //     'dob'=> $dob,
                        //     'profilePic'=> $profilePic,
                        //     'gender'=> $gender,
                        //     'phoneNo'=> $phoneNo
                        //     );
                        if(!empty($updateData)){
                            $success = $this->api->updateUser($userID,$updateData);
                        }

                        $getAccessToken = $this->api->getAccessToken($userID);
                        $accessToken = $getAccessToken['accessToken'];
                        header("accesstoken: $accessToken");

                        $userProfileData=$this->api->getUserData2($userID);
                        $socialLoginData=$this->api->getSocialLoginData($userID);
                        $getCategoryList = $this->api->getCategoryListUserData($userID);
                        if($userProfileData){
                            $updateLastLogin = $this->api->updateLastLogin($userID);
                        }
                        $userProfileData["newUser"] = "0";
                        //print_r($userProfileData);exit;
                        $arrResponse = array('status' => 1, 'message' => 'User Login Successfull!','userData'=>$userProfileData,'interest'=>$getCategoryList);
                    } else {
                        //$arrResponse = array("status"=>0,"message"=>"Register");//after login in sync response give sync account type and id 
                        $randomString = random_string('unique');
                        if($dob!=''){
                            $dob=date('Y-m-d',strtotime($dob));
                        } else {
                            $dob = '';
                        }

                        $insertDataUserProfile = array(
                            'fullName' => $fullName,
                            'dob' => $dob,
                            'profilePic'=> $profilePic,
                            'gender'=> $gender,
                            'phoneNo'=> $phoneNo,
                            'email'=> $email,
                            'androidKey'=> $androidKey,
                            'iosKey'=> $iosKey,
                            'isActive'=>'1',
                            'twitterHandler'=>$twitterHandler,
                            'created'=> date('Y-m-d H:i:s')

                            );

                        $success = $this->api->registeruser($insertDataUserProfile);
                        $userid = $this->db->insert_id();
                        if ($success) {
                            //new Addition By Mandar
                             if(!empty($userid)){
                                $updateData['newID']=$userid;
                                }
                        
                                if(!empty($updateData)){
                                $success = $this->api->updateUser($userid,$updateData);
                                }

                            $insertDataSocialLogin = array(
                                'userID' =>$userid,
                                'socialID' => $socialID,
                                'socialType' => $socialType,
                                'created'=> date('Y-m-d H:i:s')
                                );

                            $successSocialLogin = $this->api->insertSocialLogin($insertDataSocialLogin);
                            if(!empty($successSocialLogin)){
                                $this->api->updateAccessToken($randomString,$userid);
                                header("accesstoken: $randomString");

                                //adding ROCKA PICK categoryID = 1 by default
                                //$this->api->insertUserInterest($userid,1,1);
                                $insertUserInterest = $this->api->insertUserInterest($userid,1,1);

                                $userProfileData=$this->api->getUserData2($userid);
                                $socialLoginData=$this->api->getSocialLoginData($userid);
                                $getCategoryList = $this->api->getCategoryListUserData($userid);
                                if($userProfileData){
                                    $updateLastLogin = $this->api->updateLastLogin($userid);
                                }
                                $userProfileData["newUser"] = "1";
                                $arrResponse = array('status' => 1, 'message' => 'User registered successfully!','userData'=>$userProfileData,'interest'=>$getCategoryList);
                            }else{
                                $arrResponse = array('status' => 0, 'message' => 'Insert data failed.');
                            }

                        } else {
                            $arrResponse = array('status' => 0, 'message' => 'Insert data failed.');
                        }
                    }
                }
            } else {
                $arrResponse = array("status"=>0,"message"=>"SocialID and SocialType are Required fields.");
            }

        } else {
            $arrResponse = array("status"=>0,"message"=>"Request Method not accepted");
        }
        echo json_encode($arrResponse);
    }

    private function socialSync($userID,$fullName,$socialID,$socialType,$dob,$profilePic,$gender,$phoneNo,$androidKey,$iosKey){
        //$arrResponse = array("status"=>0,"message"=>"sync function");
        //return $arrResponse;
        //check if socialID exists and userID provided is different
        $checkSocialIDExist = $this->api->ifSocialIDExist($socialID);
        if(!empty($checkSocialIDExist)){
            //make entry in socialLogin table
            //update user table for the data you get 
            //return arrResponse with sync data

            if($checkSocialIDExist['userID']!=$userID){
                //$arrResponse = array("status"=>0,"message"=>"a. b. c.");

                //a. replace old userID with new one in all Tables
                //b. update `userProfile` & do isActive = 0 where userID =olduserID
                //c. echo response with userData and SyncData
                $oldUserID = $checkSocialIDExist['userID'];
                $newUserID = $userID;
                $arrResponse = $this->socialSyncUpdateUserID($oldUserID,$newUserID,$fullName,$socialID,$socialType,$dob,$profilePic,$gender,$phoneNo,$androidKey,$iosKey);

            } else {
                //if they are same then
                $userProfileData=$this->api->getUserData2($userID);
                $socialLoginData=$this->api->getSocialLoginData($userID);
                $getCategoryList = $this->api->getCategoryListUserData($userID);

                $getAccessToken = $this->api->getAccessToken($userID);
                $accessToken = $getAccessToken['accessToken'];
                header("accesstoken: $accessToken");
                
                $arrResponse = array("status"=>1,"message"=>"Sync Successfull",'userData'=>$userProfileData,'interest'=>$getCategoryList);
            }
            
            return $arrResponse;

            // if (!=different){
            //     //response user is already synced sync array
            // } else {
            //     //see page a. b. c.
            // }
        } else {

            //$arrResponse = array("status"=>0,"message"=>"socialID don't exists ");
            //we have to make an entry of that socialid with perticular userID
            //if data in userProfile is empty then update it else do nothing
            $checkFullName = $this->api->checkIfFullNameExists($userID);
            if($checkFullName){
                $this->api->updateFullName($fullName,$userID);
            }

            $checkDob = $this->api->checkIfDobExists($userID);
            if($checkDob){
                $this->api->updateDob($dob,$userID);
            }

            $checkProfilePic = $this->api->checkIfProfilePicExists($userID);
            if($checkProfilePic){
                $this->api->updateProfilePic($profilePic,$userID);
            }

            $checkGender = $this->api->checkIfGenderExists($userID);
            if($checkGender){
                $this->api->updateGender($gender,$userID);
            }

            $checkPhoneNo = $this->api->checkIfPhoneNoExists($userID);
            if($checkPhoneNo){
                $this->api->updatePhoneNo($phoneNo,$userID);
            }

            $checkAndroidKey = $this->api->checkIfAndroidKeyExists($userID);
            if($checkAndroidKey){
                $this->api->updateAndroidKey($androidKey,$userID);
            }

            $checkIosKey = $this->api->checkIfIosExists($userID);
            if($checkIosKey){
                $this->api->updateIosKey($iosKey,$userID);
            }            

            $insertDataSocialLogin = array(
                'userID' => $userID,
                'socialID' => $socialID,
                'socialType' => $socialType,
                'created' => date('Y-m-d H:i:s')
                );

            $successSocialLogin = $this->api->insertSocialLogin($insertDataSocialLogin);
            if(!empty($successSocialLogin)){
                // $this->api->updateAccessToken($randomString,$userID);
                // header("accessToken: $randomString");
                $userProfileData=$this->api->getUserData2($userID);
                $socialLoginData=$this->api->getSocialLoginData($userID);
                $getCategoryList = $this->api->getCategoryListUserData($userID);

                $getAccessToken = $this->api->getAccessToken($userID);
                $accessToken = $getAccessToken['accessToken'];
                header("accesstoken: $accessToken");

                //$userData=$this->api->getUserData($userID);
                $arrResponse = array('status' => 1, 'message' => 'Sync Successfull','userData'=>$userProfileData,'interest'=>$getCategoryList);
            }else{
                $arrResponse = array('status' => 0, 'message' => 'Insert data failed.');
            }       
            return $arrResponse;

        }

        //api_model abc
        // $query= "SELECT userID FROM `socialLogin` WHERE socialID=$socialID";
        // if(num_rows()>=0){
        //     return $query;
        // } else {
        //     return false;
        // }
        //api_model abc
    } 

    public function socialSyncUpdateUserID($oldUserID,$newUserID,$fullName,$socialID,$socialType,$dob,$profilePic,$gender,$phoneNo,$androidKey,$iosKey){

        //a. replace old userID with new one in all Tables
        //b. update `userProfile` & do isActive = 0 where userID =olduserID
        //c. echo response with userData and SyncData

        $result = $this->api->updateUserIDAll($oldUserID,$newUserID);

        $arrResponse = $this->socialSync($newUserID,$fullName,$socialID,$socialType,$dob,$profilePic,$gender,$phoneNo,$androidKey,$iosKey);

        return $arrResponse;

    }

    public function registerInitial() {
        $arrResponse = array();
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $db=$this->db->conn_id;
            $fullName = isset($_POST['name']) ? mysqli_real_escape_string($db,$_POST['name']) : '';
            $email = isset($_POST['email']) ? mysqli_real_escape_string($db,$_POST['email']) : '';
            $userName = isset($_POST['userName']) ? mysqli_real_escape_string($db,$_POST['userName']) : '';



            if($fullName!=NULL && $email!=NULL && $userName!=NULL){
                $email = strtolower($email);
                $is_email_exist = $this->api->is_email_exist($email);
                $is_userName_exist = $this->api->is_userName_exist($userName);
                if ($is_email_exist) {
                    $arrResponse = array('status' => 0, 'message' => 'User with this email already exists.');
                } else if ($is_userName_exist){
                    $arrResponse = array('status' => 0, 'message' => 'User with this username already exists.');                    
                }else{
                    //main things will happen here
                    //$hashPassword = password_hash($password, PASSWORD_DEFAULT);

                    $insertData = array(
                        'fullName' => $fullName,
                        'userName' => $userName,
                        'email' => $email,
                        'created'=> date('Y-m-d H:i:s'),
                        'isActive'=> '1'
                        );
                    $success = $this->api->registeruser($insertData);
                    $userid = $this->db->insert_id();


                    if ($success) {
                        //addition By Mandar
                        //new Addition By Mandar
                             if(!empty($userid)){
                                $updateData['newID']=$userid;
                                }
                        
                                if(!empty($updateData)){
                                $success = $this->api->updateUser($userid,$updateData);
                                }
                        //$isAccessTokenExists = $this->api->isAccessTokenExists($accessToken);
                            // do {
                            //     $randomString = random_string('unique');
                            // } while ($this->api->isAccessTokenExists($randomString));

                        $randomString = random_string('unique');
                        $this->api->updateAccessToken($randomString,$userid);
                        header("accesstoken: $randomString");

                        //$userProfileData=$this->api->getUserData2($userid);
                        //$socialLoginData=$this->api->getSocialLoginData($userid);

                        $arrResponse = array('status' => 1, 'message' => 'User registered successfully Initial', 'userID' => $userid);
                    } else {
                        $arrResponse = array('status' => 0, 'message' => 'Insert data failed.');
                    }
                }
            }else{
                $arrResponse = array("status" => 0, "message" => "name, email and userName are required.");
            }
        }else{
            $arrResponse = array("status" => 0, "message" => "Request Method Not accepted");
        }
        echo json_encode($arrResponse);
    }

    public function registerFinal() {
        $arrResponse = array();
        if($_SERVER['REQUEST_METHOD'] == "POST"){

            $db=$this->db->conn_id;
            $userID = isset($_POST['userID']) ? mysqli_real_escape_string($db,$_POST['userID']) : '';
            $password = isset($_POST['password']) ? mysqli_real_escape_string($db,$_POST['password']) : '';
            $occupation = isset($_POST['occupation']) ? mysqli_real_escape_string($db,$_POST['occupation']) : '';
            $phoneNo = isset($_POST['phoneNo']) ? mysqli_real_escape_string($db,$_POST['phoneNo']) : '';
            $aboutMe = isset($_POST['aboutMe']) ? mysqli_real_escape_string($db,$_POST['aboutMe']) : '';
            $gender = isset($_POST['gender']) ? mysqli_real_escape_string($db,$_POST['gender']) : '';
            $dob = isset($_POST['dob']) ? mysqli_real_escape_string($db,$_POST['dob']) : '';
            $country = isset($_POST['country']) ? mysqli_real_escape_string($db,$_POST['country']) : '';
            $state = isset($_POST['state']) ? mysqli_real_escape_string($db,$_POST['state']) : '';
            $androidKey = isset($_POST['androidKey']) ? mysqli_real_escape_string($db,$_POST['androidKey']) : '';
            $iosKey = isset($_POST['iosKey']) ? mysqli_real_escape_string($db,$_POST['iosKey']) : '';

            if($password != NULL && $phoneNo != NULL && $userID != NULL){// checking for required fields
                $accessToken = 0;
                $isUserExist = $this->api->isUserExist($userID);
                if($isUserExist){
                    //$arrResponse = array('status'=>1, 'message'=>'accessToken is Valid','getAccessToken'=>$getAccessToken,'accessToken'=>$accessToken);
                    $headers = apache_request_headers();
                    foreach ($headers as $key => $value) {
                        switch ($key) {
                            case 'accesstoken':
                            $accessToken = $value;
                            break;
                        }
                    }
                    $getAccessToken = $this->api->getAccessToken($userID);
                    $getAccessToken = $getAccessToken['accessToken'];
                    if(strcmp($getAccessToken,$accessToken)==0){// chcking if accesstoken is valid
                        $is_mobile_no_exist = $this->api->is_mobile_no_exist($phoneNo);
                        if($is_mobile_no_exist!=TRUE){
                            if($dob!=''){
                                $dob = date('Y-m-d',strtotime($dob));
                            }
                            $hashPassword = password_hash($password, PASSWORD_DEFAULT);
                            $updateData = array(
                                'password' => $hashPassword,
                                'occupation'=> $occupation,
                                'phoneNo'=> $phoneNo,
                                'aboutMe'=> $aboutMe,
                                'gender'=> $gender,
                                'dob'=> $dob,
                                'country'=> $country,
                                'state'=> $state,
                                'androidKey'=> $androidKey,
                                'iosKey'=> $iosKey,
                                'created'=> date('Y-m-d H:i:s'),
                                'isActive'=> '1'
                                );
                            $success = $this->api->updateUser($userID,$updateData);
                            if($success){

                                $userProfileData=$this->api->getUserData2($userID);
                                $socialLoginData=$this->api->getSocialLoginData($userID);
                                $getCategoryList = $this->api->getCategoryListUserData($userID);

                                $arrResponse = array('status' => 1, 'message' => 'user registered successfully Final.','userData'=>$userProfileData,'interest'=>$getCategoryList);

                            }else{
                                $arrResponse = array('status' => 0, 'message' => 'Insert data failed.');
                            }
                        }else{
                            $arrResponse = array("status" => 0, "message" => "User with this mobile number already exists.");
                        } 
                    }else{
                        $arrResponse = array('status'=>0, 'message'=>'Some fields are missing');
                    } 
                }else{
                    $arrResponse = array('status'=>0, 'message'=>'Error : Invalid userID '); 
                }
                
            }else{
                $arrResponse = array('status'=>0, 'message'=>'userID, password and phoneNo are required.');
            }
        }else{
            $arrResponse = array('status'=>0, 'message'=>'requested method is not accepted');
        }
        echo json_encode($arrResponse);
    }

    public function registerUser() {
        $arrResponse = array();
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $db=$this->db->conn_id;
            $fullName = isset($_POST['name']) ? mysqli_real_escape_string($db,$_POST['name']) : '';
            $email = isset($_POST['email']) ? mysqli_real_escape_string($db,$_POST['email']) : '';
            $userName = isset($_POST['userName']) ? mysqli_real_escape_string($db,$_POST['userName']) : '';
            $password = isset($_POST['password']) ? mysqli_real_escape_string($db,$_POST['password']) : '';



            if($fullName!=NULL && $email!=NULL && $userName!=NULL && $password!=NULL){
                $email = strtolower($email);
                $is_email_exist = $this->api->is_email_exist($email);
                $is_userName_exist = $this->api->is_userName_exist($userName);
                if ($is_email_exist) {
                    $arrResponse = array('status' => 0, 'message' => 'User with this email already exists.');
                } else if ($is_userName_exist){
                    $arrResponse = array('status' => 0, 'message' => 'User with this username already exists.');                    
                }else{
                    //main things will happen here
                    //$hashPassword = password_hash($password, PASSWORD_DEFAULT);
                    $hashPassword = password_hash($password, PASSWORD_DEFAULT);
                    $insertData = array(
                        'password' => $hashPassword,
                        'fullName' => $fullName,
                        'userName' => $userName,
                        'email' => $email,
                        'created'=> date('Y-m-d H:i:s'),
                        'dob' => '0000-00-00',
                        'isActive'=> '1'
                        );
                    $success = $this->api->registeruser($insertData);
                    $userid = $this->db->insert_id();


                    if ($success) {
                        //addition By Mandar
                        //new Addition By Mandar

                        $validationLink = random_string('alnum', 64).date('Y-m-d H:i:s').$userid;
                        $md5ValidationLink = md5($validationLink);

                        if(!empty($userid)){
                            $updateData['newID']=$userid;
                            $updateData['validationLink'] = $md5ValidationLink;

                            //sending validationLink through email

                            //email subject
                            $emailSubject = "Welcome to Rockabyte";
                            //email body
                            $validationConfirmationLink = site_url('api/confirmRegistration/').$md5ValidationLink;
                            // $emailBody = '<p>Thanks for joining Rockabyte! You now have access to hundreds of thousands of videos&nbsp;and can interact with friends.</p>
                            //     <p>Please confirm your email by clicking on this <span class="il"> link: </span><a href="'.$validationConfirmationLink.'">'.$validationConfirmationLink.'</a> <br />';


                            $emailBody = '<div class="holder" style="width:500px;height: 100%;left: 0;right: 0;
                        color: white;margin: auto;top: 0;bottom: 0;background-color: #008780;">
                                <div class="logoHolder" style="position: relative;font-family: Arial,sans-serif;">
                                    <img src="http://faarbetterfilms.com/rockabyteServicesV2/assets/images/logowhite.png" class="logoClass" style="width: 80px;position: relative;">
                                    <div class="textOnImg" style="position: relative;color: white;top: 0px;left: -3px;
                                    font-size: 10px;letter-spacing: 2px;width: 80px;text-align: center;">
                                        <span> <strong>ROCKA</strong><br> BYTE</span>
                                    </div>
                                
                                </div>
                                <div class="content" style="padding: 10px;text-align: center;padding-bottom: 50px;width:280px;left:0;right:0;margin:auto;">
                                    <h4 style="font-family:Arial,sans-serif;font-size: 16px;font-weight: 400;margin: 10px 0;color:white !important;">Thanks for joining Rockabyte! You now have access to hundreds of thousands of videos&nbsp;and can interact with friends. Please confirm your email by clicking on this link:</h4>
                                    <br>
                                    <a href="'.$validationConfirmationLink.'" style="font-size: 12px !important; font-family: Arial,sans-serif;font-weight: 400;word-break: break-all;color: #0000ee;" target="_blank">'.$validationConfirmationLink.'</a>
                                    <br>
                                </div>
                                <div class="footerHolder" style="text-align: center;padding:20px;width:260px;left:0;right:0;margin:auto;">
                                    <span class="rightReserved" style="color: #aaa7a7;font-size: 10px;font-family: Arial,sans-serif;">  
                                        &copy; 2017 RockaByte. All right reserved.
                                    </span>
                                    <br>
                                    <span class="notReply" style="font-size: 10px;font-family: Arial,sans-serif;color:white !important;">Please do not reply to this email as it is a computer generated mail.</span>
                                </div>
                            </div>';


                            $this->api->sendEmail($email, $emailSubject, $emailBody);

                        }
                        
                        if(!empty($updateData)){
                            $success = $this->api->updateUser($userid,$updateData);
                        }
                            //$isAccessTokenExists = $this->api->isAccessTokenExists($accessToken);
                            // do {
                            //     $randomString = random_string('unique');
                            // } while ($this->api->isAccessTokenExists($randomString));

                        $randomString = random_string('unique');
                        $this->api->updateAccessToken($randomString,$userid);
                        header("accesstoken: $randomString");

                        //$userProfileData=$this->api->getUserData2($userid);
                        //$socialLoginData=$this->api->getSocialLoginData($userid);

                        //adding ROCKA PICK categoryID = 1 by default
                        //$this->api->insertUserInterest($userid,1,1);
                        $insertUserInterest = $this->api->insertUserInterest($userid,1,1);

                        $userProfileData=$this->api->getUserData2($userid);
                        $socialLoginData=$this->api->getSocialLoginData($userid);
                        $getCategoryList = $this->api->getCategoryListUserData($userid);

                        $arrResponse = array('status' => 1, 'message' => 'user registered successfully.','userData'=>$userProfileData,'interest'=>$getCategoryList);


                        // $arrResponse = array('status' => 1, 'message' => 'User registered successfully', 'userID' => $userid);
                    } else {
                        $arrResponse = array('status' => 0, 'message' => 'Insert data failed.');
                    }
                }
            }else{
                $arrResponse = array("status" => 0, "message" => "name, email and userName are required.");
            }
        }else{
            $arrResponse = array("status" => 0, "message" => "Request Method Not accepted");
        }
        echo json_encode($arrResponse);
    }


    public function confirmRegistration($urlvalue='1'){
        //echo $urlvalue; exit;
        $this->load->helper('form');
        $userdata = $this->api->emailValidationLink($urlvalue);
        if($urlvalue!='1'){
            if($userdata){
                //do something with validation link 
                $confirmationLinkUserID = $userdata[0]['userID'];
                $updateUserData = array('isVerified'=>'1');
                $this->api->updateUser($confirmationLinkUserID,$updateUserData);
                $this->load->view('userVerifiedSuccessfully');
            }else{
                echo "<h2>Invalid Email Validation request</h2>";
            }
        }else{
            echo "<h2>Invalid Email Validation request</h2>";
        }
    }


    // public function enterNewPassword($urlvalue){
    //     $this->load->helper('form');
    //     $userdata = $this->api->forgotPasswordUserID($urlvalue);
    //     if($userdata){
    //         $this->load->view('enterNewPassword',['userData'=>$userdata]);
    //     }else{
    //         echo "<h2>Invalid password Reset request</h2>";
    //     }
    //     //$this->load->view('');
    // }

    // public function registerUser() {

    //     $arrResponse = array();
    //     if($_SERVER['REQUEST_METHOD'] == "POST"){
    //         $db=$this->db->conn_id;
    //         $firstName = isset($_POST['firstName']) ? mysqli_real_escape_string($db,$_POST['firstName']) : '';
    //         $lastName = isset($_POST['lastName']) ? mysqli_real_escape_string($db,$_POST['lastName']) : '';
    //         $userName = isset($_POST['userName']) ? mysqli_real_escape_string($db,$_POST['userName']) : '';
    //         $email = isset($_POST['email']) ? mysqli_real_escape_string($db,$_POST['email']) : '';
    //         $password = isset($_POST['password']) ? mysqli_real_escape_string($db,$_POST['password']) : '';
    //         $occupation = isset($_POST['occupation']) ? mysqli_real_escape_string($db,$_POST['occupation']) : '';
    //         $phoneNo = isset($_POST['phoneNo']) ? mysqli_real_escape_string($db,$_POST['phoneNo']) : '';
    //         $aboutMe = isset($_POST['aboutMe']) ? mysqli_real_escape_string($db,$_POST['aboutMe']) : '';
    //         $gender = isset($_POST['gender']) ? mysqli_real_escape_string($db,$_POST['gender']) : '';
    //         $dob = isset($_POST['dob']) ? mysqli_real_escape_string($db,$_POST['dob']) : '';
    //         $country = isset($_POST['country']) ? mysqli_real_escape_string($db,$_POST['country']) : '';
    //         $state = isset($_POST['state']) ? mysqli_real_escape_string($db,$_POST['state']) : '';
    //         $androidKey = isset($_POST['androidKey']) ? mysqli_real_escape_string($db,$_POST['androidKey']) : '';
    //         $iosKey = isset($_POST['iosKey']) ? mysqli_real_escape_string($db,$_POST['iosKey']) : '';

    //         $fullName = $firstName.' '.$lastName;
    //         if($dob!=''){
    //             $dob = date('Y-m-d',strtotime($dob));
    //         }
            
    //         if($email != NULL && $userName != NULL && $password !=NULL) {
    //             $is_email_exist = $this->api->is_email_exist($email);

    //             $is_userName_exist = $this->api->is_userName_exist($userName);
    //             $is_mobile_no_exist = 0;
    //             if($phoneNo!='') {
    //                 $is_mobile_no_exist = $this->api->is_mobile_no_exist($phoneNo);
    //             }

    //             if ($is_email_exist) {
    //                 $arrResponse = array('status' => 0, 'message' => 'User with this email already exists.');
    //             } else if ($is_mobile_no_exist) {
    //                 $arrResponse = array('status' => 0, 'message' => 'User with this mobile number already exists.');
    //             } else if ($is_userName_exist){
    //                 $arrResponse = array('status' => 0, 'message' => 'User with this username already exists.');                    
    //             } else {

    //                 $hashPassword = password_hash($password, PASSWORD_DEFAULT);

    //                 $insertData = array(
    //                     'fullName' => $fullName,
    //                     'userName' => $userName,
    //                     'email' => $email,
    //                     'password' => $hashPassword,
    //                     'occupation'=> $occupation,
    //                     'phoneNo'=> $phoneNo,
    //                     'aboutMe'=> $aboutMe,
    //                     'gender'=> $gender,
    //                     'dob'=> $dob,
    //                     'country'=> $country,
    //                     'state'=> $state,
    //                     'androidKey'=> $androidKey,
    //                     'iosKey'=> $iosKey,
    //                     'created'=> date('Y-m-d H:i:s'),
    //                     'isActive'=> '1'
    //                     );
    //                 $success = $this->api->registeruser($insertData);
    //                 $userid = $this->db->insert_id();


    //                 if ($success) {

    //                     //$isAccessTokenExists = $this->api->isAccessTokenExists($accessToken);
    //                         // do {
    //                         //     $randomString = random_string('unique');
    //                         // } while ($this->api->isAccessTokenExists($randomString));

    //                     $randomString = random_string('unique');
    //                     $this->api->updateAccessToken($randomString,$userid);
    //                     header("accesstoken: $randomString");

    //                     $userProfileData=$this->api->getUserData2($userid);
    //                     $socialLoginData=$this->api->getSocialLoginData($userid);

    //                     $arrResponse = array('status' => 1, 'message' => 'User registered successfully!', 'userID' => $userid,'userData'=>$userProfileData,'syncAccount'=>$socialLoginData);

    //                         //$arrResponse = array('status' => 1, 'message' => 'User registered successfully!', 'userID' => $userid,'data'=>$insertData);
    //                 } else {
    //                     $arrResponse = array('status' => 0, 'message' => 'Insert data failed.');
    //                 }
    //             }
    //         }else{
    //             $arrResponse = array("status" => 0, "message" => "Email, Username and Password are mandatory.");
    //         }
    //     }else{
    //         $arrResponse = array("status" => 0, "message" => "Request Method Not accepted");
    //     }
    //     echo json_encode($arrResponse);
    // }

    public function Testing() {

        //header("sparsh: hey Testing");
        $headers = apache_request_headers();
        foreach ($headers as $key => $value) {
            switch ($key) {
                case 'accessToken':
                $accessToken = $value;
                break;
            }
        }

        echo "this will work after this";
        echo "<br>";
        echo $accessToken;
    }

    public function testIsUserActive(){
        $userID = '56';
        $isUserActive = $this->api->isUserActive($userID);
        if($isUserActive){
            echo $userID = $isUserActive;
        }else{
            echo $userID;
        }
    }

    //sample code to check if accessToken in rerquest header is valid 
    public function testing2() {
        $arrResponse = array();
        if($_SERVER['REQUEST_METHOD'] == "POST"){

            $db=$this->db->conn_id;//gives database conn variable to be used in mysqli_real_escape_string()

            //getting post data
            $userID = isset($_POST['userID']) ? mysqli_real_escape_string($db,$_POST['userID']) : '';
            if($userID!=NULL){// checking for required fields
                $accessToken=0;
                $isUserExist = $this->api->isUserExist($userID);
                if($isUserExist){
                    //$arrResponse = array('status'=>1, 'message'=>'accessToken is Valid','getAccessToken'=>$getAccessToken,'accessToken'=>$accessToken);
                    $headers = apache_request_headers();
                    foreach ($headers as $key => $value) {
                        switch ($key) {
                            case 'accesstoken':
                            $accessToken = $value;
                            break;
                        }
                    }
                    $getAccessToken = $this->api->getAccessToken($userID);
                    $getAccessToken = $getAccessToken['accessToken'];
                    if(strcmp($getAccessToken,$accessToken)==0){// chcking if accesstoken is valid
                        //main codingstarts if access token is valic

                        //Check if userID is active
                        //if not active==true; userID = newID pass that id and data

                        //$arrResponse = array('status'=>1, 'message'=>'accessToken is Valid','getAccessToken'=>$getAccessToken,'accessToken'=>$accessToken); 
                    }else{
                        $arrResponse = array('status'=>0, 'message'=>'Some fields are missing');
                    } 
                }else{
                    $arrResponse = array('status'=>0, 'message'=>'Error : Invalid userID '); 
                }
                
            }else{
                $arrResponse = array('status'=>0, 'message'=>'userID is required');
            }
        }else{
            $arrResponse = array('status'=>0, 'message'=>'requested method is not accepted');
        }
        echo json_encode($arrResponse);
    }//testing2 ends

    public function selectUserInterest() {
        $arrResponse = array();
        if($_SERVER['REQUEST_METHOD'] == "POST"){

            $db=$this->db->conn_id;//gives database conn variable to be used in mysqli_real_escape_string()

            //getting post data
            $userID = isset($_POST['userID']) ? mysqli_real_escape_string($db,$_POST['userID']) : '';
            $categoryID = isset($_POST['categoryID']) ? mysqli_real_escape_string($db,$_POST['categoryID']) : '';
            $isSelected = isset($_POST['isSelected']) ? mysqli_real_escape_string($db,$_POST['isSelected']) : '';
            if($userID!=NULL && $categoryID!=NULL && $isSelected!=NULL){// checking for required fields
                $accessToken = 0;
                $isUserExist = $this->api->isUserExist($userID);
                if($isUserExist){
                    $headers = apache_request_headers();
                    foreach ($headers as $key => $value) {
                        switch ($key) {
                            case 'accesstoken':
                            $accessToken = $value;
                            break;
                        }
                    }
                    $getAccessToken = $this->api->getAccessToken($userID);
                    $getAccessToken = $getAccessToken['accessToken'];
                    if(strcmp($getAccessToken,$accessToken)==0){// checking if accesstoken is valid
                        //main codingstarts if access token is valic
                        //make entry in userInterest table and effect following fields userID, categoryID, isSelected, updated, created                   

                        //here we have to do 2 things 1.update 2.insert dependending on if data is present

                        $isInterestPresent = $this->api->isInterestPresent($userID,$categoryID);
                        //true means user with this category exists 
                        if($isInterestPresent){
                            //we will have to use update query
                            $isSelectUpdate = $this->api->isSelectUpdate($userID,$categoryID,$isSelected);
                            if($isSelectUpdate){
                                //$arrResponse = array(); successfull with all userInterest data
                                $getCategoryListUserData = $this->api->getCategoryListUserData($userID);
                                $arrResponse = array('status'=>1,'message'=>'user interest updated successfully','interest'=>$getCategoryListUserData);
                            }else{
                                $arrResponse = array('status'=>0,'message'=>'Error updating please try again later.');
                            }
                        }else{
                            //we will have to use insert query
                            $insertUserInterest = $this->api->insertUserInterest($userID,$categoryID,$isSelected);
                            if($insertUserInterest){
                                //$arrResponse = array(); successfull with all userInterest data
                                $getCategoryListUserData = $this->api->getCategoryListUserData($userID);
                                $arrResponse = array('status'=>1,'message'=>'user interest updated successfully','interest'=>$getCategoryListUserData);
                            }else{
                                $arrResponse = array('status'=>0,'message'=>'Error updating please try again later.');
                            }
                        }

                        //$arrResponse = array('status'=>1, 'message'=>'accessToken is Valid','getAccessToken'=>$getAccessToken,'accessToken'=>$accessToken); 
                    }else{
                        $arrResponse = array('status'=>0, 'message'=>'Some fields are missing');
                    }
                }else{
                    $arrResponse = array('status'=>0, 'message'=>'Error : Invalid userID '); 
                }
            }else{
                $arrResponse = array('status'=>0, 'message'=>'userID, categoryID and isSelected is required.');
            }
        }else{
            $arrResponse = array('status'=>0, 'message'=>'requested method is not accepted');
        }
        echo json_encode($arrResponse);
    }//testing2 ends

    public function getCategoryList() {
        $arrResponse = array();
        $db=$this->db->conn_id;
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $userID = isset($_POST['userID']) ? mysqli_real_escape_string($db,$_POST['userID']) : '';
            $accessToken = 0;
            if($userID!=NULL){
                $isUserExist = $this->api->isUserExist($userID);
                if(($isUserExist)||($userID==0)){
                    $headers = apache_request_headers();
                    foreach ($headers as $key => $value) {
                        switch ($key) {
                            case 'accesstoken':
                            $accessToken = $value;
                            break;
                        }
                    }
                    $getAccessToken = $this->api->getAccessToken($userID);
                    $getAccessToken = $getAccessToken['accessToken'];

                    if((strcmp($getAccessToken,$accessToken)==0)||($userID==0 && $accessToken=='10cd14cdb637ab82fa37b70055062b1b')){
                        $getCategoryList = $this->api->getCategoryListUserData($userID);
                        //print_r($getCategoryList);
                        $arrResponse = array('status'=>1,'message'=>'success','interest'=>$getCategoryList);

                        //print_r($getCategoryList);
                    }else{
                        $arrResponse = array('status'=>0, 'message'=>'Some fields are missing');
                    }
                }else{
                    $arrResponse = array('status'=>0, 'message'=>'Error : Invalid userID '); 
                }
            }else{
                $arrResponse = array('status'=>0, 'message'=>'userID is required.');
            }
        }else{
            $arrResponse = array('status'=>0,'message'=>'requested method is not accepted');
        }
        echo json_encode($arrResponse);
    }

    public function couponIDComparison(){
        $db=$this->db->conn_id;
        $couponWeb = isset($_POST['couponWeb']) ? mysqli_real_escape_string($db,$_POST['couponWeb']) : '';
        $excelCoupon = isset($_POST['excelCoupon']) ? mysqli_real_escape_string($db,$_POST['excelCoupon']) : '';

        if(strcmp($couponWeb,$excelCoupon)==0){
            $arrResponse = array('status'=>1,'message'=>'success');
        }else{
            $arrResponse = array('status'=>0,'message'=>'fail');
        }
        echo json_encode($arrResponse);
    }

    public function getVideo() {
        $arrResponse = array();
        if($_SERVER['REQUEST_METHOD'] == "POST"){

            $db=$this->db->conn_id;//gives database conn variable to be used in mysqli_real_escape_string()

            //getting post data
            $userID = isset($_POST['userID']) ? mysqli_real_escape_string($db,$_POST['userID']) : '';
            //$limit = isset($_POST['limit']) ? mysqli_real_escape_string($db,$_POST['limit']) : '';
            $limit = 100;
            $offset = isset($_POST['nextVideo']) ? mysqli_real_escape_string($db,$_POST['nextVideo']) : '';
            $accessToken = 0;
            $isUserExist = $this->api->isUserExist($userID);
            if(($isUserExist)||($userID==0)){
                if($userID!=NULL && $limit!=NULL && $offset!=NULL){// checking for required fields
                    $headers = apache_request_headers();
                    foreach ($headers as $key => $value) {
                        switch ($key) {
                            case 'accesstoken':
                            $accessToken = $value;
                            break;
                        }
                    }
                    $getAccessToken = $this->api->getAccessToken($userID);
                    $getAccessToken = $getAccessToken['accessToken'];
                    if((strcmp($getAccessToken,$accessToken)==0)||($userID==0 && $accessToken=='10cd14cdb637ab82fa37b70055062b1b')){// chcking if accesstoken is valid
                        //main codingstarts if access token is valic

                        list($getUserVideo,$nextVideo) = $this->api->getUserVideo($userID,$limit,$offset);


                        $arrResponse = array('status'=>1, 'message'=>'success','nextVideo'=>$nextVideo,'video'=>$getUserVideo); 
                    }else{
                        $arrResponse = array('status'=>0, 'message'=>'Some fields are missing');
                    }
                }else{
                    $arrResponse = array('status'=>0, 'message'=>'userID and nextVideo offset is required');
                }
            }else{
                $arrResponse = array('status'=>0, 'message'=>'Error : Invalid userID '); 
            }
        }else{
            $arrResponse = array('status'=>0, 'message'=>'requested method is not accepted');
        }
        echo json_encode($arrResponse);
    }

    //help not real service
    public function printStar(){
        $db=$this->db->conn_id;
        $userID = isset($_POST['userID']) ? mysqli_real_escape_string($db,$_POST['userID']) : '';

        $this->api->getStars($userID);
    }
    //help not real service 

    public function addCategory(){
        //this is admin panel thing 
    }

    //just testing 
    // public function uploadVideo(){
    //     $arrResponse = array();
    //     if($_SERVER['REQUEST_METHOD'] == "POST"){
    //         $db=$this->db->conn_id;
    //         $userID = isset($_POST['userID']) ? mysqli_real_escape_string($db,$_POST['userID']) : '';

    //         $target_dir = "uploads/";
    //         $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    //         $_FILES['fileToUpload']['name'];
    //         move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],$target_file);

    //     }else{
    //         $arrResponse = array('status'=>0, 'message'=>'requested method is not accepted');
    //     }
    //     echo json_encode($arrResponse);
    // }
    //just testing

    public function uploadVideo() {
        $arrResponse = array();
        if($_SERVER['REQUEST_METHOD'] == "POST"){

            $db=$this->db->conn_id;
            $userID = isset($_POST['userID']) ? mysqli_real_escape_string($db,$_POST['userID']) : '';
            $categoryID = isset($_POST['categoryID']) ? mysqli_real_escape_string($db,$_POST['categoryID']) : '';
            //$fileName = isset($_POST['fileName']) ? mysqli_real_escape_string($db,$_POST['fileName']) : '';
            $title = isset($_POST['title']) ? mysqli_real_escape_string($db,$_POST['title']) : '';
            $description = isset($_POST['description']) ? mysqli_real_escape_string($db,$_POST['description']) : '';
            $tag = isset($_POST['tag']) ? mysqli_real_escape_string($db,$_POST['tag']) : '';
            $isPrivate = isset($_POST['isPrivate']) ? mysqli_real_escape_string($db,$_POST['isPrivate']) : '';
            $thumbnailTime = isset($_POST['thumbnailTime']) ? mysqli_real_escape_string($db,$_POST['thumbnailTime']) : '';
            // $duration = isset($_POST['duration']) ? mysqli_real_escape_string($db,$_POST['duration']) : '';

            //thumbnail will have to be created 
            //updated is not a problem it will be updated automatically
            //created we will have to handle it

            $accessToken = 0;
            $thumbnailTime = 1;
            if($userID!=NULL && $categoryID!=NULL && $title!=NULL && $isPrivate!=NULL){// checking for required fields

                $isUserExist = $this->api->isUserExist($userID);
                if($isUserExist){
                    $headers = apache_request_headers();
                    //print_r($headers);
                    foreach ($headers as $key => $value) {
                        switch ($key) {
                            case 'accesstoken':
                            $accessToken = $value;
                            break;
                        }
                    }
                    $getAccessToken = $this->api->getAccessToken($userID);
                    $getAccessToken = $getAccessToken['accessToken'];
                    if(strcmp($getAccessToken,$accessToken)==0) { // chcking if accesstoken is valid
                        if (isset($_FILES['videoUpload']['name']) && !empty($_FILES['videoUpload']['name'])) {
                            $dateCurrent = date("Y-M-d H:i:s");
                            $img_name = $userID.$categoryID.$dateCurrent.random_string('alnum') .random_string('unique'). str_replace(' ', '_', $_FILES['videoUpload']['name']);
                            //$img_name = str_replace(' ', '_', $_FILES['videoUpload']['name']);
                            $image_path = 'uploads/rawVideoFiles/rawUploadVideo/';
                            $config['upload_path'] = $image_path;
                            $config['file_name'] = $img_name;
                            $config['overwrite'] = FALSE;
                            $config["allowed_types"] = '*';
                            $config['encrypt_name'] = TRUE;
                            $config['remove_spaces'] = TRUE;
                            $this->load->library('upload', $config);
                            //$upload1 = $this->upload->initialize($config);

                            //$uploaded=$this->upload->data(); $uploaded['file_name']
                            /*------------------Uploading files ------------------------------*/
                            if ( ! $this->upload->do_upload('videoUpload')){
                                $error = array('error' => $this->upload->display_errors());
                                $arrResponse = array('status' => 0, 'message' => 'Upload error.', 'error' => $error);
                            }else{
                                $data = $this->upload->data();
                                //$fullPath1 = base_url() .'uploads/video/'. $data['file_name'];
                                //$fullPath = base_url() .'uploads/video/'. $data['file_name'];
                                $fullPath = $data['file_path'] . $data['file_name'];
                                // echo $fullPath1;exit;
                                //echo $fullPath1; echo "<br>" ;echo $fullPath2;exit;
                                //$this->load->helper('url');
                                //current_url();
                                // exec("ffmpeg -i ".$directory_path_full." ".$directory_path.$file_name.".flv");
                                // exec("ffmpeg -i ".$fullPath.".mp4");
                                $input = $data['full_path'];
                                // echo $input;exit;
                                // $output = $data['file_path'] .'thumbnail/'. $data['raw_name'] . ".jpg";
                                // echo getcwd();exit;
                                // $output = '/var/www/html/rockabyteServicesV2/uploads/video/thumbnail/'. $data['raw_name'] . ".jpg";

                                $output = getcwd().'/uploads/video/thumbnail/'. $data['raw_name'] . ".jpg";

                                // $videoOutputPath = $data['file_path']. $data['raw_name'] . '.mp4';
                                // $videoOutputPath = '/var/www/html/rockabyteServicesV2/uploads/video/'. $data['raw_name'] .'.mp4';

                                $videoOutputPath = getcwd().'/uploads/video/'. $data['raw_name'] .'.mp4';

                                $videoOutputLink = base_url() .'uploads/rawVideoFiles/rawUploadVideo/'. $data['raw_name'].'.mp4';
                                // echo $videoOuputPath;exit;

                                $duration = $this->getVideoDuration($input);
                                
                                $this->createThumbnail($input, $output, $thumbnailTime);
                                $thumbnail = base_url() .'uploads/video/thumbnail/'. $data['raw_name'] . ".jpg";

                                //formating Tags
                                $myArray = explode('#', $tag);
                                array_splice($myArray, 0, 1);
                                $singleTag='';
                                foreach ($myArray as $value) {
                                    $value = trim($value);
                                    $singleTag = $singleTag.'#'.$value;
                                    $singleTag = $singleTag.',';
                                }
                                $finalTags = trim($singleTag, ",");
                                //..formating Tags

                                $insertDataVideo = array(
                                    'userID' => $userID,
                                    'categoryID'=> $categoryID,
                                    'title'=> $title,
                                    'description'=> $description,
                                    'tag'=> $finalTags,
                                    'videoLink'=>$videoOutputLink,
                                    'duration'=> $duration,
                                    'thumbnail'=>$thumbnail,
                                    'isActive'=>'1',
                                    'isPrivate'=>$isPrivate,
                                    'created'=> date('Y-m-d H:i:s')
                                    );
                                $this->api->insertVideo($insertDataVideo);
                                $videoid = $this->db->insert_id();
                                

                                $getInsertVideoDetail = $this->api->getVideoDetail($videoid);
                                $arrResponse = array('status' => 1, 'message' => 'Video uploaded successfully, Internal processing will take a while to display/play video','Video'=>$getInsertVideoDetail);
                                $uploadStatus = $this->convertVideo($fullPath,$videoOutputPath);

                                // if($uploadStatus){
                                //     $updateData = array(
                                //         'uploadStatus'=>'1'
                                //         );
                                //     $this->api->updateVideo($videoid,$updateData);
                                // }
                                //$this->load->view('upload_success', $data);
                            }
                            /*------------------Uploading files ------------------------------*/

                            // if (!$uploaded = $this->upload->do_upload('video_stamp')) {
                            //     $error = array('error' => $this->upload->display_errors());
                            //     $arrResponse = array('response' => 'failure', 'message' => 'Upload error.', 'error' => $error);
                            // } else {
                            //     //$completeLink = 
                            //     $fullPath = base_url() .'uploads/video/'. $uploaded['file_name'];
                            //     $arrResponse = array('status' => 1, 'message' => 'yeahha its working','filename'=>$fullPath,'array'=>$uploaded, 'array2' => $upload1);
                            // }
                            //$arrResponse = array('status' => 1, 'message' => 'yeahha its working');
                        }else{
                            $arrResponse = array('status' => 0, 'message' => 'Please select video');
                        }
                        //main codingstarts if access token is valic

                        //Check if userID is active
                        //if not active==true; userID = newID pass that id and data

                        //$arrResponse = array('status'=>1, 'message'=>'accessToken is Valid','getAccessToken'=>$getAccessToken,'accessToken'=>$accessToken); 
                    }else{
                        $arrResponse = array('status'=>0, 'message'=>'Some fields are missing');
                    } 
                }else{
                    $arrResponse = array('status'=>0, 'message'=>'Error : Invalid userID '); 
                }
                
            }else{
                $arrResponse = array('status'=>0, 'message'=>'userID, categoryID, title, description, tag is required');
            }
        }else{
            $arrResponse = array('status'=>0, 'message'=>'requested method is not accepted');
        }
        echo json_encode($arrResponse);
    }

    private function convertVideo($input,$output){

        $ffmpegpath = "/usr/bin/ffmpeg";
        //$command = "ffmpeg -i /home/codetreasure/Downloads/raees.mov -vcodec h264 -acodec aac -strict -2 /home/codetreasure/Downloads/abcd.mp4";

        // echo $input; echo "<br>";
        // echo $output;exit();
        $command = "ffmpeg -i {$input} -y -vcodec h264 -s hd720 -acodec aac -strict -2 {$output} > /dev/null &";

        // -y -vcodec h264 -s hd720
        exec($command);
        // if(exec($command)){
        //     $updateData = array(
        //         'uploadStatus'=>'1'
        //         );
        //     $this->api->updateVideo($videoID,$updateData);
        // }else{
        //     return false;
        // }
        // if (!file_exists($output))
        //     return false;
        // if (filesize($output) == 0)
        //     return false;
        // return true;
    }

    private function createThumbnail($input, $output, $fromdurasec = "1") {
        $ffmpegpath = "/usr/bin/ffmpeg"; //'/usr/bin/ffmpeg'
        if (!file_exists($input))
            return false;

        //$command = "$ffmpegpath -i {$input} -vcodec libfaac -ab 96k -vcodec libx264 -vpre slower -vpre main -level 21 -refs 2 -b 345k -bt 345k -threads 0 -s 640x360 {$output}";
        //$time = exec("ffmpeg -i {$input} 2>&1 | grep Duration | cut -d ' ' -f 4 | sed s/,//");
        //$fromdurasec = ($time/2);
        $command = "ffmpeg  -itsoffset -{$fromdurasec}  -i {$input} -vcodec mjpeg -vframes 1 -an -f rawvideo -s 1280x720 {$output} ";

        // $command = "ffmpeg  -itsoffset -{$fromdurasec}  -i {$input} -vcodec mjpeg -vframes 1 -an -f rawvideo -s 1280x720 {$output} ";

        // $command = "ffmpeg -itsoffset -{$fromdurasec} -i {$input} -vframes 1 -filter:v scale='1280:-1' {$output}";
        exec($command);

        if (!file_exists($output))
            return false;
        if (filesize($output) == 0)
            return false;
        return true;
    }

    public function getVideoDuration($input){
        $time = exec("ffmpeg -i {$input} 2>&1 | grep Duration | cut -d ' ' -f 4 | sed s/,//");
        //HH:MM:SS.miliseconds
        // echo $time;
        return $time;
    }

    public function videoDetail() {
        $arrResponse = array();
        if($_SERVER['REQUEST_METHOD'] == "POST"){

            $db=$this->db->conn_id;//gives database conn variable to be used in mysqli_real_escape_string()

            //getting post data
            $wishVideo = 0;
            $userID = isset($_POST['userID']) ? mysqli_real_escape_string($db,$_POST['userID']) : '';
            $videoID = isset($_POST['videoID']) ? mysqli_real_escape_string($db,$_POST['videoID']) : '';
            $wishVideo = isset($_POST['wishVideo']) ? mysqli_real_escape_string($db,$_POST['wishVideo']) : '';

            if($userID!=NULL && $videoID!=NULL){//    checking for required fields
                $accessToken=0;
                $isVideoExist = $this->api->isVideoExist($videoID);
                if(1){
                    //$arrResponse = array('status'=>1, 'message'=>'accessToken is Valid','getAccessToken'=>$getAccessToken,'accessToken'=>$accessToken);
                    $headers = apache_request_headers();
                    foreach ($headers as $key => $value) {
                        switch ($key) {
                            case 'accesstoken':
                            $accessToken = $value;
                            break;
                        }
                    }
                    $getAccessToken = $this->api->getAccessToken($userID);
                    $getAccessToken = $getAccessToken['accessToken'];
                    if((strcmp($getAccessToken,$accessToken)==0)||($userID==0 && $accessToken=='10cd14cdb637ab82fa37b70055062b1b')){// chcking if accesstoken is valid
                        //main codingstarts if access token is valic
                        if($wishVideo!='1'){
                            //add videoID and userID in videoView table 
                            

                            //show the like flag if present
                            $videoDetail = $this->api->specificVideoDetail($videoID,$userID);

                            $videoUserID = $videoDetail[0]['userData']->{'userID'};
                            $videoCategoryID = $videoDetail[0]['category']->{'categoryID'};
                            //$videoTag = $videoDetail[0]['tag'];
                            $videoTag = implode (",", $videoDetail[0]['tag']);

                            // print_r($videoDetail);
                            // exit;
                            if($videoDetail){
                                $getSameUserVideo = $this->api->getSuggestedVideo($videoUserID,'sameUser',$userID,$videoID);
                                $getSameCategoryVideo = $this->api->getSuggestedVideo($videoCategoryID,'sameCategory',$userID,$videoID);
                                $videoTagArray = explode(',',$videoTag);
                                $getSameTagVideo = $this->api->getSuggestedVideo($videoTagArray,'sameTag',$userID,$videoID);
                                // $arrResponse = array(
                                //  'status'=>1,
                                //  'message'=>'success',
                                //  'sameUser'=>$getSameUserVideo,
                                //  'sameCategory'=>$getSameCategoryVideo,
                                //  'sameTag'=>$getSameTagVideo
                                //  );
                                $arrResponse = array(
                                    'status'=>1, 
                                    'message'=>'success',
                                    'videoDetail'=>$videoDetail,
                                    'sameUser'=>$getSameUserVideo,
                                    'sameCategory'=>$getSameCategoryVideo,
                                    'sameTag'=>$getSameTagVideo
                                ); 
                            }else{
                                $arrResponse = array('status'=>0, 'message'=>'Error getting fetching data');
                            }
                        }elseif($wishVideo=='1'){
                            // this is wish video page 
                            $wishVideoID = $videoID;
                            $getSameUserVideo = array();
                            $getSameCategoryVideo = array();
                            $getSameTagVideo = array();

                            //getting wish video detail from WishVideoID
                            $videoDetail = $this->api->specificWishVideoDetail($wishVideoID);

                            $arrResponse = array(
                                    'status'=>1, 
                                    'message'=>'success',
                                    'videoDetail'=>$videoDetail,
                                    'sameUser'=>$getSameUserVideo,
                                    'sameCategory'=>$getSameCategoryVideo,
                                    'sameTag'=>$getSameTagVideo
                                );
                        }
                    }else{
                        $arrResponse = array('status'=>0, 'message'=>'Some fields are missing');
                    } 
                }else{
                    $arrResponse = array('status'=>0, 'message'=>'Error : Invalid videoID/inActive VideoID '); 
                }
            }else{
                $arrResponse = array('status'=>0, 'message'=>'userID, videoID are required.');
            }
        }else{
            $arrResponse = array('status'=>0, 'message'=>'requested method is not accepted');
        }
        echo json_encode($arrResponse);
    }

    public function likeDislikeVideo() {
        $arrResponse = array();
        if($_SERVER['REQUEST_METHOD'] == "POST"){

            $db=$this->db->conn_id;//gives database conn variable to be used in mysqli_real_escape_string()

            //getting post data
            $userID = isset($_POST['userID']) ? mysqli_real_escape_string($db,$_POST['userID']) : '';
            $videoID = isset($_POST['videoID']) ? mysqli_real_escape_string($db,$_POST['videoID']) : '';
            $likeDislikeFlag = isset($_POST['flag']) ? mysqli_real_escape_string($db,$_POST['flag']) : '';

            //give created time 
            //if video flag = 1 like // update this in `video` table like count 
            //if video flag = 0 dislike
            if($userID!=NULL && $videoID!=NULL && $likeDislikeFlag!=NULL){// checking for required fields
                $accessToken=0;
                $isUserExist = $this->api->isUserExist($userID);
                if($isUserExist){
                    //$arrResponse = array('status'=>1, 'message'=>'accessToken is Valid','getAccessToken'=>$getAccessToken,'accessToken'=>$accessToken);
                    $headers = apache_request_headers();
                    foreach ($headers as $key => $value) {
                        switch ($key) {
                            case 'accesstoken':
                            $accessToken = $value;
                            break;
                        }
                    }
                    $getAccessToken = $this->api->getAccessToken($userID);
                    $getAccessToken = $getAccessToken['accessToken'];
                    if(strcmp($getAccessToken,$accessToken)==0){// chcking if accesstoken is valid
                        //main codingstarts if access token is valic

                        $userLikeVideoExist = $this->api->userLikeVideoExist($userID,$videoID,$likeDislikeFlag);

                        if($userLikeVideoExist){
                            //update
                            $updateUserLikeVideo = $this->api->updateUserLikeVideo($userID,$videoID,$likeDislikeFlag);

                            if($updateUserLikeVideo){
                                //show the details of videoID
                                $videoDetail = $this->api->specificVideoDetail($videoID,$userID);

                                //startCoding from here
                                //----------------------GCM AND APNS NOTIFICATION-------------//

                                //Getting Receiver data
                                $videoUserID = $videoDetail[0]['userData']->{'userID'};
                                $receiverUserProfileData=$this->api->getUserData2($videoUserID);
                                $androidKey = $receiverUserProfileData['androidKey'];
                                $iosKey = $receiverUserProfileData['iosKey'];

                                //Getting Sender message and data
                                $senderUserProfileData=$this->api->getUserData2($userID);
                                $senderFullName = $senderUserProfileData['fullName'];
                                $senderUserName = $senderUserProfileData['userName'];

                                $videoTitle = $videoDetail[0]['title'];
                                $notification = "liked your video $videoTitle";
                                // $this->sendUserNotification($androidKey,$iosKey,$notification,$senderFullName,$senderUserName);

                                //----------------------GCM AND APNS NOTIFICATION-------------//

                                $arrResponse = array('status'=>1, 'message'=>'success updating likeFlag','videoDetail'=>$videoDetail); 
                            }else{
                                //show error
                                $arrResponse = array('status'=>0, 'message'=>'Error : updating ');
                            }

                        }else{
                            //insert
                            $insertData = array(
                                'userID' => $userID,
                                'videoID'=> $videoID,
                                'likeFlag'=> $likeDislikeFlag,
                                'created'=> date('Y-m-d H:i:s')
                                );
                            $insertUserLikeVideo = $this->api->insertUserLikeVideo($insertData);
                            //show the details of videoID
                            if($insertUserLikeVideo){
                                //show the details of videoID
                                $videoDetail = $this->api->specificVideoDetail($videoID,$userID);

                                // $sendingGcmMessage = $this->androidNotification();
                                
                                //----------------------GCM AND APNS NOTIFICATION-------------//

                                //Getting Receiver data
                                $videoUserID = $videoDetail[0]['userData']->{'userID'};
                                $receiverUserProfileData=$this->api->getUserData2($videoUserID);
                                $androidKey = $receiverUserProfileData['androidKey'];
                                $iosKey = $receiverUserProfileData['iosKey'];

                                //Getting Sender message and data
                                $senderUserProfileData=$this->api->getUserData2($userID);
                                $senderFullName = $senderUserProfileData['fullName'];
                                $senderUserName = $senderUserProfileData['userName'];

                                $videoTitle = $videoDetail[0]['title'];
                                $notification = "liked your video $videoTitle";
                                // $this->sendUserNotification($androidKey,$iosKey,$notification,$senderFullName,$senderUserName);

                                //----------------------GCM AND APNS NOTIFICATION-------------//

                                $arrResponse = array('status'=>1, 'message'=>'success inserting likeFlag','videoDetail'=>$videoDetail);
                            }else{
                                //show error
                                $arrResponse = array('status'=>0, 'message'=>'Error : inserting ');
                            }
                        }
                    }else{
                        $arrResponse = array('status'=>0, 'message'=>'Some fields are missing');
                    } 
                }else{
                    $arrResponse = array('status'=>0, 'message'=>'Error : Invalid userID '); 
                }
                
            }else{
                $arrResponse = array('status'=>0, 'message'=>'userID, videoID and flag is required');
            }
        }else{
            $arrResponse = array('status'=>0, 'message'=>'requested method is not accepted');
        }
        echo json_encode($arrResponse);
    }


    public function updateVideoViewCount() {
        $arrResponse = array();
        if($_SERVER['REQUEST_METHOD'] == "POST"){

            $db=$this->db->conn_id;//gives database conn variable to be used in mysqli_real_escape_string()

            //getting post data
            $userID = isset($_POST['userID']) ? mysqli_real_escape_string($db,$_POST['userID']) : '';
            $videoID = isset($_POST['videoID']) ? mysqli_real_escape_string($db,$_POST['videoID']) : '';
            if($userID!=NULL){// checking for required fields
                $accessToken=0;
                $isUserExist = $this->api->isUserExist($userID);

                if($userID == 0){
                    $isUserExist=1;
                }

                if($isUserExist){
                    //$arrResponse = array('status'=>1, 'message'=>'accessToken is Valid','getAccessToken'=>$getAccessToken,'accessToken'=>$accessToken);
                    $headers = apache_request_headers();
                    foreach ($headers as $key => $value) {
                        switch ($key) {
                            case 'accesstoken':
                            $accessToken = $value;
                            break;
                        }
                    }
                    $getAccessToken = $this->api->getAccessToken($userID);
                    $getAccessToken = $getAccessToken['accessToken'];
                    if((strcmp($getAccessToken,$accessToken)==0)||($userID==0 && $accessToken=='10cd14cdb637ab82fa37b70055062b1b')){// chcking if accesstoken is valid
                        //main codingstarts if access token is valic

                        //Check if userID is active
                        //if not active==true; userID = newID pass that id and data

                        $insertData = array(
                            'userID' => $userID,
                            'videoID'=> $videoID,
                            'created'=> date('Y-m-d H:i:s')
                        );
                        $this->api->insertVideoView($insertData);

                        $viewCount = $this->api->getViewCount($videoID);
                        $arrResponse = array('status'=>1, 'message'=>'Video viewCount Updated','viewCount'=> $viewCount); 
                    }else{
                        $arrResponse = array('status'=>0, 'message'=>'Some fields are missing');
                    } 
                }else{
                    $arrResponse = array('status'=>0, 'message'=>'Error : Invalid userID '); 
                }
                
            }else{
                $arrResponse = array('status'=>0, 'message'=>'userID is required');
            }
        }else{
            $arrResponse = array('status'=>0, 'message'=>'requested method is not accepted');
        }
        echo json_encode($arrResponse);
    }


    private function sendUserNotification($androidKey,$iosKey,$notification,$senderFullName,$senderUserName){

        if($senderUserName==''){
            $senderName = $senderFullName;
        }else{
            $senderName = $senderUserName;
        }

        $finalNotification = "$senderName  $notification";
        //echo $finalNotification;
        if(!empty($androidKey)){
            $sendingGcmMessage = $this->api->androidNotification($androidKey,$finalNotification);
        }

        if(!empty($iosKey)){
            // $sendingApnsMessage = $this->api->iphoneNotification($iosKey,$finalNotification);
        }
    }


    // public function androidNotification($reg_key,$notification){
    // public function androidNotification(){
    //     // API access key from Google API's Console
    //     $reg_key = "dbyIJkoPSc8:APA91bEW9UYvaskdMIMT7exRK-Q89EuZQIVJO5HsAKFUxEux7Qtq7kMRAT2RhctLui7fh0fFxhB2kTvqVuZRPJVt2BSbmYX4iBr-fnuUMR46PR-VBpFCwFS-q8LAsZh43J-gqfCbCb-c";
    //     $notification = "hello world";

    //     define( 'API_ACCESS_KEY', 'AIzaSyCdyD5YrAoqiZm-EieAVnYvczJRZlEdr84' );
    //     $registrationIds = array($reg_key);

    //     // prep the bundle
    //     $msg = array
    //     (
    //         'message'   => $notification,
    //         'title'     => 'Rockabyte',
    //         'subtitle'  => 'notification',
    //         'tickerText'    => 'Ticker text here...Ticker text here...Ticker text here',
    //         'vibrate'   => 1,
    //         'sound'     => 1,
    //         'largeIcon' => 'large_icon',
    //         'smallIcon' => 'small_icon'
    //         );
    //     $fields = array
    //     (
    //         'registration_ids'  => $registrationIds,
    //         'data'          => $msg
    //         );

    //     $headers = array
    //     (
    //         'Authorization: key=' . API_ACCESS_KEY,
    //         'Content-Type: application/json'
    //         );

    //     $ch = curl_init();
    //     curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
    //     curl_setopt( $ch,CURLOPT_POST, true );
    //     curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    //     curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    //     curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    //     curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
    //     $result = curl_exec($ch );
    //     curl_close( $ch );
    //     echo $result;

    //     //return $result;
    // }

    // private function iosNotification($deviceToken,$message){
    //     // Put your device token here (without spaces):
    //     //--$deviceToken = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

    //     // Put your private key's passphrase here:
    //     $passphrase = 'xxxxxxx';

    //     // Put your alert message here:
    //     //--$message = 'A push notification has been sent!';

    //     ////////////////////////////////////////////////////////////////////////////////

    //     $ctx = stream_context_create();
    //     stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck_file_name.pem');
    //     stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

    //     // Open a connection to the APNS server
    //     $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

    //     if (!$fp)
    //         exit("Failed to connect: $err $errstr" . PHP_EOL);

    //     echo 'Connected to APNS' . PHP_EOL;

    //     // Create the payload body
    //     $body['aps'] = array(
    //         'alert' => array(
    //             'body' => $message,
    //             'action-loc-key' => 'Bango App',
    //             ),
    //         'badge' => 2,
    //         'sound' => 'oven.caf',
    //         );

    //     // Encode the payload as JSON
    //     $payload = json_encode($body);

    //     // Build the binary notification
    //     $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

    //     // Send it to the server
    //     $result = fwrite($fp, $msg, strlen($msg));

    //     if (!$result){
    //         //-- echo 'Message not delivered' . PHP_EOL;
    //         $returnResult = 'Message not delivered' . PHP_EOL;
    //     }else{
    //         // echo 'Message successfully delivered' . PHP_EOL;
    //         $returnResult = 'Message successfully delivered' . PHP_EOL;
    //     }
    //     // Close the connection to the server
    //     fclose($fp);

    //     return $returnResult;
    // }

    public function suggestionVideo() {
        $arrResponse = array();
        if($_SERVER['REQUEST_METHOD'] == "POST"){

            $db=$this->db->conn_id;//gives database conn variable to be used in mysqli_real_escape_string()

            //getting post data
            $userID = isset($_POST['userID']) ? mysqli_real_escape_string($db,$_POST['userID']) : '';
            $videoUserID = isset($_POST['videoUserID']) ? mysqli_real_escape_string($db,$_POST['videoUserID']) : '';
            $videoCategoryID = isset($_POST['videoCategoryID']) ? mysqli_real_escape_string($db,$_POST['videoCategoryID']) : '';
            $videoTag = isset($_POST['videoTag']) ? mysqli_real_escape_string($db,$_POST['videoTag']) : '';

            if($userID!=NULL && $videoUserID!=NULL && $videoCategoryID!=NULL && $videoTag!=NULL){// checking for required fields
                $accessToken=0;
                $isUserExist = $this->api->isUserExist($userID);
                if($isUserExist){
                    //$arrResponse = array('status'=>1, 'message'=>'accessToken is Valid','getAccessToken'=>$getAccessToken,'accessToken'=>$accessToken);
                    $headers = apache_request_headers();
                    foreach ($headers as $key => $value) {
                        switch ($key) {
                            case 'accesstoken':
                            $accessToken = $value;
                            break;
                        }
                    }
                    $getAccessToken = $this->api->getAccessToken($userID);
                    $getAccessToken = $getAccessToken['accessToken'];
                    if(strcmp($getAccessToken,$accessToken)==0){// chcking if accesstoken is valid
                        //main codingstarts if access token is valic

                        //same user
                        //same category
                        //same tags
                        $getSameUserVideo = $this->api->getSuggestedVideo($videoUserID,'sameUser');
                        $getSameCategoryVideo = $this->api->getSuggestedVideo($videoCategoryID,'sameCategory');
                        $getSameTagVideo = $this->api->getSuggestedVideo($videoTag,'sameTag');
                        $arrResponse = array(
                           'status'=>1,
                           'message'=>'success',
                           'sameUser'=>$getSameUserVideo,
                           'sameCategory'=>$getSameCategoryVideo,
                           'sameTag'=>$getSameTagVideo
                           );
                        //getSameCategoryVideo();
                        //getSameTagVideo();
                    }else{
                        $arrResponse = array('status'=>0, 'message'=>'Some fields are missing');
                    } 
                }else{
                    $arrResponse = array('status'=>0, 'message'=>'Error : Invalid userID '); 
                }
            }else{
                $arrResponse = array('status'=>0, 'message'=>'userID, videoUserID, videoCategoryID, videoTags are required');
            }
        }else{
            $arrResponse = array('status'=>0, 'message'=>'requested method is not accepted');
        }
        echo json_encode($arrResponse);
    }

    public function userProfileDetail() {
        $arrResponse = array();
        if($_SERVER['REQUEST_METHOD'] == "POST"){

            $db=$this->db->conn_id;//gives database conn variable to be used in mysqli_real_escape_string()

            //getting post data
            $userID = isset($_POST['userID']) ? mysqli_real_escape_string($db,$_POST['userID']) : '';
            if($userID!=NULL){// checking for required fields
                $accessToken=0;
                $isUserExist = $this->api->isUserExist($userID);
                if($isUserExist){
                    //$arrResponse = array('status'=>1, 'message'=>'accessToken is Valid','getAccessToken'=>$getAccessToken,'accessToken'=>$accessToken);
                    $headers = apache_request_headers();
                    foreach ($headers as $key => $value) {
                        switch ($key) {
                            case 'accesstoken':
                            $accessToken = $value;
                            break;
                        }
                    }
                    $getAccessToken = $this->api->getAccessToken($userID);
                    $getAccessToken = $getAccessToken['accessToken'];
                    if(strcmp($getAccessToken,$accessToken)==0){// chcking if accesstoken is valid
                        //main codingstarts if access token is valic

                        //Check if userID is active
                        //if not active==true; userID = newID pass that id and data

                        //$arrResponse = array('status'=>1, 'message'=>'accessToken is Valid','getAccessToken'=>$getAccessToken,'accessToken'=>$accessToken);
                        $userProfileData = $this->api->getUserData2($userID); 
                        // $socialLoginData = $this->api->socialLoginData($userID);
                        $userUploadedVideoData = $this->api->userUploadedVideo($userID);
                        $arrResponse = array('status'=>1,
                            'message'=>'success',
                            'userData'=>$userProfileData,
                            // 'syncAccount'=>$socialLoginData,
                            'uploadedVideo'=>$userUploadedVideoData
                        );
                    }else{
                        $arrResponse = array('status'=>0, 'message'=>'Some fields are missing');
                    } 
                }else{
                    $arrResponse = array('status'=>0, 'message'=>'Please Login.'); 
                }
                
            }else{
                $arrResponse = array('status'=>0, 'message'=>'userID is required');
            }
        }else{
            $arrResponse = array('status'=>0, 'message'=>'requested method is not accepted');
        }
        echo json_encode($arrResponse);
    }

    
    public function editUserProfile() {
        $arrResponse = array();
        if($_SERVER['REQUEST_METHOD'] == "POST"){

            $db=$this->db->conn_id;//gives database conn variable to be used in mysqli_real_escape_string()

            //getting post data
            $userID = isset($_POST['userID']) ? mysqli_real_escape_string($db,$_POST['userID']) : '';
            $fullName = isset($_POST['name']) ? mysqli_real_escape_string($db,$_POST['name']) : '';
            $userName = isset($_POST['userName']) ? mysqli_real_escape_string($db,$_POST['userName']) : '';
            $occupation = isset($_POST['occupation']) ? mysqli_real_escape_string($db,$_POST['occupation']) : '';
            $phoneNo = isset($_POST['phoneNo']) ? mysqli_real_escape_string($db,$_POST['phoneNo']) : '';
            $gender = isset($_POST['gender']) ? mysqli_real_escape_string($db,$_POST['gender']) : '';
            $country = isset($_POST['country']) ? mysqli_real_escape_string($db,$_POST['country']) : '';
            $dob = isset($_POST['dob']) ? mysqli_real_escape_string($db,$_POST['dob']) : '';
            $state = isset($_POST['state']) ? mysqli_real_escape_string($db,$_POST['state']) : '';
            $aboutMe = isset($_POST['aboutMe']) ? mysqli_real_escape_string($db,$_POST['aboutMe']) : '';
            $email = isset($_POST['email']) ? mysqli_real_escape_string($db,$_POST['email']) : '';
            if($userID!=NULL){// checking for required fields
                $accessToken=0;
                $isUserExist = $this->api->isUserExist($userID);
                if($isUserExist){
                    //$arrResponse = array('status'=>1, 'message'=>'accessToken is Valid','getAccessToken'=>$getAccessToken,'accessToken'=>$accessToken);
                    $headers = apache_request_headers();
                    foreach ($headers as $key => $value) {
                        switch ($key) {
                            case 'accesstoken':
                            $accessToken = $value;
                            break;
                        }
                    }
                    $getAccessToken = $this->api->getAccessToken($userID);
                    $getAccessToken = $getAccessToken['accessToken'];
                    if(strcmp($getAccessToken,$accessToken)==0){// chcking if accesstoken is valid
                        //main codingstarts if access token is valic

                        $userNameExistStatus = 0;
                        $emailExistStatus = 0;
                        $phoneNoExistStatus = 0;
                        if(!empty($userName)){
                            $userNameExist = $this->api->is_userName_exist($userName);
                            if($userNameExist){
                                //$arrResponse = array('status'=>0, 'message'=>'User Name already exists.');
                                $userNameExistStatus = 1;
                                $userNameBelongToUser = $this->dataBelongToCurrentUser($userID,'userName',$userName);
                                if($userNameBelongToUser){
                                    $userNameExistStatus = 0;
                                }
                            }else{
                                //$userData['userName'] = $userName;
                                $userNameExistStatus = 0;
                            }
                        }
                        if(!empty($email)){
                            $emailExist = $this->api->is_email_exist($email);
                            if($emailExist){
                                //$arrResponse = array('status'=>0, 'message'=>'User with this Email already exists.');
                                $emailExistStatus = 1;
                                $emailBelongToUser = $this->dataBelongToCurrentUser($userID,'email',$email);
                                if($emailBelongToUser){
                                    $emailExistStatus = 0;
                                }
                            }else{
                                //$userData['email'] = $email;
                                $emailExistStatus = 0;
                            }
                        }
                        if(!empty($phoneNo)){
                            $phoneNoExist = $this->api->is_mobile_no_exist($phoneNo);
                            if($phoneNoExist){
                                //$arrResponse = array('status'=>0, 'message'=>'User with this Phone No already exists.');
                                $phoneNoExistStatus = 1;
                                $phoneNoBelongToUser = $this->dataBelongToCurrentUser($userID,'phoneNo',$phoneNo);
                                if($phoneNoBelongToUser){
                                    $phoneNoExistStatus = 0;
                                }
                                
                            }else{
                                //$userData['phoneNo'] = $phoneNo;
                                $phoneNoExistStatus = 0;
                            }
                        }
                        
                        if($phoneNoExistStatus==1){
                            $arrResponse = array('status'=>0, 'message'=>'User with this Phone No already exists.');
                        }elseif($emailExistStatus==1){
                            $arrResponse = array('status'=>0, 'message'=>'User with this Email already exists.');
                        }elseif($userNameExistStatus==1){
                            $arrResponse = array('status'=>0, 'message'=>'User Name already exists.');
                        }else{

                            if($dob!=''){
                                $dob = date('Y-m-d',strtotime($dob));
                            }

                            if(!empty($fullName)){
                                $userData['fullName'] = $fullName;
                            }
                            $userData['occupation'] = $occupation;
                            $userData['gender'] = $gender;
                            $userData['country'] = $country;
                            $userData['dob'] = $dob;
                            $userData['state'] = $state;
                            $userData['aboutMe'] = $aboutMe;
                            $userData['email'] = $email;
                            $userData['userName'] = $userName;
                            if(!empty($email)){
                                $userData['email'] = $email;
                            }
                            $userData['phoneNo'] = $phoneNo;

                            // echo 'hello';
                            // print_r($userData);exit;
                            $result = $this->api->updateUser($userID,$userData);

                            $userProfileData=$this->api->getUserData2($userID);
                            $socialLoginData=$this->api->getSocialLoginData($userID);
                            $getCategoryList = $this->api->getCategoryListUserData($userID);
                            if($socialLoginData){
                                $updateLastLogin = $this->api->updateLastLogin($userID);
                            }
                            // if($result){
                            $arrResponse = array('status'=>1, 'message'=>'User profile updated successfully','userData'=>$userProfileData,'interest'=>$getCategoryList);
                            // }else{
                            //     $arrResponse = array('status'=>0, 'message'=>'Error updating user data please try again.');
                            // }
                        }
                    }else{
                        $arrResponse = array('status'=>0, 'message'=>'Some fields are missing');
                    } 
                }else{
                    $arrResponse = array('status'=>0, 'message'=>'Error : Invalid userID '); 
                }
                
            }else{
                $arrResponse = array('status'=>0, 'message'=>'userID is required');
            }
        }else{
            $arrResponse = array('status'=>0, 'message'=>'requested method is not accepted');
        }
        echo json_encode($arrResponse);
    }

    private function dataBelongToCurrentUser($userID,$dataParameter,$parameterValue){
        $resultStatus = $this->api->dataBelongToCurrentUser($userID,$dataParameter,$parameterValue);
        return $resultStatus;
    }

    public function skipAndExplore(){
        $arrResponse = array();
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $userID = 0;
            $accessToken = '10cd14cdb637ab82fa37b70055062b1b';
            header("accesstoken: $accessToken");
            $userProfileData=$this->api->getUserData2($userID);
            $socialLoginData=$this->api->getSocialLoginData($userID);
            $getCategoryList = $this->api->getCategoryListUserData($userID);

            //10cd14cdb637ab82fa37b70055062b1b  

            $arrResponse = array('status' => 1, 'message' => 'Default User Login Successfull!','userID'=>'0');
        }else{
            $arrResponse = array("status" => 0, "message" => "Request Method Not accepted");
        }
        echo json_encode($arrResponse);
    }

    public function uploadWishVideo() {
        $arrResponse = array();
        if($_SERVER['REQUEST_METHOD'] == "POST"){

            $db=$this->db->conn_id;//gives database conn variable to be used in mysqli_real_escape_string()

            //getting post data
            $userID = isset($_POST['userID']) ? mysqli_real_escape_string($db,$_POST['userID']) : '';
            $email = isset($_POST['email']) ? mysqli_real_escape_string($db,$_POST['email']) : '';
            $date = isset($_POST['date']) ? mysqli_real_escape_string($db,$_POST['date']) : '';
            $time = isset($_POST['time']) ? mysqli_real_escape_string($db,$_POST['time']) : '';
            $title = isset($_POST['title']) ? mysqli_real_escape_string($db,$_POST['title']) : '';
            $description = isset($_POST['description']) ? mysqli_real_escape_string($db,$_POST['description']) : '';
            $thumbnailTime = isset($_POST['thumbnailTime']) ? mysqli_real_escape_string($db,$_POST['thumbnailTime']) : '';

            if($userID!=NULL && $email!=NULL && $date!=NULL && $time!=NULL){// checking for required fields
                $accessToken=0;
                $thumbnailTime = 1;
                $isUserExist = $this->api->isUserExist($userID);
                if($isUserExist){

                    $headers = apache_request_headers();
                    foreach ($headers as $key => $value) {
                        switch ($key) {
                            case 'accesstoken':
                            $accessToken = $value;
                            break;
                        }
                    }
                    $getAccessToken = $this->api->getAccessToken($userID);
                    $getAccessToken = $getAccessToken['accessToken'];
                    if(strcmp($getAccessToken,$accessToken)==0){// chcking if accesstoken is valid
                        //main codingstarts if access token is valic

                        if (isset($_FILES['videoUpload']['name']) && !empty($_FILES['videoUpload']['name'])) {
                            $dateCurrent = date("Y-m-d H:i:s");
                            $img_name = $userID.$dateCurrent.random_string('alnum') .random_string('unique'). str_replace(' ', '_', $_FILES['videoUpload']['name']);
                            //$img_name = str_replace(' ', '_', $_FILES['videoUpload']['name']);
                            $image_path = 'uploads/rawVideoFiles/rawWishVideo/';
                            $config['upload_path'] = $image_path;
                            $config['file_name'] = $img_name;
                            $config['overwrite'] = FALSE;
                            $config["allowed_types"] = '*';
                            $config['encrypt_name'] = TRUE;
                            $config['remove_spaces'] = TRUE;
                            $this->load->library('upload', $config);
                            //$upload1 = $this->upload->initialize($config);

                            //$uploaded=$this->upload->data(); $uploaded['file_name']
                            /*------------------Uploading files ------------------------------*/
                            if ( ! $this->upload->do_upload('videoUpload')){
                                $error = array('error' => $this->upload->display_errors());
                                $arrResponse = array('status' => 0, 'message' => 'Upload error.', 'error' => $error);
                            }else{
                                $data = $this->upload->data();
                                //$fullPath = base_url() .'uploads/video/'. $data['file_name'];
                                //$this->load->helper('url');
                                //current_url();
                                $fullPath = $data['file_path'] . $data['file_name'];
                                // $videoOutputPath = '/var/www/html/rockabyteServicesV2/uploads/wishVideo/'. $data['raw_name'] .'.mp4';
                                

                                $videoOutputPath = getcwd().'/uploads/wishVideo/'. $data['raw_name'] .'.mp4';
                                // $videoOutputPath = $data['file_path']. $data['raw_name'] . '.mp4';
                                $videoOutputLink = base_url() .'uploads/rawVideoFiles/rawWishVideo/'. $data['raw_name'].'.mp4';

                                $input = $data['full_path'];
                                //CodeIgniter310
                                $output = getcwd().'/uploads/wishVideo/thumbnail/'. $data['raw_name'] . ".jpg";
                                // $output = $data['file_path'] .'thumbnail/'. $data['raw_name'] . ".jpg";

                                $duration = $this->getVideoDuration($input);

                                //$thumbnailTime 
                                if(empty($thumbnailTime)){
                                    $thumbnailTime = 1;
                                }

                                $this->createThumbnail($input, $output, $thumbnailTime);
                                $thumbnail = base_url() .'uploads/wishVideo/thumbnail/'. $data['raw_name'] . ".jpg";



                                if($date!=''){
                                    $date = date('Y-m-d',strtotime($date));
                                }
                                $insertDataVideo = array(
                                    'userID' => $userID,
                                    'receiverEmail'=> $email,
                                    'receiveDateTime'=> $date.' '.$time,
                                    //'receiveTime'=> $time,
                                    'title'=> $title,
                                    'description'=> $description,
                                    'videoLink'=> $videoOutputLink,
                                    'thumbnail'=> $thumbnail,
                                    'duration'=> $duration,
                                    'created'=> date('Y-m-d H:i:s')
                                    );
                                $this->api->insertWishVideo($insertDataVideo);
                                $videoid = $this->db->insert_id();
                                $getInsertVideoDetail = $this->api->getWishVideoDetail($videoid);
                                $arrResponse = array('status' => 1, 'message' => 'Wish Video uploaded successfully','Video'=>$getInsertVideoDetail);

                                $uploadStatus = $this->convertVideo($fullPath,$videoOutputPath);
                            }
                            /*------------------Uploading files ------------------------------*/
                        }else{
                            $arrResponse = array('status' => 0, 'message' => 'Please select video');
                        }
                    }else{
                        $arrResponse = array('status'=>0, 'message'=>'Some fields are missing');
                    } 
                }else{
                    $arrResponse = array('status'=>0, 'message'=>'Error : Invalid userID '); 
                }
                
            }else{
                $arrResponse = array('status'=>0, 'message'=>'userID, email, date, time is required');
            }
        }else{
            $arrResponse = array('status'=>0, 'message'=>'requested method is not accepted');
        }
        echo json_encode($arrResponse);
    }

    public function sendEmailWishVideo(){
        //echo "works";exit;
        $response = $this->api->sendEmailWishVideo();
        //echo $response;
    }

    //things i will need
    /*_________________________________________________________
    |1. chatID // it will not be here                          |
    |2. chatType //                                            |
    |3. senderUserID->name->profilePicture                     |
    |4. messages->allthe->messages which are active between    |
    |    chatID->                                              |
    |    message->type direct|wish|videoID                     |
    |                                                          |
    |__________________________________________________________*/
    public function getChatMessage() {
        $arrResponse = array();
        if($_SERVER['REQUEST_METHOD'] == "POST"){

            $db=$this->db->conn_id;//gives database conn variable to be used in mysqli_real_escape_string()

            //getting post data
            $userID = isset($_POST['userID']) ? mysqli_real_escape_string($db,$_POST['userID']) : '';
            if($userID!=NULL){// checking for required fields
                $accessToken=0;
                $isUserExist = $this->api->isUserExist($userID);
                if($isUserExist){
                    //$arrResponse = array('status'=>1, 'message'=>'accessToken is Valid','getAccessToken'=>$getAccessToken,'accessToken'=>$accessToken);
                    $headers = apache_request_headers();
                    foreach ($headers as $key => $value) {
                        switch ($key) {
                            case 'accesstoken':
                            $accessToken = $value;
                            break;
                        }
                    }
                    $getAccessToken = $this->api->getAccessToken($userID);
                    $getAccessToken = $getAccessToken['accessToken'];
                    if(strcmp($getAccessToken,$accessToken)==0){// chcking if accesstoken is valid
                        
                        //the methods here and do the json
                        $response = $this->api->getChatMessage($userID);
                        $arrResponse = array('status'=>1, 'message'=> 'success','chatDetail'=>$response);

                    }else{
                        $arrResponse = array('status'=>0, 'message'=>'Some fields are missing');
                    } 
                }else{
                    $arrResponse = array('status'=>0, 'message'=>'Error : Invalid userID '); 
                }
                
            }else{
                $arrResponse = array('status'=>0, 'message'=>'userID is required');
            }
        }else{
            $arrResponse = array('status'=>0, 'message'=>'requested method is not accepted');
        }
        echo json_encode($arrResponse);
    }

    public function email_address_to_userid(){
        $db=$this->db->conn_id;
        $email = isset($_POST['email']) ? mysqli_real_escape_string($db,$_POST['email']) : '';
        $result = $this->api->userIDOfEmailAddress($email);
        //echo $result;
        if($result>0){
            echo $result;
        }else{
            echo "no email address";
        }
    }

    public function getUserNotification() {
        $arrResponse = array();
        if($_SERVER['REQUEST_METHOD'] == "POST"){

            $db=$this->db->conn_id;//gives database conn variable to be used in mysqli_real_escape_string()

            //getting post data
            $userID = isset($_POST['userID']) ? mysqli_real_escape_string($db,$_POST['userID']) : '';
            if($userID!=NULL){// checking for required fields
                $accessToken=0;
                $isUserExist = $this->api->isUserExist($userID);
                if($isUserExist){
                    //$arrResponse = array('status'=>1, 'message'=>'accessToken is Valid','getAccessToken'=>$getAccessToken,'accessToken'=>$accessToken);
                    $headers = apache_request_headers();
                    foreach ($headers as $key => $value) {
                        switch ($key) {
                            case 'accesstoken':
                            $accessToken = $value;
                            break;
                        }
                    }
                    $getAccessToken = $this->api->getAccessToken($userID);
                    $getAccessToken = $getAccessToken['accessToken'];
                    if(strcmp($getAccessToken,$accessToken)==0){// chcking if accesstoken is 
                        $userNotificationResult = $this->api->getUserNotification($userID);
                        if(!empty($userNotificationResult)){

                            $this->api->updateWishVideoNotification($userNotificationResult);

                            $arrResponse = array('status'=>1, 'message'=>'success', 'notification'=>$userNotificationResult);
                        }else{
                            $arrResponse = array('status'=>2, 'message'=>'Currently No Notification');
                        }
                    }else{
                        $arrResponse = array('status'=>0, 'message'=>'Some fields are missing');
                    } 
                }else{
                    $arrResponse = array('status'=>0, 'message'=>'Please Login.'); 
                }
                
            }else{
                $arrResponse = array('status'=>0, 'message'=>'userID is required');
            }
        }else{
            $arrResponse = array('status'=>0, 'message'=>'requested method is not accepted');
        }
        echo json_encode($arrResponse);
    }

    public function searchVideo() {
        $arrResponse = array();
        if($_SERVER['REQUEST_METHOD'] == "POST"){

            $db=$this->db->conn_id;//gives database conn variable to be used in mysqli_real_escape_string()

            //getting post data
            $userID = isset($_POST['userID']) ? mysqli_real_escape_string($db,$_POST['userID']) : '';
            $keyWord = isset($_POST['keyWord']) ? mysqli_real_escape_string($db,$_POST['keyWord']) : '';
            //$limit = isset($_POST['limit']) ? mysqli_real_escape_string($db,$_POST['limit']) : '';
            $limit = 25;
            $offset = isset($_POST['nextVideo']) ? mysqli_real_escape_string($db,$_POST['nextVideo']) : '';
            $accessToken = 0;
            $isUserExist = $this->api->isUserExist($userID);
            if(($isUserExist)||($userID==0)){
                if($userID!=NULL && $limit!=NULL && $offset!=NULL && $keyWord!=NULL){// checking for required fields
                    $headers = apache_request_headers();
                    foreach ($headers as $key => $value) {
                        switch ($key) {
                            case 'accesstoken':
                            $accessToken = $value;
                            break;
                        }
                    }
                    $getAccessToken = $this->api->getAccessToken($userID);
                    $getAccessToken = $getAccessToken['accessToken'];
                    if((strcmp($getAccessToken,$accessToken)==0)||($userID==0 && $accessToken=='10cd14cdb637ab82fa37b70055062b1b')){// chcking if accesstoken is valid
                        //main codingstarts if access token is valic
                        $created = date('Y-m-d H:i:s');
                        $this->api->storeSearchKeyword($userID,$keyWord,$created);

                        list($getUserVideo,$nextVideo) = $this->api->searchVideo($userID,$limit,$offset,$keyWord);


                        $arrResponse = array('status'=>1, 'message'=>'success','nextVideo'=>$nextVideo,'video'=>$getUserVideo); 
                    }else{
                        $arrResponse = array('status'=>0, 'message'=>'Some fields are missing');
                    }
                }else{
                    $arrResponse = array('status'=>0, 'message'=>'userID and nextVideo offset is required');
                }
            }else{
                $arrResponse = array('status'=>0, 'message'=>'Error : Invalid userID '); 
            }
        }else{
            $arrResponse = array('status'=>0, 'message'=>'requested method is not accepted');
        }
        echo json_encode($arrResponse);
    }


    // public function getCountry(){

    // }

    public function getCountry() {
        $arrResponse = array();
        if($_SERVER['REQUEST_METHOD'] == "POST"){

            $db=$this->db->conn_id;//gives database conn variable to be used in mysqli_real_escape_string()

            //getting post data
            $userID = isset($_POST['userID']) ? mysqli_real_escape_string($db,$_POST['userID']) : '';
            if($userID!=NULL){// checking for required fields
                $accessToken=0;
                $isUserExist = $this->api->isUserExist($userID);
                if($isUserExist){
                    //$arrResponse = array('status'=>1, 'message'=>'accessToken is Valid','getAccessToken'=>$getAccessToken,'accessToken'=>$accessToken);
                    $headers = apache_request_headers();
                    foreach ($headers as $key => $value) {
                        switch ($key) {
                            case 'accesstoken':
                            $accessToken = $value;
                            break;
                        }
                    }
                    $getAccessToken = $this->api->getAccessToken($userID);
                    $getAccessToken = $getAccessToken['accessToken'];
                    if(strcmp($getAccessToken,$accessToken)==0){// chcking if accesstoken is valid
                        //main codingstarts if access token is valic

                        //Check if userID is active
                        //if not active==true; userID = newID pass that id and data

                        // $this->api->getCountryList();
                        $getCountryList = $this->api->getCountryList();
                        $arrResponse = array('status'=>1, 'message'=>'success', 'countryList'=> $getCountryList); 
                    }else{
                        $arrResponse = array('status'=>0, 'message'=>'Some fields are missing');
                    } 
                }else{
                    $arrResponse = array('status'=>0, 'message'=>'Error : Invalid userID '); 
                }
                
            }else{
                $arrResponse = array('status'=>0, 'message'=>'userID is required');
            }
        }else{
            $arrResponse = array('status'=>0, 'message'=>'requested method is not accepted');
        }
        echo json_encode($arrResponse);
    }

    public function getState() {
        $arrResponse = array();
        if($_SERVER['REQUEST_METHOD'] == "POST"){

            $db=$this->db->conn_id;//gives database conn variable to be used in mysqli_real_escape_string()

            //getting post data
            $userID = isset($_POST['userID']) ? mysqli_real_escape_string($db,$_POST['userID']) : '';
            $countryID = isset($_POST['countryID']) ? mysqli_real_escape_string($db,$_POST['countryID']) : '';

            if($userID!=NULL){// checking for required fields
                $accessToken=0;
                $isUserExist = $this->api->isUserExist($userID);
                if($isUserExist){
                    //$arrResponse = array('status'=>1, 'message'=>'accessToken is Valid','getAccessToken'=>$getAccessToken,'accessToken'=>$accessToken);
                    $headers = apache_request_headers();
                    foreach ($headers as $key => $value) {
                        switch ($key) {
                            case 'accesstoken':
                            $accessToken = $value;
                            break;
                        }
                    }
                    $getAccessToken = $this->api->getAccessToken($userID);
                    $getAccessToken = $getAccessToken['accessToken'];
                    if(strcmp($getAccessToken,$accessToken)==0){// chcking if accesstoken is valid
                        //main codingstarts if access token is valic

                        //Check if userID is active
                        //if not active==true; userID = newID pass that id and data

                        $getStateList = $this->api->getStateList($countryID);
                        $arrResponse = array('status'=>1, 'message'=>'success', 'stateList'=> $getStateList); 
                    }else{
                        $arrResponse = array('status'=>0, 'message'=>'Some fields are missing');
                    } 
                }else{
                    $arrResponse = array('status'=>0, 'message'=>'Error : Invalid userID '); 
                }
                
            }else{
                $arrResponse = array('status'=>0, 'message'=>'userID is required');
            }
        }else{
            $arrResponse = array('status'=>0, 'message'=>'requested method is not accepted');
        }
        echo json_encode($arrResponse);
    }


    public function forgotPassword() {
        $this->load->helper('form');
        $this->load->view('forgotPassword');
    }

    public function doForgotPassword() {
        if($_POST){
            $email = $this->input->post('email');
            //check if this email exists in
            $email = strtolower($email);
            $is_email_exist = $this->api->is_email_exist($email);

            if($is_email_exist){
                //email present in system
                $userDataArray = $this->api->getUserIDviaEmail($email);
                $forgotPassUserID = $userDataArray[0]['userID'];
                $forgotPassFullName = $userDataArray[0]['fullName'];
                $password = random_string('alnum', 32).date('Y-m-d H:i:s').$forgotPassUserID;
                $md5ForgotPassword = md5($password);
                //store it in db and send email
                $userData = array();
                $userData['forgotPassword']=$md5ForgotPassword;
                $result = $this->api->updateUser($forgotPassUserID,$userData);
                
                $emailSubject = 'Rockabyte Password Reset Link ';

                $passwordResetLink = site_url('api/enterNewPassword/').$md5ForgotPassword;

                // $emailBody = '<p>Hi '.$forgotPassFullName.' </p>
                // <p>&nbsp;</p>
                // <p>&nbsp;</p>
                // <p>We\'ve received a request to reset your password. if you didn\'t make the request,</p>
                // <p>just ignore this email. otherwise, you can reset your password using this link:</p>
                // <p><a href="'.$passwordResetLink.'">'.$passwordResetLink.'</a>&nbsp;</p>
                // <p>&nbsp;</p>
                // <p>&nbsp;</p>
                // <p>Thanks,</p>
                // <p>Rockabyte Team</p>';

                $emailBody = '<div class="holder" style="width:500px;height: 100%;left: 0;right: 0;
            color: white;margin: auto;top: 0;bottom: 0;background-color: #008780;">
                    <div class="logoHolder" style="position: relative;font-family: Arial,sans-serif;">
                        <img src="http://faarbetterfilms.com/rockabyteServicesV2/assets/images/logowhite.png" class="logoClass" style="width: 80px;position: relative;">
                        <div class="textOnImg" style="position: relative;color: white;top: 0px;left: -3px;
                        font-size: 10px;letter-spacing: 2px;width: 80px;text-align: center;">
                            <span> <strong>ROCKA</strong><br> BYTE</span>
                        </div>
                    
                    </div>
                    <div class="content" style="padding: 10px;text-align: center;padding-bottom: 50px;width:280px;left:0;right:0;margin:auto;">
                        <h4 style="font-family:Arial,sans-serif;font-size: 16px;font-weight: 400;margin: 10px 0;color:white !important;">Hi '.$forgotPassFullName.' <br>You recently requested to reset your password for your Rockabyte account. Use the link below to reset it.</h4>
                        <br>
                        <a href="'.$passwordResetLink.'" style="font-size: 12px !important; font-family: Arial,sans-serif;font-weight: 400;word-break: break-all;color: #0000ee;" target="_blank">'.$passwordResetLink.'</a>
                        <br>
                    </div>
                    <div class="footerHolder" style="text-align: center;padding:20px;width:260px;left:0;right:0;margin:auto;">
                        <span class="rightReserved" style="color: #aaa7a7;font-size: 10px;font-family: Arial,sans-serif;">  
                            &copy; 2017 RockaByte. All right reserved.
                        </span>
                        <br>
                        <span class="notReply" style="font-size: 10px;font-family: Arial,sans-serif;color:white !important;">Please do not reply to this email as it is a computer generated mail.</span>
                    </div>
                </div>';
                //echo $emailBody;exit;
                $this->api->sendEmail($email, $emailSubject, $emailBody);
                redirect('api/forgotPasswordLinkSent');

            } else {
                //email not present in system
                $this->session->set_flashdata('emailNotInSystem', 'Sorry, We didn\'t recognize that email.');
                redirect('api/forgotPassword');
            }
        }else{
            redirect('api/forgotPassword');
        }
    }

    public function forgotPasswordLinkSent(){
        $this->load->view('forgotPasswordLinkSent');
    }

    public function enterNewPassword($urlvalue){
        $this->load->helper('form');
        $userdata = $this->api->forgotPasswordUserID($urlvalue);
        if($userdata){
            $this->load->view('enterNewPassword',['userData'=>$userdata]);
        }else{
            echo "<h2>Invalid password Reset request</h2>";
        }
        //$this->load->view('');
    }


    public function doEnterNewPassword(){
        if($_POST){
            $arraReturn = array('txtsuccess'=>true,'txtmessage'=>'');
            $password = $this->input->post('password');
            $userID = $this->input->post('userID');
            //$forgotPassword = $this->input->post('forgotPassword');
            //check if this email exists in
            $userData = array();

           
            $hashPassword = password_hash($password, PASSWORD_DEFAULT);
            $userData['password'] = $hashPassword;

            $result = $this->api->updateUser($userID,$userData);
            if($result){
                $userData['forgotPassword'] = '';
                $this->api->updateUser($userID,$userData);
                $arraReturn['txtsuccess'] = true;

                //show modal 
            }else{
                $arraReturn['txtsuccess'] = false;
            }
        }

        echo json_encode($arraReturn);
    }


    public function passwordUpdatedSuccessFully(){
        $this->load->view('passwordUpdated');
    }

    public function editUserProfilePicture() {
        $arrResponse = array();
        if($_SERVER['REQUEST_METHOD'] == "POST"){

            $db=$this->db->conn_id;//gives database conn variable to be used in mysqli_real_escape_string()

            //getting post data
            $userID = isset($_POST['userID']) ? mysqli_real_escape_string($db,$_POST['userID']) : '';
            //$userID=10;
            //$userID = isset($_POST['userID']) ? mysqli_real_escape_string($db,$_POST['userID']) : '';
            if($userID!=NULL){// checking for required fields
                $accessToken=0;
                $isUserExist = $this->api->isUserExist($userID);
                if($isUserExist){
                    //$arrResponse = array('status'=>1, 'message'=>'accessToken is Valid','getAccessToken'=>$getAccessToken,'accessToken'=>$accessToken);
                    $headers = apache_request_headers();
                    foreach ($headers as $key => $value) {
                        switch ($key) {
                            case 'accesstoken':
                            $accessToken = $value;
                            break;
                        }
                    }
                    $getAccessToken = $this->api->getAccessToken($userID);
                    $getAccessToken = $getAccessToken['accessToken'];
                    if(strcmp($getAccessToken,$accessToken)==0){// chcking if accesstoken is 
                        if (isset($_FILES['imageUpload']['name']) && !empty($_FILES['imageUpload']['name'])) {
                            $dateCurrent = date("Y-M-d H:i:s");
                            $img_name = $userID.$dateCurrent.random_string('alnum') .random_string('unique'). str_replace(' ', '_', $_FILES['imageUpload']['name']);

                            $image_path = 'uploads/userProfilePicture';
                            $config['upload_path'] = $image_path;
                            $config['file_name'] = $img_name;
                            $config['overwrite'] = FALSE;
                            $config["allowed_types"] = 'gif|jpg|png|jpeg';
                            $config['encrypt_name'] = TRUE;
                            $config['remove_spaces'] = TRUE;
                            $this->load->library('upload', $config);

                            if ( ! $this->upload->do_upload('imageUpload')){
                                $error = array('error' => $this->upload->display_errors());
                                $arrResponse = array('status' => 0, 'message' => 'Upload error.', 'error' => $error);
                            }else{
                                $data = $this->upload->data();
     
                                $fullPath = base_url() .'uploads/userProfilePicture/'. $data['file_name'];

                                $updateData = array(
                                    'profilePic' => $fullPath,
                                    );
                                $success = $this->api->updateUser($userID,$updateData);

                                $arrResponse = array('status' => 1, 'message' => 'Profile Picture Updated Successfully','profilePic'=>$fullPath);
                            }
                        }else{
                            $arrResponse = array('status' => 0, 'message' => 'Please select image');
                        }
                    }else{
                        $arrResponse = array('status'=>0, 'message'=>'Some fields are missing');
                    } 
                }else{
                    $arrResponse = array('status'=>0, 'message'=>'Error : Invalid userID '); 
                }
                
            }else{
                $arrResponse = array('status'=>0, 'message'=>'userID is required');
            }
        }else{
            $arrResponse = array('status'=>0, 'message'=>'requested method is not accepted');
        }
        echo json_encode($arrResponse);
    }

    public function deleteVideo() {
        $arrResponse = array();
        if($_SERVER['REQUEST_METHOD'] == "POST"){

            $db=$this->db->conn_id;//gives database conn variable to be used in mysqli_real_escape_string()

            //getting post data
            $userID = isset($_POST['userID']) ? mysqli_real_escape_string($db,$_POST['userID']) : '';
            $videoID = isset($_POST['videoID']) ? mysqli_real_escape_string($db,$_POST['videoID']) : '';

            if($userID!=NULL && $videoID!=NULL){// checking for required fields
                $accessToken=0;
                $isUserExist = $this->api->isUserExist($userID);
                if($isUserExist){
                    //$arrResponse = array('status'=>1, 'message'=>'accessToken is Valid','getAccessToken'=>$getAccessToken,'accessToken'=>$accessToken);
                    $headers = apache_request_headers();
                    foreach ($headers as $key => $value) {
                        switch ($key) {
                            case 'accesstoken':
                            $accessToken = $value;
                            break;
                        }
                    }
                    $getAccessToken = $this->api->getAccessToken($userID);
                    $getAccessToken = $getAccessToken['accessToken'];
                    if(strcmp($getAccessToken,$accessToken)==0){// chcking if accesstoken is valid
                        //main codingstarts if access token is valic
                        //update video data
                        $updateData = array('isActive' => '0');
                        $updateVideoData = $this->api->updateVideo($userID,$videoID,$updateData);
                        $arrResponse = array('status'=>1, 'message'=>'Video Deleted Successfully.');
                    }else{
                        $arrResponse = array('status'=>0, 'message'=>'Some fields are missing');
                    } 
                }else{
                    $arrResponse = array('status'=>0, 'message'=>'Error : Invalid userID '); 
                }
                
            }else{
                $arrResponse = array('status'=>0, 'message'=>'userID and videoID is required');
            }
        }else{
            $arrResponse = array('status'=>0, 'message'=>'requested method is not accepted');
        }
        echo json_encode($arrResponse);
    }


    public function notificationBadgeCount() {
        $arrResponse = array();
        if($_SERVER['REQUEST_METHOD'] == "POST"){

            $db=$this->db->conn_id;//gives database conn variable to be used in mysqli_real_escape_string()

            //getting post data
            $userID = isset($_POST['userID']) ? mysqli_real_escape_string($db,$_POST['userID']) : '';
            // $lastCount = isset($_POST['lastCount']) ? mysqli_real_escape_string($db,$_POST['lastCount']) : '';

            if($userID!=NULL){// checking for required fields
                $accessToken=0;
                $isUserExist = $this->api->isUserExist($userID);
                if($isUserExist){
                    //$arrResponse = array('status'=>1, 'message'=>'accessToken is Valid','getAccessToken'=>$getAccessToken,'accessToken'=>$accessToken);
                    $headers = apache_request_headers();
                    foreach ($headers as $key => $value) {
                        switch ($key) {
                            case 'accesstoken':
                            $accessToken = $value;
                            break;
                        }
                    }
                    $getAccessToken = $this->api->getAccessToken($userID);
                    $getAccessToken = $getAccessToken['accessToken'];
                    if(strcmp($getAccessToken,$accessToken)==0){// chcking if accesstoken is valid
 
                        $userNotificationResult = $this->api->getUserNotification($userID);

                        $count = 0;
                        foreach ($userNotificationResult as $value) {
                            if($value['isRead']==0){
                                $count = $count + 1;
                            }
                        }
                        // echo $count;exit;
                        // $totalCount = count($userNotificationResult);
                        // $endSplice = $totalCount-$lastCount;

                        // $finalArray = array_splice($userNotificationResult,0,$endSplice);

                        $arrResponse = array('status'=>1, 'message'=>'success', 'badgeCount'=>$count);
                    }else{
                        $arrResponse = array('status'=>0, 'message'=>'Some fields are missing');
                    } 
                }else{
                    $arrResponse = array('status'=>0, 'message'=>'Error : Invalid userID '); 
                }
            }else{
                $arrResponse = array('status'=>0, 'message'=>'userID, lastCount is required');
            }
        }else{
            $arrResponse = array('status'=>0, 'message'=>'requested method is not accepted');
        }
        echo json_encode($arrResponse);
    }


    // public function replaceSpaceEmail(){
    //     $this->api->replaceSpaceEmail();
    // }

    public function videoCompressionTesting(){

        $input = '/var/www/html/rockabyte/admin/uploads/video/S2ZIU1px20160324_201621.mp4';
        $output = '/var/www/html/rockabyteV1/uploads/video/testconvert1.mp4';
        $command="ffmpeg -i {$input} -c:v libx264 -preset slow -crf 22 -c:a copy {$output}";

        // exec('ln -s ' . PLUGIN_DIR . '/.htaccess ' . ABSPATH . '/.htaccess' . '2>&1',$output);

        'ffmpeg -i /home/codetreasure/Downloads/largeport.mp4 -vcodec h264 -s hd720 -acodec aac -strict -2 /home/codetreasure/Downloads/thumbnail/largeport3.mp4';
        exec($command,$error);
        var_dump($error);
    }


    public function updateConvertedVideoLink(){
        $this->api->updateConvertedVideoLink();
        $this->api->updateConvertedWishVideoLink();
    }


    // public function updateConvertedWishVideoLink(){

    // }

}
