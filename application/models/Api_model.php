<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_Model extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
        date_default_timezone_set('Asia/Calcutta');
        $this->load->helper('url');
        $this->load->database();
        date_default_timezone_set("Asia/Kolkata");

    }

    public function loginNewTest($userName, $password) {
        $email = strtolower($userName);
        $sql = "SELECT * FROM userProfile WHERE (userName = ? or email = ?) AND password <> ''";
        $query = $this->db->query($sql,array($userName,$email));

        if($query->num_rows() == 1) {
            $res = $query->row_array();
            //$response['userID'] = $res['userID'];
            $hashPass = $res['password'];

            if (password_verify($password, $hashPass)) {
                // echo "The posted password matches the hashed one";
                $response['userID'] = $res['userID'];
            }
            else {
                $response['userID'] = 0;
            }

        } else {
            $response['userID'] = 0;
        }

        return $response;
    }

    public function getAccessToken($userID){
        $sql = "SELECT accessToken FROM userProfile WHERE userID = $userID";
        $query = $this->db->query($sql,array($userID));
        $res = $query->row_array();
        return $res;
    }

    public function getUserData2($userID){
        $sql = "SELECT
        userID,
        fullName,
        userName,
        occupation,
        phoneNo,
        gender,
        email,
        profilePic,
        country,
        dob,
        state,
        aboutMe,
        androidKey,
        iosKey,
        isActive

        FROM userProfile WHERE userID = ?";
        $query = $this->db->query($sql,array($userID));
        $res = $query->row_array();
        // $userData = array(
        //         "fullName"=>$res['fullName'];
        //     );

        $socialLoginData = $this->socialLoginData($res['userID']);

        $result = array(
            'userID'        => $res['userID'],
            'fullName'      => $res['fullName'],
            'userName'      => $res['userName'],
            'occupation'    => $res['occupation'],
            'phoneNo'       => $res['phoneNo'],
            'gender'        => $res['gender'],
            'email'         => $res['email'],
            'profilePic'    => $res['profilePic'],
            'country'       => $res['country'],
            'dob'           => $res['dob'],
            'state'         => $res['state'],
            'aboutMe'       => $res['aboutMe'],
            'androidKey'    => $res['androidKey'],
            'iosKey'        => $res['iosKey'],
            'isActive'      => $res['isActive'],
            'syncAccount'   => $socialLoginData
            );


        return $result;
    }

    public function getUserDataProfileData($userID){
        $sql = "SELECT
        userID,
        fullName,
        userName,
        occupation,
        phoneNo,
        gender,
        email,
        profilePic,
        country,
        dob,
        state,
        aboutMe,
        androidKey,
        iosKey,
        isActive

        FROM userProfile WHERE userID = ?";
        $query = $this->db->query($sql,array($userID));
        $res = $query->row_array();
        // $userData = array(
        //         "fullName"=>$res['fullName'];
        //     );

        $socialLoginData = $this->socialLoginData($res['userID']);

        // following count
        $followingSql = "SELECT COUNT(*) as followingCount FROM `following` WHERE userID = ? AND isFollowing = '1'";
        $followingQuery = $this->db->query($followingSql,array($userID));

        if($followingQuery->num_rows()>0){
            $followingData = $followingQuery->row();
            $followingCount = $followingData->followingCount;
        } else {
             $followingCount = '0';
        }

        // follower count
        $followerSql = "SELECT COUNT(*) as followerCount FROM `following` WHERE followUserID = ? AND isFollowing = '1'";
        $followerQuery = $this->db->query($followerSql,array($userID));

        if($followerQuery->num_rows()>0){
            $followerData = $followerQuery->row();
            $followerCount = $followerData->followerCount;
        } else {
             $followerCount = '0';
        }

        $result = array(
            'isVerified'    => '0',
            'userID'        => $res['userID'],
            'fullName'      => $res['fullName'],
            'userName'      => $res['userName'],
            'occupation'    => $res['occupation'],
            'phoneNo'       => $res['phoneNo'],
            'gender'        => $res['gender'],
            'email'         => $res['email'],
            'profilePic'    => $res['profilePic'],
            'country'       => $res['country'],
            'dob'           => $res['dob'],
            'state'         => $res['state'],
            'aboutMe'       => $res['aboutMe'],
            'androidKey'    => $res['androidKey'],
            'iosKey'        => $res['iosKey'],
            'isActive'      => $res['isActive'],
            'followerCount' => $followerCount,
            'followingCount' => $followingCount,
            'syncAccount'   => $socialLoginData
            );


        return $result;
    }


    public function getUserDataFollow($userID ,$profileUserID){
        $sql = "SELECT
        userID,
        fullName,
        userName,
        occupation,
        phoneNo,
        gender,
        email,
        profilePic,
        country,
        dob,
        state,
        aboutMe,
        androidKey,
        iosKey,
        isActive

        FROM userProfile WHERE userID = ?";
        $query = $this->db->query($sql,array($profileUserID));
        $res = $query->row_array();
        // $userData = array(
        //         "fullName"=>$res['fullName'];
        //     );

        $socialLoginData = $this->socialLoginData($res['userID']);

        // Deciding wether that user follows the other user or not
        $sql = "SELECT * FROM following WHERE userID = ? AND followUserID = ?";
        $query = $this->db->query($sql, array($userID, $profileUserID));
        $isFollowing = '';
        if($query->num_rows()>0){
            $followingData = $query->row_array();
            $isFollowing = $followingData['isFollowing'];
        } else {
            $isFollowing = '0';
        }

        // following count
        $followingSql = "SELECT COUNT(*) as followingCount FROM `following` WHERE userID = ? AND isFollowing = '1'";
        $followingQuery = $this->db->query($followingSql,array($profileUserID));

        if($followingQuery->num_rows()>0){
            $followingData = $followingQuery->row();
            $followingCount = $followingData->followingCount;
        } else {
             $followingCount = '0';
        }

        // follower count
        $followerSql = "SELECT COUNT(*) as followerCount FROM `following` WHERE followUserID = ? AND isFollowing = '1'";
        $followerQuery = $this->db->query($followerSql,array($profileUserID));

        if($followerQuery->num_rows()>0){
            $followerData = $followerQuery->row();
            $followerCount = $followerData->followerCount;
        } else {
             $followerCount = '0';
        }

        $result = array(
            'isFollowing'   => $isFollowing,
            'isVerified'    => '0',
            'userID'        => $res['userID'],
            'fullName'      => $res['fullName'],
            'userName'      => $res['userName'],
            'occupation'    => $res['occupation'],
            'phoneNo'       => $res['phoneNo'],
            'gender'        => $res['gender'],
            'email'         => $res['email'],
            'profilePic'    => $res['profilePic'],
            'country'       => $res['country'],
            'dob'           => $res['dob'],
            'state'         => $res['state'],
            'aboutMe'       => $res['aboutMe'],
            'androidKey'    => $res['androidKey'],
            'iosKey'        => $res['iosKey'],
            'isActive'      => $res['isActive'],
            'followerCount' => $followerCount,
            'followingCount' => $followingCount,
            'syncAccount'   => $socialLoginData
            );


        return $result;
    }

    public function getSocialLoginData($userID){
        $sql = "SELECT socialType, socialID FROM socialLogin WHERE userID = ?";
        $query = $this->db->query($sql,array($userID));
        $res = $query->result();
        return $res;
    }

    public function socialLoginData($userID){
        $facebookStatus = $this->socialLoginDataCheck($userID,'facebook');
        $twitterStatus = $this->socialLoginDataCheck($userID,'twitter');
        $googleStatus = $this->socialLoginDataCheck($userID,'google');
        $res = array(
            'facebook' => $facebookStatus,
            'twitter' => $twitterStatus,
            'google' => $googleStatus,
            );
        return $res;
    }

    private function socialLoginDataCheck($userID,$socialType){
        $sql = "SELECT socialType, socialID FROM socialLogin WHERE userID = ? AND socialType = ?";
        $query = $this->db->query($sql,array($userID,$socialType));
        return ($query->num_rows() > 0) ? 1 : 0;
    }

    public function ifSocialIDExist($socialID){
        $sql= "SELECT userID FROM `socialLogin` WHERE socialID=?";
        $query = $this->db->query($sql,array($socialID));
        if($query->num_rows()>0){
            $res = $query->row_array();
            return $res;
        } else {
            return false;
        }
    }

    // public function getAccessToken($userID){
    //     $sql = "SELECT accessToken FROM userProfile WHERE userID = $userID";
    //     $query = $this->db->query($sql,array($userID));
    //     $res = $query->row_array();
    //     return $res;
    // }

    public function registeruser($insertData) {
        $this->db->insert('userProfile', $insertData);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function updateUser($userID,$updateData){
        $this->db->where('userID', $userID);
        $this->db->update('userProfile', $updateData);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function insertSocialLogin($insertData){
        $this->db->insert('socialLogin',$insertData);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function updateAccessToken($accessToken,$userID) {
        $sql = "UPDATE userProfile SET accessToken= ? WHERE userID= ?";
        $query = $this->db->query($sql,array($accessToken,$userID));
    }
    public function checkIfPhoneNoExists($userID){
        $sql = "SELECT phoneNo FROM userProfile WHERE userID = ?";
        $query = $this->db->query($sql,array($userID));

        $res = $query->row_array();
        $phoneNo=$res['phoneNo'];

        if ($phoneNo == '') {
            return true;
        } else {
            return false;
        }
    }

    public function checkIfFullNameExists($userID){
        $sql = "SELECT fullName FROM userProfile WHERE userID = ?";
        $query = $this->db->query($sql,array($userID));
        $res = $query->row_array();
        $fullName=$res['fullName'];

        if ($fullName == '') {
            return true;
        } else {
            return false;
        }
    }

    public function checkIfDobExists($userID){
        $sql = "SELECT dob FROM userProfile WHERE userID = ?";
        $query = $this->db->query($sql,array($userID));
        $res = $query->row_array();
        $dob=$res['dob'];
        if ($dob == '') {
            return true;
        } else {
            return false;
        }
    }

    public function checkIfProfilePicExists($userID){
        $sql = "SELECT profilePic FROM userProfile WHERE userID = ?";
        $query = $this->db->query($sql,array($userID));
        $res = $query->row_array();
        $profilePic=$res['profilePic'];

        if ($profilePic == '') {
            return true;
        } else {
            return false;
        }
    }

    public function checkIfGenderExists($userID){
        $sql = "SELECT gender FROM userProfile WHERE userID = ?";
        $query = $this->db->query($sql,array($userID));

        $res = $query->row_array();
        $gender=$res['gender'];

        if ($gender == '') {
            return true;
        } else {
            return false;
        }
    }

    public function checkIfAndroidKeyExists($userID){
        $sql = "SELECT androidKey FROM userProfile WHERE userID = ?";
        $query = $this->db->query($sql,array($userID));

        $res = $query->row_array();
        $androidKey=$res['androidKey'];

        if ($androidKey == '') {
            return true;
        } else {
            return false;
        }
    }

    public function checkIfIosExists($userID){
        $sql = "SELECT iosKey FROM userProfile WHERE userID = ?";
        $query = $this->db->query($sql,array($userID));

        $res = $query->row_array();
        $iosKey=$res['iosKey'];

        if ($iosKey == '') {
            return true;
        } else {
            return false;
        }
    }

    public function updatePhoneNo($phoneNo,$userID){
        $sql = "UPDATE userProfile SET phoneNo= ? WHERE userID=?";
        $query = $this->db->query($sql,array($phoneNo,$userID));
    }

    public function updateFullName($fullName,$userID){
        $sql = "UPDATE userProfile SET fullName= ? WHERE userID=?";
        $query = $this->db->query($sql,array($fullName,$userID));
    }

    public function updateDob($dob,$userID){
        $sql = "UPDATE userProfile SET dob= ? WHERE userID=?";
        $query = $this->db->query($sql,array($dob,$userID));
    }

    public function updateProfilePic($profilePic,$userID){
        $sql = "UPDATE userProfile SET profilePic= ? WHERE userID=?";
        $query = $this->db->query($sql,array($profilePic,$userID));
    }

    public function updateAndroidKey($androidKey,$userID){
        $sql = "UPDATE userProfile SET androidKey= ? WHERE userID=?";
        $query = $this->db->query($sql,array($androidKey,$userID));
    }

    public function updateIosKey($iosKey,$userID){
        $sql = "UPDATE userProfile SET iosKey= ? WHERE userID=?";
        $query = $this->db->query($sql,array($iosKey,$userID));
    }

    public function updateGender($gender,$userID){
        $sql = "UPDATE userProfile SET gender= ? WHERE userID=?";
        $query = $this->db->query($sql,array($gender,$userID));
    }

    public function updateUserIDAll($oldUserID,$newUserID){
        // UPDATE table_name
        // SET column1=value1,column2=value2,...
        // WHERE some_column=some_value;

        // Table SocialLogin
        $sql = "UPDATE socialLogin SET userID = ? WHERE userID = ?";
        $query = $this->db->query($sql,array($newUserID,$oldUserID));
        $affectedQuery = $this->db->affected_rows();

        //Table UserProfile
        $sql2 = "UPDATE userProfile SET `isActive` = '0',`newID` = ? WHERE userID = ?";
        $query2 = $this->db->query($sql2,array($newUserID,$oldUserID));
        $affectedQuery2 = $this->db->affected_rows();

        //update table userProfile `newID`
        $sql5 = "UPDATE userProfile SET `newID` = ? WHERE isActive = ? AND newID = ? ";
        $query5 = $this->db->query($sql5,array($newUserID,'0',$oldUserID));
        $affectedQuery5 = $this->db->affected_rows();

        //userInterest
        $sql3 = "UPDATE userInterest SET userID = ? WHERE userID = ?";
        $query3 = $this->db->query($sql3,array($newUserID,$oldUserID));
        $affectedQuery3 = $this->db->affected_rows();

        //video
        $sql4 = "UPDATE video SET userID = ? WHERE userID = ?";
        $query4 = $this->db->query($sql4,array($newUserID,$oldUserID));
        $affectedQuery4 = $this->db->affected_rows();

        //userLikeVideo
        $sql6 = "UPDATE userLikeVideo SET userID = ? WHERE userID = ?";
        $query6 = $this->db->query($sql6,array($newUserID,$oldUserID));
        $affectedQuery6 = $this->db->affected_rows();

        //videoView
        $sql7 = "UPDATE videoView SET userID = ? WHERE userID = ?";
        $query7 = $this->db->query($sql7,array($newUserID,$oldUserID));
        $affectedQuery7 = $this->db->affected_rows();

        //userIDUpdateLogs NOW()
        $datetime = date("Y-m-d h:i:s");
        $sql8 = "INSERT INTO userIDUpdateLogs (newID,oldID,created) VALUES (?,?,'$datetime')";
        $query8 = $this->db->query($sql8,array($newUserID,$oldUserID));
        $affectedQuery8 = $this->db->affected_rows();

        //wishVideo
        $sql9 = "UPDATE wishVideo SET userID = ? WHERE userID = ?";
        $query9 = $this->db->query($sql9,array($newUserID,$oldUserID));
        $affectedQuery9 = $this->db->affected_rows();

        //searchHistory
        $sql10 = "UPDATE searchHistory SET userID = ? WHERE userID = ?";
        $query10 = $this->db->query($sql10,array($newUserID,$oldUserID));
        $affectedQuery10 = $this->db->affected_rows();

        //searchHistory
        $sql11 = "UPDATE reportVideo SET userID = ? WHERE userID = ?";
        $query11 = $this->db->query($sql11,array($newUserID,$oldUserID));
        $affectedQuery11 = $this->db->affected_rows();

        //following userID
        $sql12 = "UPDATE following SET userID = ? WHERE userID = ?";
        $query12 = $this->db->query($sql12,array($newUserID,$oldUserID));
        $affectedQuery12 = $this->db->affected_rows();

        //following followUserID
        $sql13 = "UPDATE following SET followUserID = ? WHERE followUserID = ?";
        $query13 = $this->db->query($sql13,array($newUserID,$oldUserID));
        $affectedQuery13 = $this->db->affected_rows();




        if($affectedQuery && $affectedQuery2 && $affectedQuery3 && $affectedQuery4 && $affectedQuery5 && $affectedQuery6 && $affectedQuery7){
            return true;
        } else {
            return false;
        }
    }

    public function updateLastLogin($userID){
        $datetime = date("Y-m-d H:i:s");
        $sql = "UPDATE userProfile SET lastLogin = ? WHERE userID = ?";
        $query = $this->db->query($sql,array($datetime,$userID));
    }

    public function is_email_exist($email) {


        $sql   = "SELECT * FROM userProfile WHERE email= ? " ;
        $query = $this->db->query($sql,array($email));


        //$query = $this->db->query("SELECT * FROM user_profile WHERE email='$email' ");
        if ($query->num_rows() >= 1) {
            return true;
        } else {
            return false;
        }
    }

    public function is_userName_exist($userName) {


        $sql   = "SELECT * FROM userProfile WHERE userName= ? " ;
        $query = $this->db->query($sql,array($userName));


        //$query = $this->db->query("SELECT * FROM user_profile WHERE email='$email' ");
        if ($query->num_rows() >= 1) {
            return true;
        } else {
            return false;
        }
    }

    public function is_mobile_no_exist($mobile_no) {
        $sql = "SELECT * FROM userProfile WHERE phoneNo= ? ";
        $query = $this->db->query($sql,array($mobile_no));
        //$query = $this->db->query("SELECT * FROM user_profile WHERE phone_no='$mobile_no' ");
        if ($query->num_rows() >= 1) {
            return true;
        } else {
            return false;
        }
    }

    public function isUserExist($userID){
        //checks if the userID is pesent in userProfile table AND if it is active
        $sql = "SELECT * FROM `userProfile` WHERE userID = ? ";
        $query = $this->db->query($sql,array($userID));
        //$query = $this->db->query("SELECT * FROM user_profile WHERE phone_no='$mobile_no' ");
        if ($query->num_rows() >= 1) {
            return true;
        } else {
            return false;
        }
    }

    // i was here last time
    public function isUserActive($userID){
        $sql = "SELECT * FROM `userProfile` WHERE userID = ? AND isActive = ? ";
        $query = $this->db->query($sql,array($userID,'0'));
        $res = $query->row_array();
        $newID=$res['newID'];
        //$query = $this->db->query("SELECT * FROM user_profile WHERE phone_no='$mobile_no' ");
        if ($query->num_rows() >= 1) {
            return $newID;
        } else {
            return false;
        }
    }

    public function isInterestPresent($userID,$categoryID){
        $sql = "SELECT * FROM userInterest WHERE userID = ? AND categoryID = ?";
        $query = $this->db->query($sql,array($userID,$categoryID));
        if ($query->num_rows() >= 1) {
            return true;
        } else {
            return false;
        }
    }

    public function checkIsEmailFieldEmpty($userID){
        $sql = "SELECT * FROM userProfile WHERE userID = ?";
        $query = $this->db->query($sql,array($userID));
        foreach ($query->result_array() as $value) {
            $email = $value['email'];
        }

        if(empty($email)){
            return true;
        }else{
            return false;
        }
    }

    public function isSelectUpdate($userID,$categoryID,$isSelected){
        $sql = "UPDATE userInterest SET isSelected= ? WHERE userID= ? AND categoryID = ?";
        $query = $this->db->query($sql,array($isSelected,$userID,$categoryID));
        return ($query) ? true : false;
    }

    public function insertUserInterest($userID,$categoryID,$isSelected){
        //         INSERT INTO table_name (column1, column2, column3,...)
        // VALUES (value1, value2, value3,...)
        $datetime = date("Y-m-d h:i:s");
        $sql = "INSERT INTO userInterest (userID,categoryID,isSelected,created)
                VALUES (?,?,?,'$datetime')";
        $query = $this->db->query($sql,array($userID,$categoryID,$isSelected));
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function getUserInterestData($userID){
        $sql = "SELECT
        category.categoryName,
        category.categoryThumbnail,
        category.categoryID,
        userInterest.isSelected

        FROM    category

        INNER JOIN userInterest
        ON userInterest.categoryID = category.categoryID

        WHERE userInterest.userID = ?";
        $query = $this->db->query($sql,array($userID));
        $res = $query->result();
        return $res;
    }

    public function getCategoryList($userID){
        $sql = "SELECT * FROM category";
        $query = $this->db->query($sql);
        //--------------------------------------------
        // $userID = '45';
        // $sql = "SELECT
        // category.categoryName,
        // category.categoryThumbnail,
        // category.categoryID,
        // userInterest.isSelected

        // FROM    category

        // INNER JOIN userInterest
        // ON userInterest.categoryID = category.categoryID

        // WHERE userInterest.userID = ?";
        // $query = $this->db->query($sql,array($userID));
        //--------------------------------------------
        //$res = $query->result();

        // while($row=$query->result_array()){
        //     extract($row);

        //     $result=array(
        //         'categoryID'=>$row['categoryID'],
        //         'categoryName'=>$row['categoryName'],
        //         'categoryThumbnail'=>$row['categoryThumbnail'],
        //         'categoryStatus'=>$row['categoryStatus']
        //     );
        // }

        foreach ($query->result_array() as $row)
        {
            //$userID='45';
            $sql1 = "SELECT isSelected
                FROM userInterest
                WHERE categoryID = ?
                AND userID = ? ";

            $query1 = $this->db->query($sql1,array($row['categoryID'],$userID));
            $row1 = $query1->row();

            if (isset($row1)){
                $isSelected = $row1->isSelected;
            }else{
                $isSelected='';
            }

            $result[]=array(
                'categoryID'=>$row['categoryID'],
                'categoryName'=>$row['categoryName'],
                'categoryThumbnail'=>$row['categoryThumbnail'],
                'isActive'=>$row['isActive'],
                'isSelected'=>$isSelected
            );
        }
        return $result;
    }

    public function getCategoryListUserData($userID){
        //multiple to one data
        $sql = "SELECT * FROM category";
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row)
        {
            //$userID='45';
            $sql1 = "SELECT isSelected
                FROM userInterest
                WHERE categoryID = ?
                AND userID = ? ";

            $query1 = $this->db->query($sql1,array($row['categoryID'],$userID));
            $row1 = $query1->row();

            if (isset($row1)){
                $isSelected = $row1->isSelected;
            }else{
                $isSelected='0';
            }

            //rocka pick setting by default to 1
            $categoryID = $row['categoryID'];
            $categoryName = $row['categoryName'];
            $categoryThumbnail = $row['categoryThumbnail'];
            if($categoryID==1){
                $isSelected = '1';
            }

            $result[]=array(
                'categoryID'=>$categoryID,
                'categoryName'=>$categoryName,
                'categoryThumbnail'=>$categoryThumbnail,
                //'isActive'=>$row['isActive'],
                'isSelected'=>$isSelected
            );
        }
        return $result;
    }

    // public function getUserVideo($userID,$limit,$offset){
    //     //multiple to multiple data
    //     $getUserCategory = $this->getUserCategory($userID);
    //     foreach ($getUserCategory->result_array() as $row)
    //     {
    //         $sql = "SELECT *
    //         FROM video
    //         WHERE categoryID = ? AND isActive = ? LIMIT $limit OFFSET $offset ";

    //         $query1 = $this->db->query($sql,array($row['categoryID'],'1'));
    //         //$row1 = $query1->row();
    //         $result = $query1->result();
    //         //print_r($row1);
    //         //exit;
    //         //you will need foreach here

    //         foreach ($result as $row1)
    //         {
    //             $resultObj[]=array(
    //                 'videoID'=>$row1->videoID,
    //                 'categoryID'=>$row1->categoryID,
    //                 'fileName'=>$row1->fileName,
    //                 'title'=>$row1->title,
    //                 'description'=>$row1->description,
    //                 'tag'=>$row1->tag,
    //                 'view'=>$row1->view,
    //                 'duration'=>$row1->duration,
    //                 'thumbnail'=>$row1->thumbnail,
    //                 'likeCount'=>$row1->likeCount,
    //                 'isRockapick'=>$row1->isRockapick
    //                 );
    //         }
    //         //..you will need foreach here
    //     }
    //     return $resultObj;
    //     // $sql = "SELECT * FROM video";
    //     // $query = $this->db->query($sql);
    // }

    /*------------------------were using it works fine-----------------------------*/
    //changes due to isRockapic
    // public function getUserVideo($userID,$limit,$nextVideo){
    //     //multiple to multiple data
    //     $getUserCategory = $this->getUserCategory($userID);
    //     $nextVideo = $nextVideo - 1;
    //     $prefix  = 'AND (categoryID =';
    //     $string='';
    //     foreach ($getUserCategory->result_array() as $row)
    //     {
    //         $string=$string .= $prefix . ' ' . $row['categoryID'] . ' ';
    //         $prefix = 'or categoryID=';
    //     }
    //     $string=$string.=')';

    //     if($string == ')'){
    //         $string='';
    //     }

    //     $sql = "SELECT *
    //     FROM video
    //     WHERE isActive = 1 $string";
    //     $query = $this->db->query($sql,array('1'));

    //     $result = $query->result();
    //     //print_r($result);
    //     $totalCount = count($result);

    //     $sql = "SELECT *
    //     FROM video
    //     WHERE isActive = 1 $string ORDER by videoID DESC LIMIT $limit OFFSET $nextVideo ";

    //     $query = $this->db->query($sql,array('1'));
    //     $result = $query->result();
    //     $currentLimit = count($result);
    //     $nextVideo +=1;
    //     $nextVideo = $currentLimit + $nextVideo;

    //     /*-------------Getting each category name----------------------------- */
    //     $results = array();
    //     foreach ($query->result_array() as $row)
    //     {
    //         //$userID='45';
    //         $sql1 = "SELECT categoryID,categoryName
    //         FROM category
    //         WHERE categoryID = ?";

    //         $query1 = $this->db->query($sql1,array($row['categoryID']));
    //         $categoryDetail = $query1->row();

    //         //userLIke videos
    //         $likeVideoID =  $row['videoID'];
    //         $likeSql = "SELECT count(*) AS likeCount FROM userLikeVideo WHERE videoID = ? and likeFlag =1";
    //         $likeQuery = $this->db->query($likeSql,array($likeVideoID));
    //         foreach ($likeQuery->result_array() as $likeRow)
    //         {
    //             $likeCount = $likeRow['likeCount'];
    //         }
    //         //.userLike videos

    //         //viewCount
    //         $viewSql = "SELECT count(*) AS ViewCount FROM videoView WHERE videoID = ?";
    //         $viewQuery = $this->db->query($viewSql,array($likeVideoID));
    //         foreach ($viewQuery->result_array() as $viewRow)
    //         {
    //             $ViewCount = $viewRow['ViewCount'];
    //         }

    //         //userID of video uploader detials
    //         $userDetailSql = "SELECT userID,fullName,profilePic,userName
    //         FROM userProfile
    //         WHERE userID = ?";

    //         $userDetailQuery = $this->db->query($userDetailSql,array($row['userID']));
    //         $userData = $userDetailQuery->row();
    //         $userProfileData=$this->api->getUserData2($userID);

    //         // getting video like flag details
    //         $likeFlagSql = "SELECT videoID FROM userLikeVideo  WHERE videoID = ? AND likeFlag = 1 AND userID = ?";
    //         $likeFlagQuery = $this->db->query($likeFlagSql,array($row['videoID'],$userID));
    //         if ($likeFlagQuery->num_rows() >= 1) {
    //             $likeFlag = '1';
    //         } else {
    //             $likeFlag = '0';
    //         }

    //         //converting video tags into an array
    //         $tagString = $row['tag'];
    //         $tagArray = array();
    //         $tagArray = explode(',',$tagString);
    //         if($tagArray[0]==''){
    //             unset($tagArray);
    //             $tagArray = array();
    //         }

    //         //converting duration to mm:ss if hh==00
    //         $duration = $row['duration'];
    //         $durationArray = explode(':',$duration);
    //         if($durationArray[0]=='00'){
    //             $finalDuration = $durationArray[1].':'.$durationArray[2];
    //         }else{
    //             $finalDuration = $durationArray[0].':'.$durationArray[1].':'.$durationArray[2];
    //         }

    //         //$createdTime = $this->time_elapsed_string($row['created']);
    //         if(($row['userID']==$userID) || ($row['isPrivate']=='0')){

    //             $results[]=array(
    //                 'videoID'=>$row['videoID'],
    //                 'userData'=>$userData,
    //                 'category'=>$categoryDetail,
    //                 'videoLink'=>$row['videoLink'],
    //                 'title'=>$row['title'],
    //                 'description'=>str_replace('\\n', "\n",$row['description']),
    //                 'tag'=>$tagArray,
    //                 'viewCount'=>$ViewCount,
    //                 'duration'=>$finalDuration,
    //                 'thumbnail'=>$row['thumbnail'],
    //                 'likeCount'=>$likeCount,
    //                 'likeFlag'=>$likeFlag,
    //                 'isActive'=>$row['isActive'],
    //                 'isRockapick'=>$row['isRockapick'],
    //                 'updated'=>$row['updated'],
    //                 'created'=>$row['created']
    //                 );
    //         }else{
    //            continue;
    //         }
    //     }
    //     /*-------------Getting each category name----------------------------- */

    //     if($nextVideo > $totalCount){
    //         $nextVideo = -1;
    //         return array($results,$nextVideo);
    //     }
    //     //print_r($result);exit;

    //     return array($results,$nextVideo);
    // }
    /*------------------------were using it works fine-----------------------------*/

    public function rockapickVideoCurrentDay()
    {
        $currentDate = date('Y-m-d');
        $sql = "SELECT * FROM rockapickVideo  WHERE rockapickDate = ?";
        $query = $this->db->query($sql,array($currentDate));
        return $query->result_array();
    }
    public function getUserVideo($userID){
        //multiple to multiple data
        $getUserCategory = $this->getUserCategory($userID);
        // $nextVideo = $nextVideo - 1;
        $prefix  = 'AND (categoryID =';
        $string='';
        foreach ($getUserCategory->result_array() as $row)
        {
            $string=$string .= $prefix . ' ' . $row['categoryID'] . ' ';
            $prefix = 'or categoryID=';
        }
        $string=$string.=')';

        if($string == ')'){
            $string='';
        }

        //changing this to select all the videos except which are
        // select all the videos except which are already in rockapickVideo for that date
        $rockapickVideoCurrentDay = $this->rockapickVideoCurrentDay();
        // print_r($rockapickVideoCurrentDay);

        $prefix2  = 'AND (videoID <>';
        $string2='';
        foreach ($rockapickVideoCurrentDay as $row)
        {
            $string2=$string2 .= $prefix2 . ' ' . $row['videoID'] . ' ';
            $prefix2 = 'AND videoID<>';
        }
        $string2=$string2.=')';

        if($string2 == ')'){
            $string2='';
        }

        // print_r($string2);


        $sql = "SELECT *
        FROM video
        WHERE isActive = 1 $string $string2 ORDER by videoID DESC";
        $query = $this->db->query($sql,array('1'));

        // echo $this->db->last_query();exit;
        $result = $query->result();
        //print_r($result);
        $totalCount = count($result);

        // $sql = "SELECT *
        // FROM video
        // WHERE isActive = 1 $string ORDER by videoID DESC LIMIT $limit OFFSET $nextVideo ";

        // $query = $this->db->query($sql,array('1'));
        // $result = $query->result();
        // $currentLimit = count($result);
        // $nextVideo +=1;
        // $nextVideo = $currentLimit + $nextVideo;

        /*-------------Getting each category name----------------------------- */
        $results = array();
        foreach ($query->result_array() as $row)
        {
            //$userID='45';
            $sql1 = "SELECT categoryID,categoryName
            FROM category
            WHERE categoryID = ?";

            $query1 = $this->db->query($sql1,array($row['categoryID']));
            $categoryDetail = $query1->row();

            //userLIke videos
            $likeVideoID =  $row['videoID'];
            $likeSql = "SELECT count(*) AS likeCount FROM userLikeVideo WHERE videoID = ? and likeFlag =1";
            $likeQuery = $this->db->query($likeSql,array($likeVideoID));
            foreach ($likeQuery->result_array() as $likeRow)
            {
                $likeCount = $likeRow['likeCount'];
            }
            //.userLike videos

            //viewCount
            $viewSql = "SELECT count(*) AS ViewCount FROM videoView WHERE videoID = ?";
            $viewQuery = $this->db->query($viewSql,array($likeVideoID));
            foreach ($viewQuery->result_array() as $viewRow)
            {
                $ViewCount = $viewRow['ViewCount'];
            }

            //userID of video uploader detials
            $userDetailSql = "SELECT userID,fullName,profilePic,userName
            FROM userProfile
            WHERE userID = ?";

            $userDetailQuery = $this->db->query($userDetailSql,array($row['userID']));
            $userData = $userDetailQuery->row();
            $userProfileData=$this->api->getUserData2($userID);

            // getting video like flag details
            $likeFlagSql = "SELECT videoID FROM userLikeVideo  WHERE videoID = ? AND likeFlag = 1 AND userID = ?";
            $likeFlagQuery = $this->db->query($likeFlagSql,array($row['videoID'],$userID));
            if ($likeFlagQuery->num_rows() >= 1) {
                $likeFlag = '1';
            } else {
                $likeFlag = '0';
            }

            //converting video tags into an array
            $tagString = $row['tag'];
            $tagArray = array();
            $tagArray = explode(',',$tagString);
            if($tagArray[0]==''){
                unset($tagArray);
                $tagArray = array();
            }

            //converting duration to mm:ss if hh==00
            $duration = $row['duration'];
            $durationArray = explode(':',$duration);
            if($durationArray[0]=='00'){
                $finalDuration = $durationArray[1].':'.$durationArray[2];
            }else{
                $finalDuration = $durationArray[0].':'.$durationArray[1].':'.$durationArray[2];
            }

            //$createdTime = $this->time_elapsed_string($row['created']);
            if(($row['userID']==$userID) || ($row['isPrivate']=='0')){

                $results[]=array(
                    'videoID'=>$row['videoID'],
                    'userData'=>$userData,
                    'category'=>$categoryDetail,
                    'videoLink'=>$row['videoLink'],
                    'title'=>$row['title'],
                    'description'=>str_replace('\\n', "\n",$row['description']),
                    'tag'=>$tagArray,
                    'viewCount'=>$ViewCount,
                    'duration'=>$finalDuration,
                    'thumbnail'=>$row['thumbnail'],
                    'likeCount'=>$likeCount,
                    'likeFlag'=>$likeFlag,
                    'isActive'=>$row['isActive'],
                    'isRockapick'=>$row['isRockapick'],
                    'updated'=>$row['updated'],
                    'created'=>$row['created']
                    );
            }else{
               continue;
            }
        }
        /*-------------Getting each category name----------------------------- */

        // if($nextVideo > $totalCount){
        //     $nextVideo = -1;
        //     return array($results,$nextVideo);
        // }
        // //print_r($result);exit;

        return array($results);
    }

    // public function getRockaPickVideo($todayDate){
    //     // return $todayDate;

    //     $sql = "SELECT * FROM video WHERE isActive = ? AND isPrivate = ? AND isRockapick = ? AND rockapickDate = ?";
    //     $query = $this->db->query($sql,array('1','0','1',$todayDate));
    //     // echo $this->db->last_query();
    //     // print_r($query->result_array());exit;
    //     // if($query->result_array()){
    //     //     return
    //     // }

    //     print_r($query->result_array());
    //     return $query->result_array();
    // }


    public function getRockaPickVideo($todayDate,$userID){
        // return $todayDate;

        // $sql = "SELECT * FROM video WHERE isActive = ? AND isPrivate = ? AND isRockapick = ? AND rockapickDate = ? ORDER BY rockapickStepNo ASC";

        $sql = "SELECT
        v.videoID,
        v.userID,
        v.categoryID,
        v.videoLink,
        v.title,
        v.description,
        v.tag,
        v.duration,
        v.thumbnail,
        v.isActive,
        v.isPrivate,
        v.isRockapick,
        v.updated,
        v.created,
        rv.rockapickStepNo,
        rv.rockapickDate

        FROM rockapickVideo as rv

        INNER JOIN video as v
        ON rv.videoID = v.videoID

        WHERE v.isActive = ?
        AND v.isPrivate = ?
        AND v.isRockapick = ?
        AND rv.rockapickDate = ?
        AND rv.rockapickStepNo <> ?
        ORDER BY rv.rockapickStepNo ASC";

        $query = $this->db->query($sql,array('1','0','1',$todayDate,'0'));
        // echo $this->db->last_query();
        // print_r($query->result_array());exit;
        // if($query->result_array()){
        //     return
        // }
        // return $query->result_array();
        $results = array();
        foreach ($query->result_array() as $row)
        {
            //$userID='45';
            $sql1 = "SELECT categoryID,categoryName
            FROM category
            WHERE categoryID = ?";

            $query1 = $this->db->query($sql1,array($row['categoryID']));
            $categoryDetail = $query1->row();

            //userLIke videos
            $likeVideoID =  $row['videoID'];
            $likeSql = "SELECT count(*) AS likeCount FROM userLikeVideo WHERE videoID = ? and likeFlag =1";
            $likeQuery = $this->db->query($likeSql,array($likeVideoID));
            foreach ($likeQuery->result_array() as $likeRow)
            {
                $likeCount = $likeRow['likeCount'];
            }
            //.userLike videos

            //viewCount
            $viewSql = "SELECT count(*) AS ViewCount FROM videoView WHERE videoID = ?";
            $viewQuery = $this->db->query($viewSql,array($likeVideoID));
            foreach ($viewQuery->result_array() as $viewRow)
            {
                $ViewCount = $viewRow['ViewCount'];
            }

            //userID of video uploader detials
            $userDetailSql = "SELECT userID,fullName,profilePic,userName
            FROM userProfile
            WHERE userID = ?";

            $userDetailQuery = $this->db->query($userDetailSql,array($row['userID']));
            $userData = $userDetailQuery->row();
            $userProfileData=$this->api->getUserData2($userID);

            // getting video like flag details
            $likeFlagSql = "SELECT videoID FROM userLikeVideo  WHERE videoID = ? AND likeFlag = 1 AND userID = ?";
            $likeFlagQuery = $this->db->query($likeFlagSql,array($row['videoID'],$userID));
            if ($likeFlagQuery->num_rows() >= 1) {
                $likeFlag = '1';
            } else {
                $likeFlag = '0';
            }

            //converting video tags into an array
            $tagString = $row['tag'];
            $tagArray = array();
            $tagArray = explode(',',$tagString);
            if($tagArray[0]==''){
                unset($tagArray);
                $tagArray = array();
            }

            //converting duration to mm:ss if hh==00
            $duration = $row['duration'];
            $durationArray = explode(':',$duration);
            if($durationArray[0]=='00'){
                $finalDuration = $durationArray[1].':'.$durationArray[2];
            }else{
                $finalDuration = $durationArray[0].':'.$durationArray[1].':'.$durationArray[2];
            }

            //$createdTime = $this->time_elapsed_string($row['created']);
            if(($row['userID']==$userID) || ($row['isPrivate']=='0')){

                $results[]=array(
                    'videoID'=>$row['videoID'],
                    'userData'=>$userData,
                    'category'=>$categoryDetail,
                    'videoLink'=>$row['videoLink'],
                    'title'=>$row['title'],
                    'description'=>str_replace('\\n', "\n",$row['description']),
                    'tag'=>$tagArray,
                    'viewCount'=>$ViewCount,
                    'duration'=>$finalDuration,
                    'thumbnail'=>$row['thumbnail'],
                    'likeCount'=>$likeCount,
                    'likeFlag'=>$likeFlag,
                    'isActive'=>$row['isActive'],
                    'isRockapick'=>$row['isRockapick'],
                    'updated'=>$row['updated'],
                    'created'=>$row['created']
                    );
            }else{
               continue;
            }
        }

        // print_r($results);ex
        return $results;
    }

    public function getSuggestedVideo($suggestionID,$suggestion,$userID,$videoID){
        $results=array();

        switch ($suggestion) {
            case 'sameUser':
                $sql = "SELECT *
                FROM video
                WHERE isActive = 1 AND userID=? AND videoID <> ? ORDER by videoID DESC LIMIT 25";
                $query = $this->db->query($sql,array($suggestionID,$videoID));
                // print_r($this->db->last_query());
                break;

            case 'sameCategory';
                $sql = "SELECT *
                FROM video
                WHERE isActive = 1 AND categoryID=? AND videoID <> ? ORDER by videoID DESC LIMIT 25";
                $query = $this->db->query($sql,array($suggestionID,$videoID));
                // print_r($this->db->last_query());
                break;

            case 'sameTag';
                //$videoTagArray = $suggestionID;

                $prefix  = "tag LIKE  '%";
                $string='';
                foreach($suggestionID as $value) {
                    $string=$string .= $prefix . '' . $value. '';
                    $prefix = "%' or tag LIKE  '%";
                }
                $string=$string."%'";

                $sql = "SELECT *
                FROM video
                WHERE isActive = 1 AND ($string) AND videoID <> ? ORDER by videoID DESC LIMIT 25";
                $query = $this->db->query($sql,$videoID);
                // print_r($this->db->last_query());
                break;
        }
        /*-------------Getting each category name----------------------------- */
        foreach ($query->result_array() as $row)
        {
            //$userID='45';
            $sql1 = "SELECT categoryID,categoryName
            FROM category
            WHERE categoryID = ?";

            $query1 = $this->db->query($sql1,array($row['categoryID']));
            $categoryDetail = $query1->row();

            //userLIke videos
            $likeVideoID =  $row['videoID'];
            $likeSql = "SELECT count(*) AS likeCount FROM userLikeVideo WHERE videoID = ? and likeFlag =1";
            $likeQuery = $this->db->query($likeSql,array($likeVideoID));
            foreach ($likeQuery->result_array() as $likeRow)
            {
                $likeCount = $likeRow['likeCount'];
            }
            //.userLike videos

            //viewCount
            $viewSql = "SELECT count(*) AS ViewCount FROM videoView WHERE videoID = ?";
            $viewQuery = $this->db->query($viewSql,array($likeVideoID));
            foreach ($viewQuery->result_array() as $viewRow)
            {
                $ViewCount = $viewRow['ViewCount'];
            }

            //userID of video uploader detials
            $userDetailSql = "SELECT userID,fullName,profilePic,userName
            FROM userProfile
            WHERE userID = ?";

            $userDetailQuery = $this->db->query($userDetailSql,array($row['userID']));
            $userData = $userDetailQuery->row();

            //like dislike flag
            $likeFlagSql = "SELECT videoID FROM userLikeVideo  WHERE videoID = ? AND likeFlag = 1 AND userID = ?";
            $likeFlagQuery = $this->db->query($likeFlagSql,array($row['videoID'],$userID));
            if ($likeFlagQuery->num_rows() >= 1) {
                $likeFlag = '1';
            } else {
                $likeFlag = '0';
            }

            //converting video tags into an array
            $tagString = $row['tag'];
            $tagArray = array();
            $tagArray = explode(',',$tagString);
            if($tagArray[0]==''){
                unset($tagArray);
                $tagArray = array();
            }


            //converting duration to mm:ss if hh==00
            $duration = $row['duration'];
            $durationArray = explode(':',$duration);
            if($durationArray[0]=='00'){
                $finalDuration = $durationArray[1].':'.$durationArray[2];
            }else{
                $finalDuration = $durationArray[0].':'.$durationArray[1].':'.$durationArray[2];
            }

            //$createdTime = $this->time_elapsed_string($row['created']);
            if(($row['userID']==$userID) || ($row['isPrivate']=='0')){
            $results[]=array(
                'videoID'=>$row['videoID'],
                'userData'=>$userData,
                'category'=>$categoryDetail,
                'videoLink'=>$row['videoLink'],
                'title'=>$row['title'],
                'description'=>str_replace('\\n', "\n",$row['description']),
                'tag'=>$tagArray,
                'viewCount'=>$ViewCount,
                'duration'=>$finalDuration,
                'thumbnail'=>$row['thumbnail'],
                'likeCount'=>$likeCount,
                'isActive'=>$row['isActive'],
                'likeFlag'=>$likeFlag,
                'isRockapick'=>$row['isRockapick'],
                'updated'=>$row['updated'],
                'created'=>$row['created']
                );
        }else{
            continue;
        }
        }
        /*-------------Getting each category name----------------------------- */

        return $results;
    }

    //getStars is a test service
    public function getStars($userID){
        $stars = 0;
        $getUserCategory = $this->getUserCategory($userID);
        // foreach ($getUserCategory->result_array() as $row){
        //     $stars = $row['categoryID'];
        //     print_r($stars);
        //     echo "<br>";
        // }
        // print_r($stars);

        $prefix  = 'categoryID =';
        $string='';
        foreach ($getUserCategory->result_array() as $row)
        {

            $string=$string .= $prefix . ' ' . $row['userID'] . ' ';
            $prefix = 'or categoryID=';
        }

        echo $string;
    }

    private function getUserCategory($userID){
        $sql = "SELECT *
        FROM userInterest
        WHERE userID = ? AND isSelected = 1";
        // $sql = "SELECT * FROM userProfile";
        $query = $this->db->query($sql,array($userID));
        //$resultArray = $query->result_array();
        return $query;
    }

    public function insertVideo($insertVideo){
        $this->db->insert('video', $insertVideo);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function getVideoDetail($videoID){
        $sql = "SELECT *
        FROM video WHERE videoID = ? AND isActive = '1' ";
        $query = $this->db->query($sql,array($videoID));
        //$res = $query->result();

        //counting like
        $likeSql = "SELECT count(*) AS likeCount FROM userLikeVideo WHERE videoID = ? and likeFlag =1";
        $likeQuery = $this->db->query($likeSql,array($videoID));
        //$likeQuery->num_rows()>=1

        //$likeQuery->result_array()
        foreach ($likeQuery->result_array() as $likeRow)
        {
            $likeCount = $likeRow['likeCount'];
        }

        //viewCount
        $viewSql = "SELECT count(*) AS ViewCount FROM videoView WHERE videoID = ?";
        $viewQuery = $this->db->query($viewSql,array($videoID));
        foreach ($viewQuery->result_array() as $viewRow)
        {
            $ViewCount = $viewRow['ViewCount'];
        }

        // creating video detail array
        foreach ($query->result_array() as $row)
        {
            //$userID='45';
            $sql1 = "SELECT categoryID,categoryName
            FROM category
            WHERE categoryID = ?";

            $query1 = $this->db->query($sql1,array($row['categoryID']));
            $categoryDetail = $query1->row();

            //userID of video uploader detials
            $userDetailSql = "SELECT userID,fullName,profilePic,userName
            FROM userProfile
            WHERE userID = ?";

            $userDetailQuery = $this->db->query($userDetailSql,array($row['userID']));
            $userData = $userDetailQuery->row();



            //converting video tags into an array
            $tagString = $row['tag'];
            $tagArray = array();
            $tagArray = explode(',',$tagString);
            if($tagArray[0]==''){
                unset($tagArray);
                $tagArray = array();
            }

            //converting duration to mm:ss if hh==00
            $duration = $row['duration'];
            $durationArray = explode(':',$duration);
            if($durationArray[0]=='00'){
                $finalDuration = $durationArray[1].':'.$durationArray[2];
            }else{
                $finalDuration = $durationArray[0].':'.$durationArray[1].':'.$durationArray[2];
            }


            //$createdTime = $this->time_elapsed_string($row['created']);
            $result[]=array(
                'videoID'=>$row['videoID'],
                'userData'=>$userData,
                'category'=>$categoryDetail,
                'videoLink'=>$row['videoLink'],
                'title'=>$row['title'],
                'description'=>str_replace('\\n', "\n",$row['description']),
                'tag'=>$tagArray,
                'viewCount'=>$ViewCount,
                'duration'=>$finalDuration,
                'thumbnail'=>$row['thumbnail'],
                'likeCount'=>$likeCount,
                //'likeFlag' => $likeFlag,
                'isActive'=>$row['isActive'],
                'isRockapick'=>$row['isRockapick'],
                'updated'=>$row['updated'],
                'created'=>$row['created']
                );
        }

        return $result;
    }

    public function specificVideoDetail($videoID,$userID){
        $sql = "SELECT *
        FROM video WHERE videoID = ? AND isActive = '1'";
        $query = $this->db->query($sql,array($videoID));
        //$res = $query->result();

        //counting like
        $likeSql = "SELECT count(*) AS likeCount FROM userLikeVideo WHERE videoID = ? and likeFlag =1";
        $likeQuery = $this->db->query($likeSql,array($videoID));
        //$likeQuery->result_array()
        foreach ($likeQuery->result_array() as $likeRow)
        {
            $likeCount = $likeRow['likeCount'];
        }

        //viewCount
        $viewSql = "SELECT count(*) AS ViewCount FROM videoView WHERE videoID = ?";
        $viewQuery = $this->db->query($viewSql,array($videoID));
        foreach ($viewQuery->result_array() as $viewRow)
        {
            $ViewCount = $viewRow['ViewCount'];
        }

        //likeFlag Details
        $likeFlagSql = "SELECT likeFlag FROM userLikeVideo WHERE videoID=? AND userID=?";
        $likeFlagQuery = $this->db->query($likeFlagSql,array($videoID,$userID));
        if ($likeFlagQuery->num_rows() >= 1) {
            foreach ($likeFlagQuery->result_array() as $likeFlagRow)
            {
                $likeFlag = $likeFlagRow['likeFlag'];
            }
        } else {
            $likeFlag = '0';
        }
        // creating video detail array
        $result = array();
        foreach ($query->result_array() as $row)
        {
            //$userID='45';
            $sql1 = "SELECT categoryID,categoryName
            FROM category
            WHERE categoryID = ?";

            $query1 = $this->db->query($sql1,array($row['categoryID']));
            $categoryDetail = $query1->row();

            //userID of video uploader detials
            $userDetailSql = "SELECT userID,fullName,profilePic,userName
            FROM userProfile
            WHERE userID = ?";

            $userDetailQuery = $this->db->query($userDetailSql,array($row['userID']));
            $userData = $userDetailQuery->row();

            //converting video tags into an array
            $tagString = $row['tag'];
            $tagArray = array();
            $tagArray = explode(',',$tagString);
            if($tagArray[0]==''){
                unset($tagArray);
                $tagArray = array();
            }

            //converting duration to mm:ss if hh==00
            $duration = $row['duration'];
            $durationArray = explode(':',$duration);
            if($durationArray[0]=='00'){
                $finalDuration = $durationArray[1].':'.$durationArray[2];
            }else{
                $finalDuration = $durationArray[0].':'.$durationArray[1].':'.$durationArray[2];
            }

            //$createdTime = $this->time_elapsed_string($row['created']);
            $result[]=array(
                'videoID'=>$row['videoID'],
                'userData'=>$userData,
                'category'=>$categoryDetail,
                'videoLink'=>$row['videoLink'],
                'title'=>$row['title'],
                'description'=>str_replace('\\n', "\n",$row['description']),
                'tag'=>$tagArray,
                'viewCount'=>$ViewCount,
                'duration'=>$finalDuration,
                'thumbnail'=>$row['thumbnail'],
                'likeCount'=>$likeCount,
                'isActive'=>$row['isActive'],
                'isRockapick'=>$row['isRockapick'],
                'likeFlag'=>$likeFlag,
                'updated'=>$row['updated'],
                'created'=>$row['created']
                );
        }

        return $result;
    }

    //making an entry videoView table when ever a user watches video
    public function insertVideoView($insertData){
        $this->db->insert('videoView', $insertData);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function isVideoExist($videoID){
        $sql = "SELECT * FROM `video` WHERE videoID = ? AND isActive = '1'";
        $query = $this->db->query($sql,array($videoID));
        //$query = $this->db->query("SELECT * FROM user_profile WHERE phone_no='$mobile_no' ");
        if ($query->num_rows() >= 1) {
            return true;
        } else {
            return false;
        }
    }

    public function updateViewCount($videoID){
        //$viewCount = "view + 1";

        $sql = "UPDATE video SET view = view+1 WHERE videoID = ?";
        $query = $this->db->query($sql,array($videoID));
    }

    public function userLikeVideoExist($userID,$videoID){
        $sql = "SELECT *
        FROM userLikeVideo
        WHERE userID = ? AND videoID = ?";
        $query = $this->db->query($sql,array($userID,$videoID));
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function updateUserLikeVideo($userID,$videoID,$likeDislikeFlag){
        // UPDATE table_name
        // SET column1=value1,column2=value2,...
        // WHERE some_column=some_value;
        $sql = "UPDATE userLikeVideo SET likeFlag=? WHERE userID=? AND videoID=?";
        $query = $this->db->query($sql,array($likeDislikeFlag,$userID,$videoID));
        return ($query) ? true : false;
    }

    public function insertUserLikeVideo($insertUserLikeVideo){
        $query = $this->db->insert('userLikeVideo', $insertUserLikeVideo);
        return ($query) ? true : false;
    }

    private function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            // 's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    public function userUploadedVideo($userID){
        $sql = "SELECT *
        FROM video
        WHERE isActive = 1 AND userID = ? ORDER BY videoID DESC";
        $query = $this->db->query($sql,array($userID));

        /*-------------Getting each category name----------------------------- */
        $results=array();
        foreach ($query->result_array() as $row)
        {
            //$userID='45';
            $sql1 = "SELECT categoryID,categoryName
            FROM category
            WHERE categoryID = ?";

            $query1 = $this->db->query($sql1,array($row['categoryID']));
            $categoryDetail = $query1->row();

            //userLIke videos
            $likeVideoID =  $row['videoID'];
            $likeSql = "SELECT count(*) AS likeCount FROM userLikeVideo WHERE videoID = ? and likeFlag =1";
            $likeQuery = $this->db->query($likeSql,array($likeVideoID));
            foreach ($likeQuery->result_array() as $likeRow)
            {
                $likeCount = $likeRow['likeCount'];
            }
            //.userLike videos

            //likeFlag
            $likeFlagSql = "SELECT likeFlag FROM userLikeVideo WHERE videoID=? AND userID=?";
            $likeFlagQuery = $this->db->query($likeFlagSql,array($row['videoID'],$userID));
            if ($likeFlagQuery->num_rows() >= 1) {
                foreach ($likeFlagQuery->result_array() as $likeFlagRow)
                {
                    $likeFlag = $likeFlagRow['likeFlag'];
                }
            } else {
                $likeFlag = '0';
            }

            //viewCount
            $viewSql = "SELECT count(*) AS ViewCount FROM videoView WHERE videoID = ?";
            $viewQuery = $this->db->query($viewSql,array($likeVideoID));
            foreach ($viewQuery->result_array() as $viewRow)
            {
                $ViewCount = $viewRow['ViewCount'];
            }

            //userID of video uploader detials
            $userDetailSql = "SELECT userID,fullName,profilePic,userName
            FROM userProfile
            WHERE userID = ?";

            $userDetailQuery = $this->db->query($userDetailSql,array($row['userID']));
            $userData = $userDetailQuery->row();

            //converting video tags into an array
            $tagString = $row['tag'];
            $tagArray = array();
            $tagArray = explode(',',$tagString);
            if($tagArray[0]==''){
                unset($tagArray);
                $tagArray = array();
            }

            //converting duration to mm:ss if hh==00
            $duration = $row['duration'];
            $durationArray = explode(':',$duration);
            if($durationArray[0]=='00'){
                $finalDuration = $durationArray[1].':'.$durationArray[2];
            }else{
                $finalDuration = $durationArray[0].':'.$durationArray[1].':'.$durationArray[2];
            }

            //$createdTime = $this->time_elapsed_string($row['created']);
            if(($row['userID']==$userID) || ($row['isPrivate']=='0')){
                $results[]=array(
                    'videoID'=>$row['videoID'],
                    'userData'=>$userData,
                    'category'=>$categoryDetail,
                    'videoLink'=>$row['videoLink'],
                    'title'=>$row['title'],
                    'description'=>str_replace('\\n', "\n",$row['description']),
                    'tag'=>$tagArray,
                    'viewCount'=>$ViewCount,
                    'duration'=>$finalDuration,
                    'thumbnail'=>$row['thumbnail'],
                    'likeCount'=>$likeCount,
                    'likeFlag'=>$likeFlag,
                    'isActive'=>$row['isActive'],
                    'isRockapick'=>$row['isRockapick'],
                    'updated'=>$row['updated'],
                    'created'=>$row['created']
                    );
            }else{
                continue;
            }
        }
        /*-------------Getting each category name----------------------------- */
        return $results;
    }

    public function uploadIpAddress($ipaddress){
        $sql = "INSERT userLikeVideo SET likeFlag=? WHERE userID=? AND videoID=?";
        $query = $this->db->query($sql,array($likeDislikeFlag,$userID,$videoID));
        return ($query) ? true : false;
    }

    public function insertWishVideo($insertVideo){
        $this->db->insert('wishVideo', $insertVideo);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function getWishVideoDetail($videoID){
        $sql = "SELECT *
        FROM wishVideo WHERE WishvideoID = ?";
        $query = $this->db->query($sql,array($videoID));

        foreach ($query->result_array() as $row)
        {
            //userID of video uploader detials
            $userDetailSql = "SELECT userID,fullName,profilePic,userName
            FROM userProfile
            WHERE userID = ?";

            $userDetailQuery = $this->db->query($userDetailSql,array($row['userID']));
            $userData = $userDetailQuery->row();


            //converting duration to mm:ss if hh==00
            $duration = $row['duration'];
            $durationArray = explode(':',$duration);
            if($durationArray[0]=='00'){
                $finalDuration = $durationArray[1].':'.$durationArray[2];
            }else{
                $finalDuration = $durationArray[0].':'.$durationArray[1].':'.$durationArray[2];
            }


            //$createdTime = $this->time_elapsed_string($row['created']);
            $result[]=array(
                'videoID'=>$row['wishVideoID'],
                'userData'=>$userData,
                //'category'=>$categoryDetail,
                'videoLink'=>$row['videoLink'],
                'title'=>$row['title'],
                'description'=>str_replace('\\n', "\n",$row['description']),
                //'tag'=>$row['tag'],
                //'viewCount'=>$ViewCount,
                //'duration'=>$row['duration'],
                'receiverEmail'=>$row['receiverEmail'],
                'receiveDateTime'=>$row['receiveDateTime'],
                'duration'=>$finalDuration,
                'thumbnail'=>$row['thumbnail'],
                //'likeCount'=>$likeCount,
                //'isActive'=>$row['isActive'],
                //'isRockapick'=>$row['isRockapick'],
                'updated'=>$row['updated'],
                'created'=>$row['created']
                );
        }
        return $result;
    }

    public function sendEmailWishVideo()
    {

        $dateTime = date('Y-m-d H:i:s');
        //$time1 = date('H:i:s');
        $less = date('Y-m-d H:i:s',strtotime('-5 minutes'));

        $sql = "SELECT w.*, u.fullName
        FROM wishVideo w
        INNER JOIN userProfile u
        ON w.userID=u.userID
        WHERE
        receiveDateTime >= ? AND receiveDateTime <= ? AND isSent ='0'";



        $query = $this->db->query($sql,array($less,$dateTime));

        // echo $this->db->last_query();exit;

        if($query->num_rows() >= 1){
            foreach ($query->result_array() as $row) {

                $receiverEmailAddress = $row['receiverEmail'];
                $senderUserID = $row['userID'];


                $wishVideoID = $row['wishVideoID'];
                $sql = "UPDATE wishVideo SET isSent='1' WHERE wishVideoID=?";
                $query = $this->db->query($sql,array($wishVideoID));


                $androidUrl='<a href="https://play.google.com/store/apps/details?id=com.gss.rockabyte.login&wishVideoID=19">Playstore</a>';

                //https://play.google.com/store/apps/details?id=com.gss.rockabyte.login&wishVideoID=19
                $iphoneUrl='<a href="https://itunes.apple.com/us/app/rockabyte/id1068986999?ls=1&mt=8">AppStore</a>';

                $subject = 'Rockabyte Wish Video';

                // $body ='<html><body>
                // '.$row['fullName'].' has sent you a video message through Rockabyte .<br><br>
                // Please download the Android application from '.$androidUrl.' or iOS application from '.$iphoneUrl.' to view the message. <br><br>
                // Note: Please register and login using the same e-mail id which you received this email.</body></html>';

                $body = '
                <div class="holder" style="width:500px;height: 100%;left: 0;right: 0;
                        color: white;margin: auto;top: 0;bottom: 0;background-color: rgb(12,45,87);">
                    <table width="100%" height="100%">
                    <td width="100%" height="100%" style="width:500px;height: 100%;left: 0;right: 0;
                        background-color: #008780 !important;">

                    <div class="logoHolder" style="position: relative;font-family: Arial,sans-serif;">
                        <img src="http://faarbetterfilms.com/rockabyteServicesV2/assets/images/logowhite.png" class="logoClass" style="width: 80px;position: relative;">
                        <div class="textOnImg" style="position: relative;color: white;top: 0px;left: -3px;
                        font-size: 10px;letter-spacing: 2px;width: 80px;text-align: center;">
                            <span> <strong>ROCKA</strong><br> BYTE</span>
                        </div>

                    </div>
                    <div class="content" style="padding: 10px;text-align: center;padding-bottom: 50px;width:280px;left:0;right:0;margin:auto;">
                        <h4 style="color: #fff289 !important;font-family:Arial,sans-serif;font-size: 20px;font-weight: 400;margin: 10px 0;">'.$row['fullName'].' has sent you a video message through Rockabyte.</h4>
                        <br>
                        <h6 style="font-size: 12px !important; font-family: Arial,sans-serif;font-weight: 400;color: #ffffff !important;">Please download the Android application from Playstore or iOS application from Appstore to view the message.</h6>
                        <br>
                        <div style="width: 80%;left: 0;right: 0;margin: auto;">
                            <div style="width: 50%;float: left;">
                                <a href="https://play.google.com/store/apps/details?id=com.gss.rockabyte.login" class="socialText" style="padding: 15px;text-decoration: none;color: inherit;font-size: 12px !important;
                                    font-family: Arial,sans-serif;font-weight: 400;color: #ffffff !important;">Playstore</a>
                                    <br>
                                <a href="https://play.google.com/store/apps/details?id=com.gss.rockabyte.login"><img src="http://faarbetterfilms.com/rockabyteServicesV2/assets/images/playstore.png" class="social" style="width: 100px;padding: 5px;"></a>

                            </div>
                            <div style="width: 50%;float: right;">
                                <a href="https://itunes.apple.com/us/app/rockabyte/id1068986999?ls=1&mt=8" class="socialText" style="padding: 15px;text-decoration: none;font-size: 12px !important;
                                    font-family: Arial,sans-serif;font-weight: 400;color: #ffffff !important;">Appstore</a>
                                    <br>
                                <a href="https://itunes.apple.com/us/app/rockabyte/id1068986999?ls=1&mt=8"><img src="http://faarbetterfilms.com/rockabyteServicesV2/assets/images/appstore.png" class="social" style="width: 100px;padding: 5px;"></a>
                            </div>
                        </div>
                        <br><br>
                        <h6><i style="font-family: Arial,sans-serif;font-weight: 400;font-size: 12px;">Note: Please register and login using the same Email id on which you received this email.</i></h6>
                    </div>
                    <div class="footerHolder" style="text-align: center;padding:20px;width:260px;left:0;right:0;margin:auto;">
                        <span class="rightReserved" style="color: #aaa7a7;font-size: 10px;font-family: Arial,sans-serif;">
                            &copy; 2017 RockaByte. All right reserved.
                        </span>
                        <br>
                        <span class="notReply" style="font-size: 10px;font-family: Arial,sans-serif;color: #ffffff !important;">Please do not reply to this email as it is a computer generated mail.</span>
                    </div>
                </td>
                </table>
                </div>
                ';

                //$receiverEmailAddress = $row['receiverEmail'];
                $emailResponse = $this->sendEmail($receiverEmailAddress, $subject, $body);
                //----------push notification---------------------//
                //$sendPushResponse = $this->sendPushNotification($receiverEmailAddress,$senderUserID);
                // return $emailResponse;

                //inserting wish video details in chat folder
                // if(userid_corresponding_to_email_address){
                //     $wishVideoToChat = array(
                //         'senderUserID' => $row['userID'],
                //         'receiverUserID' => $receiverEmail(we_need_userid),
                //         'message' => '',
                //         'videoID' => $row['wishVideoID'],
                //         'chatType' => 'user',
                //         'isActive' => '1',
                //         'messageType' => 'wish',
                //         'created' => date('Y-m-d H:i:s')
                //         );
                //     $this->db->insert('chat', $wishVideoToChat);
                // }

                //--------------inserting in notifications-------------------//
                $sqlReceiverUserID = "SELECT * FROM userProfile WHERE email = ?";
                $queryReceiverUserID = $this->db->query($sqlReceiverUserID,array($receiverEmailAddress));

                $queryReceiverUserIDRow = $queryReceiverUserID->row();

                if($queryReceiverUserID->num_rows() >=1){
                    // return $row->userID;
                    $insertDataNotifications = array(
                        "notificationTypeID" =>'2',//wish Received == 2
                        "userID" => $queryReceiverUserIDRow->userID,// the one who is receiving
                        "videoID" =>'0',// not applicable here
                        "wishVideoID" => $wishVideoID,// wish video id
                        "followingID" =>'0',//not applicable here
                        "isActive" =>'1',// will be one always till user deletes it
                        "created" =>date('Y-m-d H:i:s')
                    );
                }else{
                    $insertDataNotifications = array(
                        "notificationTypeID" =>'2',//wish Received == 2
                        "userID" => '0',// when ever a user registers check if they have wish video in wish table by email
                        "videoID" =>'0',// not applicable here
                        "wishVideoID" => $wishVideoID,// wish video id
                        "followingID" =>'0',//not applicable here
                        "isActive" =>'1',// will be one always till user deletes it
                        "created" =>date('Y-m-d H:i:s')
                    );
                }

                $this->insertNotifications($insertDataNotifications);
                //--------------inserting in notifications-------------------//
            }
        }
    }

    public function userIDOfEmailAddress($email){
        $sql = "SELECT userID FROM userProfile WHERE email = ?";
        $query = $this->db->query($sql,array($email));
        $row = $query->row();
        if($query->num_rows() >=1){
            return $row->userID;
        }else{
            return 0;
        }
    }

    public function sendPushNotification($receiverEmailAddress,$senderUserID){

        //receiver data
        $receiverUserProfileData=$this->getUserDataViaEmail($receiverEmailAddress);
        // print_r($receiverUserProfileData);
        $androidKey = $receiverUserProfileData['androidKey'];
        $iosKey = $receiverUserProfileData['iosKey'];

        //sender data
        $senderUserProfileData = $this->getUserData2($senderUserID);
        $senderFullName = $senderUserProfileData['fullName'];
        $senderUserName = $senderUserProfileData['userName'];

        if($senderUserName==''){
            $senderName = $senderFullName;
        }else{
            $senderName = $senderUserName;
        }

        $finalNotification = "$senderName has sent you a wish video ";
        echo $finalNotification;
        if(!empty($androidKey)){
            $sendingGcmMessage = $this->androidNotification($androidKey,$finalNotification);
        }

        if(!empty($iosKey)){
            // $sendingApnsMessage = $this->iphoneNotification($iosKey,$finalNotification);
        }
    }


    public function getUserDataViaEmail($receiverEmailAddress){
        $sql = "SELECT
        userID,
        fullName,
        userName,
        occupation,
        phoneNo,
        gender,
        email,
        profilePic,
        country,
        dob,
        state,
        aboutMe,
        androidKey,
        iosKey,
        isActive

        FROM userProfile WHERE email = ?";
        $query = $this->db->query($sql,array($receiverEmailAddress));
        $res = $query->row_array();
        // $userData = array(
        //         "fullName"=>$res['fullName'];
        //     );

        $socialLoginData = $this->socialLoginData($res['userID']);

        $result = array(
            'userID'        => $res['userID'],
            'fullName'      => $res['fullName'],
            'userName'      => $res['userName'],
            'occupation'    => $res['occupation'],
            'phoneNo'       => $res['phoneNo'],
            'gender'        => $res['gender'],
            'email'         => $res['email'],
            'profilePic'    => $res['profilePic'],
            'country'       => $res['country'],
            'dob'           => $res['dob'],
            'state'         => $res['state'],
            'aboutMe'       => $res['aboutMe'],
            'androidKey'    => $res['androidKey'],
            'iosKey'        => $res['iosKey'],
            'isActive'      => $res['isActive'],
            'syncAccount'   => $socialLoginData
            );


        return $result;
    }


    public function androidNotification($reg_key,$notification){
        // public function androidNotification(){
        // API access key from Google API's Console
        // $reg_key = "dbyIJkoPSc8:APA91bEW9UYvaskdMIMT7exRK-Q89EuZQIVJO5HsAKFUxEux7Qtq7kMRAT2RhctLui7fh0fFxhB2kTvqVuZRPJVt2BSbmYX4iBr-fnuUMR46PR-VBpFCwFS-q8LAsZh43J-gqfCbCb-c";
        // $notification = "hello world";

        define( 'API_ACCESS_KEY', 'AIzaSyDNk9wafP7C8TMNHVoT9TuuMAiF0_mh6LM' );
        $registrationIds = array($reg_key);

        // prep the bundle
        $msg = array
        (
            'message'   => $notification,
            'title'     => 'Rockabyte',
            'subtitle'  => 'notification',
            'tickerText'    => 'Ticker text here...Ticker text here...Ticker text here',
            'vibrate'   => 1,
            'sound'     => 1,
            'largeIcon' => 'large_icon',
            'smallIcon' => 'small_icon'
            );
        $fields = array
        (
            'registration_ids'  => $registrationIds,
            'data'          => $msg
            );

        $headers = array
        (
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
            );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        echo $result;

        //return $result;
    }

    public function iosNotification($deviceToken,$message){
        // Put your device token here (without spaces):
        //--$deviceToken = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

        // Put your private key's passphrase here:
        $passphrase = 'xxxxxxx';

        // Put your alert message here:
        //--$message = 'A push notification has been sent!';

        ////////////////////////////////////////////////////////////////////////////////

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck_file_name.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

        // Open a connection to the APNS server
        $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

        if (!$fp)
            exit("Failed to connect: $err $errstr" . PHP_EOL);

        echo 'Connected to APNS' . PHP_EOL;

        // Create the payload body
        $body['aps'] = array(
            'alert' => array(
                'body' => $message,
                'action-loc-key' => 'Bango App',
                ),
            'badge' => 2,
            'sound' => 'oven.caf',
            );

        // Encode the payload as JSON
        $payload = json_encode($body);

        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));

        if (!$result){
            //-- echo 'Message not delivered' . PHP_EOL;
            $returnResult = 'Message not delivered' . PHP_EOL;
        }else{
            // echo 'Message successfully delivered' . PHP_EOL;
            $returnResult = 'Message successfully delivered' . PHP_EOL;
        }
        // Close the connection to the server
        fclose($fp);

        return $returnResult;
    }


    // public function sendEmail($receiverEmailAddress, $emailSubject, $emailBody)
    // {
    //     $this->load->library('email');

    //     $config['useragent'] = 'CodeIgniter';
    //     $config['protocol'] = 'smtp';
    //     //$config['mailpath'] = '/usr/sbin/sendmail';
    //     $config['smtp_host'] = 'ssl://smtp.googlemail.com';
    //     $config['smtp_user'] = 'turkane.sparsh@gmail.com';
    //     $config['smtp_pass'] = 'sparsh@';
    //     $config['smtp_port'] = 465;
    //     $config['smtp_timeout'] = 30;
    //     $config['wordwrap'] = TRUE;
    //     $config['wrapchars'] = 76;
    //     $config['mailtype'] = 'html';
    //     $config['charset'] = 'utf-8';
    //     $config['validate'] = FALSE;
    //     $config['priority'] = 3;
    //     $config['crlf'] = "\r\n";
    //     $config['newline'] = "\r\n";
    //     $config['bcc_batch_mode'] = FALSE;
    //     $config['bcc_batch_size'] = 200;

    //     $initialize = $this->email->initialize($config);
    //     // echo "<pre>";
    //     // print_r($initialize);exit;

    //     $this->email->from('turkane.sparsh@gmail.com', 'turkane.sparsh@gmail.com');
    //     $this->email->to($receiverEmailAddress);
    //     // $this->email->cc('another@another-example.com');
    //     // $this->email->bcc('them@their-example.com');

    //     $this->email->subject($emailSubject);
    //     $this->email->message($emailBody);

    //     $value = $this->email->send();
    //     if($value){
    //         //echo "message sent successfully";
    //     }else{
    //         //echo $this->email->print_debugger();
    //     }
    // }

    public function sendEmail($receiverEmailAddress, $emailSubject, $emailBody)
    {
        $this->load->library('email');

        $config['useragent'] = 'CodeIgniter';
        $config['protocol'] = 'smtp';
        //$config['mailpath'] = '/usr/sbin/sendmail';
        $config['smtp_host'] = 'ssl://smtp.googlemail.com';
        $config['smtp_user'] = 'donotreply@test.com';
        $config['smtp_pass'] = 'this is wrong password';//
        $config['smtp_port'] = 465;
        $config['smtp_timeout'] = 30;
        $config['wordwrap'] = TRUE;
        $config['wrapchars'] = 76;
        $config['mailtype'] = 'html';
        $config['charset'] = 'utf-8';
        $config['validate'] = FALSE;
        $config['priority'] = 3;
        $config['crlf'] = "\r\n";
        $config['newline'] = "\r\n";
        $config['bcc_batch_mode'] = FALSE;
        $config['bcc_batch_size'] = 200;

        $initialize = $this->email->initialize($config);
        // echo "<pre>";
        // print_r($initialize);exit;

        $this->email->from('donotreply@test.com', 'donotreply@test.com');
        $this->email->to($receiverEmailAddress);
        // $this->email->cc('another@another-example.com');
        // $this->email->bcc('them@their-example.com');

        $this->email->subject($emailSubject);
        $this->email->message($emailBody);

        $value = $this->email->send();
        if($value){
            //echo "message sent successfully";
        }else{
            //echo $this->email->print_debugger();
        }
    }

    //old getChatMessage
    public function getChatMessage1($receiverUserID)
    {
        //user
        $sql = "SELECT senderUserID
        FROM chat WHERE receiverUserID = ? AND chatType = ?";
        $query = $this->db->query($sql,array($receiverUserID,'user'));

        //group
        $sql1 = "SELECT senderUserID
        FROM chat WHERE receiverUserID = ? AND chatType = ?";
        $query2 = $this->db->query($sql1,array($receiverUserID,'group'));

        $senderDetailsArray = array();
        $result = array();
        $message = array();

        // switch (variable) {
        //     case 'value':
        //         # code...
        //         break;

        //     default:
        //         # code...
        //         break;
        // }

        if($query->num_rows() >=1){
            $senderUserIDArray = array();

            //user array for chatType = user/private
            foreach ($query->result_array() as $row) {
                if(!(in_array($row['senderUserID'] , $senderUserIDArray))){
                    array_push($senderUserIDArray, $row['senderUserID']);
                }
                // echo "<pre>";print_r($senderUserIDArray);exit;
            }

            //user chat details i.e private chat
            foreach ($senderUserIDArray as $row1) {
                //$result = $row1;
                unset($message);
                unset($senderDetailQuery);
                //get sender details
                $senderDetailSql = "SELECT * FROM userProfile WHERE userID = ?";
                $senderDetailQuery = $this->db->query($senderDetailSql,array($row1));

                //getting message details
                $messageSql = "SELECT *
                FROM chat WHERE ((receiverUserID = ? AND senderUserID = ?)or(receiverUserID = ? AND senderUserID = ?)) AND chatType = ?";
                $messageQuery = $this->db->query($messageSql,array($receiverUserID, $row1,$row1,$receiverUserID,'user'));

                foreach ($messageQuery->result_array() as $row3) {
                    $message[]=array(
                        'chatID' => $row3['chatID'],
                        'message' => $row3['message'],
                        // 'chatType' => $row3['chatType'],
                        'fromUser' => $row3['senderUserID'],
                        'toUser' =>$row3['receiverUserID'],
                        'messageType' => $row3['messageType'],
                        'created' => $row3['created']
                    );
                }

                foreach ($senderDetailQuery->result_array() as $row2) {
                    $senderDetailsArray[] = array(
                        'senderUserName' => $row2['userName'],
                        'senderFullName' => $row2['fullName'],
                        'profilePic' => $row2['profilePic'],
                        'chatType' => 'user',
                        'unReadedMessageCount'=> '0',
                        'lastMessageDate' => '0',
                        'messageDetail' => $message
                    );
                }


                $result = $senderDetailsArray;
                //the get message details
                // $messageSql = "SELECT *
                // FROM chat WHERE receiverUserID = ? AND senderUserID = ";
                // $messageQuery = $this->db->query($messageSql,array($receiverUserID, $row1['senderUserID']));
            }

            unset($senderUserIDArray);
            $senderUserIDArray = array();
            //user array for chatType = group
            foreach ($query2->result_array() as $row) {
                if(!(in_array($row['senderUserID'] , $senderUserIDArray))){
                    array_push($senderUserIDArray, $row['senderUserID']);
                }
            }
            //user array for chatType = group

            //group chat details
            foreach ($senderUserIDArray as $row1) {
                //$result = $row1;
                unset($message);
                $message = array();
                unset($senderDetailQuery);
                //get sender details
                $senderDetailSql = "SELECT * FROM userProfile WHERE userID = ?";
                $senderDetailQuery = $this->db->query($senderDetailSql,array($row1));

                //getting message details
                $messageSql = "SELECT *
                FROM chat WHERE ((receiverUserID = ? AND senderUserID = ?)or(receiverUserID = ? AND senderUserID = ?)) AND chatType = ?";
                $messageQuery = $this->db->query($messageSql,array($receiverUserID, $row1,$row1,$receiverUserID,'group'));

                foreach ($messageQuery->result_array() as $row4) {
                    $message[]=array(
                        'chatID' => $row4['chatID'],
                        'message' => $row4['message'],
                        //'chatType' => $row4['chatType'],
                        'fromUser' => $row4['senderUserID'],
                        'toUser' =>$row4['receiverUserID'],
                        'messageType' => $row4['messageType'],
                        'created' => $row4['created']
                    );
                }

                foreach ($senderDetailQuery->result_array() as $row5) {
                    $senderDetailsArray[] = array(
                        'senderUserName' => $row5['userName'],
                        'senderFullName' => $row5['fullName'],
                        'profilePic' => $row5['profilePic'],
                        'chatType' => 'group',
                        'unReadedMessageCount'=> '0',
                        'lastMessageDate' => '0',
                        'messageDetail' => $message
                    );
                }


                $result = $senderDetailsArray;
                //the get message details
                // $messageSql = "SELECT *
                // FROM chat WHERE receiverUserID = ? AND senderUserID = ";
                // $messageQuery = $this->db->query($messageSql,array($receiverUserID, $row1['senderUserID']));
            }
            //group chat details
        }
        return $result;
    }

    // new & improved getChatMessage
    public function getChatMessage($receiverUserID)
    {
        //user
        $sql = "SELECT senderUserID,chatType
        FROM chat WHERE receiverUserID = ?";
        $query = $this->db->query($sql,array($receiverUserID));

        $senderUserIDArray = array();
        if($query->num_rows() >=1){ // num_rows if
            /*-----------chatType & senderUserID---------------*/
            foreach($query->result_array() as $row){
                $senderUserIDArray[] = array(
                    'chatType' => $row['chatType'],
                    'senderUserID' => $row['senderUserID']
                );
            }

            //removing duplicate elements from array
            $senderUserIDArray = array_unique($senderUserIDArray, SORT_REGULAR);

            /*-----------chatType & senderUserID---------------*/
            foreach ($senderUserIDArray as $row1) {
                $result[]=$this->senderDetail_messageDetail($row1['senderUserID'],$receiverUserID,$row1['chatType']);
            }
        }

        //processing array to remove the first level of [] from it;
        $processed = array_map(function($a) {  return array_pop($a); }, $result);
        //print_r($processed);
        return $processed;
    }

    private function senderDetail_messageDetail($senderUserID,$receiverUserID,$chatType)
    {
        //$count = 0;
        $senderDetailSql = "SELECT * FROM userProfile WHERE userID = ?";
        $senderDetailQuery = $this->db->query($senderDetailSql,array($senderUserID));

                //getting message details
        $messageSql = "SELECT *
        FROM chat
        WHERE ((receiverUserID = ? AND senderUserID = ?)or(receiverUserID = ? AND senderUserID = ?)) AND chatType = ?";
        $messageQuery = $this->db->query($messageSql,array($receiverUserID, $senderUserID,$senderUserID,$receiverUserID,$chatType));

        //unset($message);
        foreach ($messageQuery->result_array() as $row4) {
            $videoLink = '';
            $thumbnail = '';
            $title = '';
            $messageChat = $row4['message'];
            if($row4['messageType']=='video'){
                $videoSql = "SELECT * FROM video WHERE videoID = ?";
                $videoQuery = $this->db->query($videoSql, array($row4['videoID']));
                $videoQueryResult = $videoQuery->row();
                //print_r($videoQueryResult); exit;
                $videoLink = $videoQueryResult->videoLink;
                $thumbnail = $videoQueryResult->thumbnail;
            }

            if($row4['messageType']=='wish'){
                $wishVideoSql = "SELECT * FROM wishVideo WHERE wishVideoID = ?";
                $wishVideoQuery = $this->db->query($wishVideoSql, array($row4['videoID']));
                $wishVideoQueryResult = $wishVideoQuery->row();
                $videoLink = $wishVideoQueryResult->videoLink;
                $thumbnail = $wishVideoQueryResult->thumbnail;
                $messageChat = $wishVideoQueryResult->description;
                $title = $wishVideoQueryResult->title;
            }

            $message[]=array(
                'chatID' => $row4['chatID'],
                'message' => $messageChat,
                'title' => $title,
                //'chatType' => $row4['chatType'],
                'fromUser' => $row4['senderUserID'],
                'toUser' =>$row4['receiverUserID'],
                'messageType' => $row4['messageType'],
                'videoLink' => $videoLink,
                'thumbnail' => $thumbnail,
                'created' => $row4['created']
                );
        }

        //unset($senderDetailsArray);
        foreach ($senderDetailQuery->result_array() as $row5) {
            $senderDetailsArray[] = array(
                'senderUserName' => $row5['userName'],
                'senderFullName' => $row5['fullName'],
                'profilePic' => $row5['profilePic'],
                'chatType' => $chatType,
                'unReadedMessageCount'=> '0',
                'lastMessageDate' => '0',
                'messageDetail' => $message
                );
        }


        return $senderDetailsArray;
        // $count++;
        // return $count;
    }

    public function dataBelongToCurrentUser($userID,$dataParameter,$parameterValue)
    {
        //userID = userID (dugh..)
        //$dataParameter = email
        //$parameterValue = emailValue
        $sql = "SELECT $dataParameter FROM userProfile WHERE $dataParameter = ? AND userID = ?";
        $query = $this->db->query($sql,array($parameterValue, $userID));
        //return ($query->num_rows() > 0) ? true : false;
        if ($query->num_rows() >= 1) {
            return true;
        } else {
            return false;
        }
    }

    public function getUserNotification($userID){
        //return 'abc';
        //return false;
        $currentDateTime = date('Y-m-d H:i:s');
        //echo $currentTime;exit;
        $result = array();

        $sqlGetReceiverEmail = "SELECT email FROM userProfile WHERE userID = ?";
        $queryGetReceiverEmail=$this->db->query($sqlGetReceiverEmail,array($userID));

        $res = $queryGetReceiverEmail->row_array();
        $receiverEmail = $res['email'];

        if($receiverEmail){
            $sql = "SELECT
                up.profilePic,
                up.userName,
                up.fullName,
                up.userID,
                ww.thumbnail,
                ww.wishVideoID,
                ww.receiverEmail,
                ww.receiveDateTime,
                ww.videoLink,
                ww.description,
                ww.title,
                ww.isRead
            FROM userProfile AS up

            INNER JOIN wishVideo AS ww
            ON ww.receiverEmail = ? AND up.userID = ww.userID

            WHERE receiveDateTime >= '2012-12-12 00:00:00' AND receiveDateTime <= ?
            ORDER BY ww.wishVideoID DESC";

            $query = $this->db->query($sql,array($receiverEmail, $currentDateTime));
            //echo $this->db->last_query();exit;
            foreach ($query->result_array() as $row) {
                //$username;
                // if($row['userName']==''){
                //     $username = $row['fullName'];
                // }else{
                //     $username = $row['userName'];
                // }

                //user data of user who sent me wish video
                $fullName = $row['fullName'];
                $userData = array(
                    'userID'=> $row['userID'],
                    'userName'=> $fullName,
                    'fullName'=> $fullName,
                    'profilePic'=> $row['profilePic'],
                );
                $result[]=array(
                    'profilePic'=> $row['profilePic'],
                    'userName'=> $fullName,
                    'wishDescription'=>'sent you a wish video',
                    'description' => str_replace('\\n', "\n",$row['description']),
                    'userData' => $userData,
                    'thumbnail'=> $row['thumbnail'],
                    'videoID'=> $row['wishVideoID'],
                    'videoLink' =>$row['videoLink'],
                    'title' => str_replace('\\n', "\n",$row['title']),
                    'created'=> $row['receiveDateTime'],
                    'createdSort' => strtotime($row['receiveDateTime']),
                    'isRead' =>$row['isRead'],
                );
            }
        }
        return $result;
    }

    public function getUserNotificationV3($userID)
    {
        $selectNotificationSql = "SELECT * FROM notifications WHERE userID = ? ORDER BY notificationID DESC";
        $selectNotificationQuery = $this->db->query($selectNotificationSql, array($userID));

        $results = array();

        // nulling all the values here so they don't duplicate
        $profilePic = '';
        $userName = '';
        $wishDescription = '';
        $description = '';
        $userData = '';
        $thumbnail = '';
        $wishVideoID = '';
        $videoLink = '';
        $title = '';
        $created = '';
        $isRead = '';

        foreach ($selectNotificationQuery->result_array() as $value)
        {
            /**
            * we will have to implement switch loop lere what will be the data outside switch loop
            */
            // echo $value['notificationID'];
            switch ($value['notificationTypeID']) {
                case '1': //wish Sent
                    // wish sent
                    # code...
                    // $switchStatement = 1;
                    /**
                     * what do i have from notifications table
                     * 1. notificationID
                     * 2. notificationTypeID
                     * 3. userID
                     * 4. videoID
                     * 5. wishVideoID
                     * 6. followingID
                     * 7. userLikeVideoID
                     * 8. isRead
                     * 9. isActive
                     * 1. updated
                     * 2. created
                     */

                    $sentWishSql = "SELECT * FROM wishVideo WHERE wishVideoID = ?";
                    $sentWishQuery = $this->db->query($sentWishSql, array($value['wishVideoID']));

                    //$row = $query->row();
                    $sentWishData = $sentWishQuery->row();

                    //video Details
                    $description = $sentWishData->description;
                    $thumbnail = $sentWishData->thumbnail;
                    $wishVideoID = $sentWishData->wishVideoID;
                    $videoLink = $sentWishData->videoLink;
                    $title = $sentWishData->title;
                    $created = $sentWishData->created;
                    $isRead = $value['isRead'];

                    $isReceiverExist = "SELECT * FROM userProfile WHERE email = ?";
                    $queryIsReceiverExist = $this->db->query($isReceiverExist,array($sentWishData->receiverEmail));

                    if($queryIsReceiverExist->row()){
                        $userValue = $queryIsReceiverExist->row();
                        $profilePic = $userValue->profilePic;
                        $userName = $userValue->fullName;
                    }else{
      
                        $profilePic = '';
                        $userName = ucwords(str_replace('.',' ',strstr($sentWishData->receiverEmail, '@', true)));
                    }

                    //find video uploader details with the help of userID
                    $userDetailSql = "SELECT userID,fullName,profilePic,userName
                    FROM userProfile
                    WHERE userID = ?";

                    $userDetailQuery = $this->db->query($userDetailSql,array($userID));
                    $userDataInfo = $userDetailQuery->row();

                    $userData = array(
                        'userID'=> $userDataInfo->userID,
                        'userName'=> $userDataInfo->fullName,
                        'fullName'=> $userDataInfo->fullName,
                        'profilePic'=> $userDataInfo->profilePic,
                    );

                    // Determining if wish is past or future
                    $currentTime = date('Y-m-d H:i:s');
                    $currentTime = strtotime($currentTime);
                    $wishReceiveDateTime = strtotime($sentWishData->receiveDateTime);
                    if($currentTime > $wishReceiveDateTime){
                        //wish sent
                        $wishDescription = "Wish sent successfully";
                    }else{
                        //wish not sent
                        $wishDescription = "Pending";
                    }

                break;

                case '2': //wish received 
                    // wish received
                    # code...
                    // $switchStatement = 2;
                    // i have userID I will have to find emailID from that  user ID 
                    $receivedWishSql = "SELECT * FROM wishVideo WHERE wishVideoID = ?";
                    $receivedWishQuery = $this->db->query($receivedWishSql, array($value['wishVideoID']));

                    $receiveWishData = $receivedWishQuery->row();

                    //video Details
                    $description = $receiveWishData->description;
                    $thumbnail = $receiveWishData->thumbnail;
                    $wishVideoID = $receiveWishData->wishVideoID;
                    $videoLink = $receiveWishData->videoLink;
                    $title = $receiveWishData->title;
                    $created = $receiveWishData->created;
                    $isRead = $value['isRead'];

                    //sender userDetails
                    //find video uploader details with the help of userID
                    $userDetailSql = "SELECT userID,fullName,profilePic,userName
                    FROM userProfile
                    WHERE userID = ?";

                    $userDetailQuery = $this->db->query($userDetailSql,array($receiveWishData->userID));
                    $userDataInfo = $userDetailQuery->row();

                    $userData = array(
                        'userID'=> $userDataInfo->userID,
                        'userName'=> $userDataInfo->fullName,
                        'fullName'=> $userDataInfo->fullName,
                        'profilePic'=> $userDataInfo->profilePic,
                    );

                    //sender Details
                    $wishDescription = 'sent you a wish video';
                    $profilePic = $userDataInfo->profilePic;
                    $userName = $userDataInfo->fullName;
                break;

                case '3':
                    // like video
                    # code...
                    // in notification we have userID this is me to get receiver information i would have to do 
                    // and i also have userLikeVideoID
                    //1. from userLikeVideoTable get userID and videoID
                    //2. get user info
                    //3. get video info and videoUserID info 
                    $sqlUserLikeVideo = "SELECT * FROM userLikeVideo WHERE id = ?";
                    $queryUserLikeVideo = $this->db->query($sqlUserLikeVideo,array($value['userLikeVideoID']));
                    $userLikeVideoData = $queryUserLikeVideo->row();
                    // print_r($userLikeVideoData);

                    // $value['userLikeVideoID'];
                    //liker userID and liked videoID
                    $likerUserID = $userLikeVideoData->userID;
                    $likedVideoID = $userLikeVideoData->videoID;


                    //getting userDetails 
                    $sqlUserDetails = "SELECT * FROM userProfile WHERE userID = ?";
                    $queryUserDetails = $this->db->query($sqlUserDetails,array($likerUserID));
                    $userDetails = $queryUserDetails->row();


                    //getting videoDetails
                    $sqlVideoDetails = "SELECT * FROM video WHERE videoID = ?";
                    $queryVideoDetails = $this->db->query($sqlVideoDetails,array($likedVideoID));
                    $videoDetails = $queryVideoDetails->row();

                    // getting uploaded video user data
                    $sqlVideoUserDetails = "SELECT * FROM userProfile WHERE userID = ?";
                    $queryVideoUserDetails = $this->db->query($sqlVideoUserDetails,array($videoDetails->userID));
                    $userVideoDetails = $queryVideoUserDetails->row();



                    // $switchStatement = 3;
                    $profilePic = $userDetails->profilePic; //liker profile picture 
                    $userName = $userDetails->fullName; //liker userName
                    $wishDescription = 'liked your video';
                    $description = $videoDetails->description; // video description
                    $userData = array(
                        'userID'=> $userVideoDetails->userID,
                        'userName'=> $userVideoDetails->fullName,
                        'fullName'=> $userVideoDetails->fullName,
                        'profilePic'=> $userVideoDetails->profilePic,
                    );
                    $thumbnail = $videoDetails->thumbnail; //video Thumbnail
                    $wishVideoID = $videoDetails->videoID; // we will need videoID instead of wishVideoID
                    // $videoID = $videoDetails->videoID;
                    $videoLink = $videoDetails->videoLink; // video link
                    $title = $videoDetails->title; // videotitle
                    $created = $videoDetails->created;
                    $isRead = $value['isRead'];
                break;

                case '4':
                    // follow
                    # code...
                    $sqlFollowing = "SELECT * FROM following WHERE followingID = ?";
                    $queryFollowing = $this->db->query($sqlFollowing,array($value['followingID']));
                    $followingData = $queryFollowing->row();


                    $sqlUserDetails = "SELECT * FROM userProfile WHERE userID = ?";
                    $queryUserDetails = $this->db->query($sqlUserDetails,array($followingData->userID));
                    $userDetails = $queryUserDetails->row();


                    // $switchStatement = 4;
                    $profilePic = $userDetails->profilePic; // the follower
                    $userName = $userDetails->fullName; //the follower
                    $wishDescription = 'is following you'; 
                    $followerUserID = $userDetails->userID; // the follower ID
                    $created = $followingData->created;
                    $isRead = $value['isRead'];
                    //$description = ''; //
                    // $userData = '';
                    // $thumbnail = '';
                    // $wishVideoID = '';
                    // $videoLink = '';
                    // $title = '';
                    // $created = '';
                    // $isRead = '';
                break;

                case '5':
                    // unfollow
                    # code...
                    $sqlFollowing = "SELECT * FROM following WHERE followingID = ?";
                    $queryFollowing = $this->db->query($sqlFollowing,array($value['followingID']));
                    $followingData = $queryFollowing->row();


                    $sqlUserDetails = "SELECT * FROM userProfile WHERE userID = ?";
                    $queryUserDetails = $this->db->query($sqlUserDetails,array($followingData->userID));
                    $userDetails = $queryUserDetails->row();


                    // $switchStatement = 5;
                    $profilePic = $userDetails->profilePic; // the follower
                    $userName = $userDetails->fullName; //the follower
                    $wishDescription = 'unfollowed you'; 
                    $followerUserID = $userDetails->userID; // the follower ID
                    $created = $followingData->created;
                    $isRead = $value['isRead'];
                break;


                default:
                    # code...
                    $profilePic = '';
                    $userName = '';
                    $wishDescription = '';
                    $description = '';
                    $userData = '';
                    $thumbnail = '';
                    $wishVideoID = '';
                    $videoLink = '';
                    $title = '';
                    $created = '';
                    $isRead = '';
                break;
            }

            // $results[] = array(
            //     "notificationID" => $value['notificationID'],
            //     "notificationTypeID" => $value['notificationTypeID'],
            //     "switchStatement" => $switchStatement
            // );

            if ($value['notificationTypeID']==4 || $value['notificationTypeID']==5) {
                $results[] = array(
                    "profilePic" => $profilePic,
                    "userName" => $userName,
                    "wishDescription" => $wishDescription,
                    "userID" => $followerUserID,
                    "created" => $created,
                    "isRead" => $isRead,
                    "notificationTypeID" => $value['notificationTypeID'],
                );
            } else {
                $results[] = array(
                    "profilePic" => $profilePic,
                    "userName" => $userName,
                    "wishDescription" => $wishDescription,
                    "description" => $description,
                    "userData" => $userData,
                    "thumbnail" => $thumbnail,
                    "videoID" => $wishVideoID,
                    "videoLink" => $videoLink,
                    "title" => $title,
                    "created" => $created,
                    "isRead" => $isRead,
                    "notificationTypeID" => $value['notificationTypeID'],
                );
            }
            

            

            // // wish received
            // {
            //     "profilePic": "http://faarbetterfilms.com/rockabyteServicesV2/uploads/userProfilePicture/aed9e6bf76812b930288c7472037c1e8.jpg", // sender profile picture
            //     "userName": "Prathmesh Angachekar", // sender userName
            //     "wishDescription": "sent you a wish video",
            //     "description": "Hvbinnp inn'hi JFK",
            //     "userData": { // user Data of video change // get userdata
            //         "userID": "3844",
            //         "userName": "Prathmesh Angachekar",
            //         "fullName": "Prathmesh Angachekar",
            //         "profilePic": "http://faarbetterfilms.com/rockabyteServicesV2/uploads/userProfilePicture/aed9e6bf76812b930288c7472037c1e8.jpg"
            //     },
            //     "thumbnail": "http://faarbetterfilms.com/rockabyteServicesV2/uploads/wishVideo/thumbnail/1564596dba3c39b8533d613ddbfc921f.jpg",
            //     "videoID": "468",
            //     "videoLink": "http://faarbetterfilms.com/rockabyteServicesV2/uploads/wishVideo/1564596dba3c39b8533d613ddbfc921f.mp4",
            //     "title": "Test wish",
            //     "created": "2017-01-21 21:11:00",
            //     "createdSort": 1485013260,
            //     "isRead": "1"
            // },

            // // wish sent
            // {
            //     "profilePic": "https://graph.facebook.com/342477939441179/picture?type=large",//receiver profilePic
            //     "userName": "Aditya Dev", // reciever userName
            //     "wishDescription": "Wish sent successfully", // wish description
            //     "description": "", // video// description
            //     "userData": { //user Data of video
            //         "userID": "3823",
            //         "fullName": "Sparsh Turkane ",
            //         "profilePic": "http://faarbetterfilms.com/rockabyteServicesV2/uploads/userProfilePicture/2751ea99420b7cfc3e2cc46302ae8f34.jpg",
            //         "userName": "sparsh_tur"
            //     },
            //     "thumbnail": "http://faarbetterfilms.com/rockabyteServicesV2/uploads/wishVideo/thumbnail/a50b52feadce96a3c273b60b24b94c21.jpg",//video thumbnail
            //     "videoID": "467", //sent videoID
            //     "videoLink": "http://faarbetterfilms.com/rockabyteServicesV2/uploads/wishVideo/a50b52feadce96a3c273b60b24b94c21.mp4",// sent VideoLink
            //     "title": "Test wish ",// sent videoTitle
            //     "created": "2017-01-21 16:57:00", //sent created Videodate
            //     "isRead": "1" //notifications table
            // },
        }

        return $results;

    }


    public function sentWishVideo($userIDSender){
        $sql = "SELECT * FROM wishVideo WHERE userID = ? ORDER BY wishVideoID DESC";
        $query = $this->db->query($sql, array($userIDSender));
        // print_r($query->result_array());
        // now i will have to replicate get user notification
        // Thumbnail if userID with email present then show else blank
        // userData of user whom I sent wishVideo
        //

        $result = array();
        foreach ($query->result_array() as $value) {
                // $userData = array(
                //     'userID'=> $row['userID'],
                //     'userName'=> $row['userName'],
                //     'fullName'=> $row['fullName'],
                //     'profilePic'=> $row['profilePic'],
                // );
            $isReceiverExist = "SELECT * FROM userProfile WHERE email = ?";
            $queryIsReceiverExist = $this->db->query($isReceiverExist,array($value['receiverEmail']));
            if($queryIsReceiverExist->row()){


                $userValue = $queryIsReceiverExist->row();
                // print_r($userValue);
                // $userData = array(
                //     'userID'=> $userValue->userID,
                //     'userName'=> $userValue->fullName,
                //     'fullName'=> $userValue->fullName,
                //     'profilePic'=> $userValue->profilePic,
                // );
                $profilePic = $userValue->profilePic;
                $userName = $userValue->fullName;
            }else{
                // $userData = array(
                //     'userID'=> '',
                //     // 'userName'=> str_replace('.',' ',strstr($value['receiverEmail'], '@', true)),
                //     'userName' => ucwords(str_replace('.',' ',strstr($value['receiverEmail'], '@', true))),
                //     'fullName'=> '',
                //     'profilePic'=> '',
                // );
                $profilePic = '';
                //echo ;
                // $userName = str_replace('.',' ',strstr($value['receiverEmail'], '@', true));
                $userName = ucwords(str_replace('.',' ',strstr($value['receiverEmail'], '@', true)));
            }

            //find video uploader details with the help of userID
            $userDetailSql = "SELECT userID,fullName,profilePic,userName
            FROM userProfile
            WHERE userID = ?";

            $userDetailQuery = $this->db->query($userDetailSql,array($userIDSender));
            $userData = $userDetailQuery->row();

            // Determining if wish is past or future
            $currentTime = date('Y-m-d H:i:s');
            $currentTime = strtotime($currentTime);
            $wishReceiveDateTime = strtotime($value['receiveDateTime']);
            if($currentTime > $wishReceiveDateTime){
                //wish sent
                $wishDescription = "Wish sent successfully";
            }else{
                //wish not sent
                $wishDescription = "Pending";
            }


            $result[]=array(
                'profilePic'=> $profilePic,
                'userName'=> $userName,
                'wishDescription'=> $wishDescription, //will be based on time
                'description' => str_replace('\\n', "\n",$value['description']),
                'userData' => $userData,
                'thumbnail'=> $value['thumbnail'],
                'videoID'=> $value['wishVideoID'],
                'videoLink' =>$value['videoLink'],
                'title' => str_replace('\\n', "\n",$value['title']),
                'created'=> $value['receiveDateTime'],
                'createdSort' => strtotime($value['receiveDateTime']),
                'isRead' =>$value['isRead'],
            );
        }

        // if($result){
        return $result;
        // }
    }


    public function updateWishVideoNotification($userNotificationResult){
        // print_r($userNotificationResult);exit;
        foreach ($userNotificationResult as $value) {
            $wishVideoID = $value['videoID'];
            $sql = "UPDATE wishVideo SET isRead = '1' WHERE wishVideoID=?";
            $query = $this->db->query($sql,array($wishVideoID));
        }
    }

    public function specificWishVideoDetail($videoID){

        $sql = "SELECT *
        FROM wishVideo WHERE WishvideoID = ?";
        $query = $this->db->query($sql,array($videoID));

        foreach ($query->result_array() as $row)
        {
            //userID of video uploader detials
            $userDetailSql = "SELECT userID,fullName,profilePic,userName
            FROM userProfile
            WHERE userID = ?";

            $userDetailQuery = $this->db->query($userDetailSql,array($row['userID']));
            $userData = $userDetailQuery->row();


            //converting duration to mm:ss if hh==00
            $duration = $row['duration'];
            $durationArray = explode(':',$duration);
            if($durationArray[0]=='00'){
                $finalDuration = $durationArray[1].':'.$durationArray[2];
            }else{
                $finalDuration = $durationArray[0].':'.$durationArray[1].':'.$durationArray[2];
            }


            //$createdTime = $this->time_elapsed_string($row['created']);
            $result[]=array(
                'videoID'=>$row['wishVideoID'],
                'userData'=>$userData,
                //'category'=>$categoryDetail,
                'videoLink'=>$row['videoLink'],
                'title'=> str_replace('\\n', "\n",$row['title']),
                'description'=> str_replace('\\n', "\n",$row['description']),
                //'tag'=>$row['tag'],
                //'viewCount'=>$ViewCount,
                //'duration'=>$row['duration'],
                // 'receiverEmail'=>$row['receiverEmail'],
                // 'receiveTime'=>$row['receiveTime'],
                'duration'=>$finalDuration,
                'thumbnail'=>$row['thumbnail'],
                //'likeCount'=>$likeCount,
                //'isActive'=>$row['isActive'],
                //'isRockapick'=>$row['isRockapick'],
                'updated'=>$row['updated'],
                'created'=>$row['receiveDateTime']
                );
        }
        return $result;
    }

    public function searchVideo($userID,$limit,$nextVideo,$keyWord){
        //multiple to multiple data
        $getUserCategory = $this->getUserCategory($userID);
        $nextVideo = $nextVideo - 1;
        $prefix  = 'AND (video.categoryID =';
        $string='';
        foreach ($getUserCategory->result_array() as $row)
        {
            $string=$string .= $prefix . ' ' . $row['categoryID'] . ' ';
            $prefix = 'or video.categoryID=';
        }
        $string=$string.=')';

        if($string == ')'){
            $string='';
        }

        $search="AND ( video.title Like '%".$keyWord."%' OR video.tag Like '%".$keyWord."%' OR userProfile.fullName Like '%".$keyWord."%' OR userProfile.userName Like '%".$keyWord."%')";

        // $sql = "SELECT video.*
        // FROM video
        // INNER JOIN userProfile
        // ON userProfile.newID = video.userID
        // WHERE video.isActive = 1 AND userProfile.isActive = 1 $search";//before $search add $string

        $sql = "SELECT distinct videoID, video.*
        FROM video
        INNER JOIN userProfile
        ON userProfile.newID = video.userID
        WHERE video.isActive = 1 $search";//before $search add $string

        $query = $this->db->query($sql);
        // echo $this->db->last_query();exit;
        $result = $query->result();
        //print_r($result);
        $totalCount = count($result);

        // $sql = "SELECT *
        // FROM video
        // INNER JOIN userProfile
        // ON userProfile.newID = video.userID
        // WHERE video.isActive = 1 AND userProfile.isActive = 1 $search ORDER by video.videoID DESC LIMIT $limit OFFSET $nextVideo ";//before $search add $string

        $sql = "SELECT  distinct videoID, video.*
        FROM video
        INNER JOIN userProfile
        ON userProfile.newID = video.userID
        WHERE video.isActive = 1 $search ORDER by video.videoID DESC LIMIT $limit OFFSET $nextVideo ";//before $search add $string

        $query = $this->db->query($sql);
        //echo($this->db->last_query());exit();
        $result = $query->result();
        //$totalCount = count($result);
        $currentLimit = count($result);
        $nextVideo +=1;
        $nextVideo = $currentLimit + $nextVideo;

        /*-------------Getting each category name----------------------------- */
        $results = array();
        foreach ($query->result_array() as $row)
        {
            //$userID='45';
            $sql1 = "SELECT categoryID,categoryName
            FROM category
            WHERE categoryID = ?";

            $query1 = $this->db->query($sql1,array($row['categoryID']));
            $categoryDetail = $query1->row();

            //userLIke videos
            $likeVideoID =  $row['videoID'];
            $likeSql = "SELECT count(*) AS likeCount FROM userLikeVideo WHERE videoID = ? and likeFlag =1";
            $likeQuery = $this->db->query($likeSql,array($likeVideoID));
            foreach ($likeQuery->result_array() as $likeRow)
            {
                $likeCount = $likeRow['likeCount'];
            }
            //.userLike videos

            //viewCount
            $viewSql = "SELECT count(*) AS ViewCount FROM videoView WHERE videoID = ?";
            $viewQuery = $this->db->query($viewSql,array($likeVideoID));
            foreach ($viewQuery->result_array() as $viewRow)
            {
                $ViewCount = $viewRow['ViewCount'];
            }

            //userID of video uploader detials
            $userDetailSql = "SELECT userID,fullName,profilePic,userName
            FROM userProfile
            WHERE userID = ?";

            $userDetailQuery = $this->db->query($userDetailSql,array($row['userID']));
            $userData = $userDetailQuery->row();
            $userProfileData=$this->api->getUserData2($userID);

            // getting video like flag details
            $likeFlagSql = "SELECT videoID FROM userLikeVideo  WHERE videoID = ? AND likeFlag = 1 AND userID = ?";
            $likeFlagQuery = $this->db->query($likeFlagSql,array($row['videoID'],$userID));
            if ($likeFlagQuery->num_rows() >= 1) {
                $likeFlag = '1';
            } else {
                $likeFlag = '0';
            }

            //converting video tags into an array
            $tagString = $row['tag'];
            $tagArray = array();
            $tagArray = explode(',',$tagString);
            if($tagArray[0]==''){
                unset($tagArray);
                $tagArray = array();
            }


            //converting duration to mm:ss if hh==00
            $duration = $row['duration'];
            $durationArray = explode(':',$duration);
            if($durationArray[0]=='00'){
                $finalDuration = $durationArray[1].':'.$durationArray[2];
            }else{
                $finalDuration = $durationArray[0].':'.$durationArray[1].':'.$durationArray[2];
            }


            //$createdTime = $this->time_elapsed_string($row['created']);
            if(($row['userID']==$userID) || ($row['isPrivate']=='0')){
                $results[]=array(
                'isVideo'=> '1',
                'videoID'=>$row['videoID'],
                'userData'=>$userData,
                'category'=>$categoryDetail,
                'videoLink'=>$row['videoLink'],
                'title'=>$row['title'],
                'description'=>str_replace('\\n', "\n",$row['description']),
                'tag'=>$tagArray,
                'viewCount'=>$ViewCount,
                'duration'=>$finalDuration,
                'thumbnail'=>$row['thumbnail'],
                'likeCount'=>$likeCount,
                'likeFlag'=>$likeFlag,
                'isActive'=>$row['isActive'],
                'isRockapick'=>$row['isRockapick'],
                'updated'=>$row['updated'],
                'created'=>$row['created']
                );
            }else{
                continue;
            }
        }
        /*-------------Getting each category name----------------------------- */
        // echo $totalCount; exit;
        if($nextVideo > $totalCount){
            $nextVideo = -1;
            return array($results,$nextVideo);
        }
        //print_r($result);exit;

        return array($results,$nextVideo);
    }


    public function searchVideoV3($userID,$keyWord){
        //multiple to multiple data
        $getUserCategory = $this->getUserCategory($userID);
        // $nextVideo = $nextVideo - 1;
        $prefix  = 'AND (video.categoryID =';
        $string='';
        foreach ($getUserCategory->result_array() as $row)
        {
            $string=$string .= $prefix . ' ' . $row['categoryID'] . ' ';
            $prefix = 'or video.categoryID=';
        }
        $string=$string.=')';

        if($string == ')'){
            $string='';
        }

        $search="AND ( video.title Like '%".$keyWord."%' OR video.tag Like '%".$keyWord."%' OR userProfile.fullName Like '%".$keyWord."%' OR userProfile.userName Like '%".$keyWord."%')";

        // $sql = "SELECT video.*
        // FROM video
        // INNER JOIN userProfile
        // ON userProfile.newID = video.userID
        // WHERE video.isActive = 1 AND userProfile.isActive = 1 $search";//before $search add $string

        $sql = "SELECT distinct videoID, video.*
        FROM video
        INNER JOIN userProfile
        ON userProfile.newID = video.userID
        WHERE video.isActive = 1 $search";//before $search add $string

        $query = $this->db->query($sql);
        // echo $this->db->last_query();exit;
        $result = $query->result();
        //print_r($result);
        $totalCount = count($result);

        // $sql = "SELECT *
        // FROM video
        // INNER JOIN userProfile
        // ON userProfile.newID = video.userID
        // WHERE video.isActive = 1 AND userProfile.isActive = 1 $search ORDER by video.videoID DESC LIMIT $limit OFFSET $nextVideo ";//before $search add $string

        // $sql = "SELECT  distinct videoID, video.*
        // FROM video
        // INNER JOIN userProfile
        // ON userProfile.newID = video.userID
        // WHERE video.isActive = 1 $search ORDER by video.videoID DESC LIMIT $limit OFFSET $nextVideo ";//before $search add $string

        // $query = $this->db->query($sql);
        // //echo($this->db->last_query());exit();
        // $result = $query->result();
        // //$totalCount = count($result);
        // $currentLimit = count($result);
        // $nextVideo +=1;
        // $nextVideo = $currentLimit + $nextVideo;

        /*-------------Getting each category name----------------------------- */
        $results = array();
        foreach ($query->result_array() as $row)
        {
            //$userID='45';
            $sql1 = "SELECT categoryID,categoryName
            FROM category
            WHERE categoryID = ?";

            $query1 = $this->db->query($sql1,array($row['categoryID']));
            $categoryDetail = $query1->row();

            //userLIke videos
            $likeVideoID =  $row['videoID'];
            $likeSql = "SELECT count(*) AS likeCount FROM userLikeVideo WHERE videoID = ? and likeFlag =1";
            $likeQuery = $this->db->query($likeSql,array($likeVideoID));
            foreach ($likeQuery->result_array() as $likeRow)
            {
                $likeCount = $likeRow['likeCount'];
            }
            //.userLike videos

            //viewCount
            $viewSql = "SELECT count(*) AS ViewCount FROM videoView WHERE videoID = ?";
            $viewQuery = $this->db->query($viewSql,array($likeVideoID));
            foreach ($viewQuery->result_array() as $viewRow)
            {
                $ViewCount = $viewRow['ViewCount'];
            }

            //userID of video uploader detials
            $userDetailSql = "SELECT userID,fullName,profilePic,userName
            FROM userProfile
            WHERE userID = ?";

            $userDetailQuery = $this->db->query($userDetailSql,array($row['userID']));
            $userData = $userDetailQuery->row();
            $userProfileData=$this->api->getUserData2($userID);

            // getting video like flag details
            $likeFlagSql = "SELECT videoID FROM userLikeVideo  WHERE videoID = ? AND likeFlag = 1 AND userID = ?";
            $likeFlagQuery = $this->db->query($likeFlagSql,array($row['videoID'],$userID));
            if ($likeFlagQuery->num_rows() >= 1) {
                $likeFlag = '1';
            } else {
                $likeFlag = '0';
            }

            //converting video tags into an array
            $tagString = $row['tag'];
            $tagArray = array();
            $tagArray = explode(',',$tagString);
            if($tagArray[0]==''){
                unset($tagArray);
                $tagArray = array();
            }


            //converting duration to mm:ss if hh==00
            $duration = $row['duration'];
            $durationArray = explode(':',$duration);
            if($durationArray[0]=='00'){
                $finalDuration = $durationArray[1].':'.$durationArray[2];
            }else{
                $finalDuration = $durationArray[0].':'.$durationArray[1].':'.$durationArray[2];
            }


            //$createdTime = $this->time_elapsed_string($row['created']);
            if(($row['userID']==$userID) || ($row['isPrivate']=='0')){
                $results[]=array(
                'isVideo'=> '1',
                'videoID'=>$row['videoID'],
                'userData'=>$userData,
                'category'=>$categoryDetail,
                'videoLink'=>$row['videoLink'],
                'title'=>$row['title'],
                'description'=>str_replace('\\n', "\n",$row['description']),
                'tag'=>$tagArray,
                'viewCount'=>$ViewCount,
                'duration'=>$finalDuration,
                'thumbnail'=>$row['thumbnail'],
                'likeCount'=>$likeCount,
                'likeFlag'=>$likeFlag,
                'isActive'=>$row['isActive'],
                'isRockapick'=>$row['isRockapick'],
                'updated'=>$row['updated'],
                'created'=>$row['created']
                );
            }else{
                continue;
            }
        }
        /*-------------Getting each category name----------------------------- */
        // echo $totalCount; exit;
        // if($nextVideo > $totalCount){
        //     $nextVideo = -1;
        //     return array($results,$nextVideo);
        // }
        //print_r($result);exit;

        // return array($results,$nextVideo);
        return array($results);
    }


    public function searchUserV3($userID,$keyWord)
    {
        // parameters
        /**
         *
         1. keyWord = Get user matching that keyword
         2. userID = except the current userID (" will not be showing user its own profile")
         */

        $search="AND ( userProfile.fullName Like '%".$keyWord."%' OR userProfile.userName Like '%".$keyWord."%')";
        $notEqualTo = "AND userID <> $userID";

        $sql = "SELECT * FROM userProfile WHERE isActive = 1 $search $notEqualTo";
        $query = $this->db->query($sql);

        // if ($query->num_rows() >= 1) {
        //     return $query->result_array();
        // } else {
        //     return false;
        // }
        $results = array();
        foreach ($query->result_array() as $row)
        {
            $results[]=array(
                "isVideo"=> '0',
                "userID"=> $row['userID'],
                "fullName"=> $row['fullName'],
                "userName"=> $row['userName'],
                "occupation"=> $row['occupation'],
                "phoneNo"=> $row['phoneNo'],
                "gender"=> $row['gender'],
                "email"=> $row['email'],
                "password"=> $row['password'],
                "profilePic"=> $row['profilePic'],
                "country"=> $row['country'],
                "dob"=> $row['dob'],
                "state"=> $row['state'],
                "aboutMe"=> $row['aboutMe'],
                "accessToken"=> $row['accessToken'],
                "androidKey"=> $row['androidKey'],
                "iosKey"=> $row['iosKey'],
                "isActive"=> $row['isActive'],
                "forgotPassword"=> $row['forgotPassword'],
                "validationLink"=> $row['validationLink'],
                "isVerified"=> $row['isVerified'],
                "twitterHandler"=> $row['twitterHandler'],
                "newID"=> $row['newID'],
                "lastLogin"=> $row['lastLogin'],
                "updated"=> $row['updated'],
                "created"=> $row['created']
                );
        }
        return $results;


    }

    public function storeSearchKeyword($userID,$keyWord,$created){
        $sql = "INSERT INTO searchHistory (userID,keyword,created) VALUES (?,?,?)";
        $query = $this->db->query($sql,array($userID,$keyWord,$created));
    }

    // public function insertVideoViewCount($userID,$videoID){
    //     $created = date('Y-m-d H:i:s');
    //     $sql = "INSERT INTO videoView (videoID,userID,created)
    //         VALUES (? ,? ,?)";
    //     $query = $this->db->query($sql,array($videoID, $userID, $created));
    // }

    public function getViewCount($videoID){
        $viewSql = "SELECT count(*) AS ViewCount FROM videoView WHERE videoID = ?";
        $viewQuery = $this->db->query($viewSql,array($videoID));

        foreach ($viewQuery->result_array() as $viewRow)
        {
                $ViewCount = $viewRow['ViewCount'];
        }
        return $ViewCount;
    }

    public function getStateList($countryID){
        $sql = "SELECT * FROM state WHERE countryID = ?";
        $query = $this->db->query($sql,array($countryID));
        if ($query->num_rows() >= 1) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function getCountryList(){
        $sql = "SELECT * FROM country ";
        $query = $this->db->query($sql);
        if ($query->num_rows() >= 1) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function getUserIDviaEmail($email){
        if($email){
            $sql = "SELECT * FROM userProfile WHERE email = ? ";
            $query = $this->db->query($sql,array($email));
            if ($query->num_rows() >= 1) {
                return $query->result_array();
            } else {
                return false;
            }
        }
    }

    public function forgotPasswordUserID($forgotPassword){
        $sql = "SELECT * FROM userProfile WHERE forgotPassword = ? ";
        $query = $this->db->query($sql,array($forgotPassword));
        if ($query->num_rows() >= 1) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function emailValidationLink($validationLink){
        $sql = "SELECT * FROM userProfile WHERE validationLink = ? ";
        $query = $this->db->query($sql,array($validationLink));
        if ($query->num_rows() >= 1) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function isUserVerified($userID){
        $sql = "SELECT userID, isVerified, created FROM userProfile WHERE userID = ? ";
        $query = $this->db->query($sql,array($userID));

        // echo "<pre>";
        // print_r($query->result_array());
        foreach ($query->result_array() as $value) {
            $queryUserData = array(
                'userID' => $value['userID'],
                'isVerified' => $value['isVerified'],
                'created' => $value['created'],
            );
        }

        if($queryUserData['isVerified']==1){
            return true;
        }else{
            //checking if day is greater than five day
            $createdDate = $queryUserData['created'];
            $dateNow = date('Y-m-d H:i:s');

            $createdDate = strtotime($createdDate);
            $greater5 = strtotime("+5 day", $createdDate);

            $dateNow = strtotime($dateNow);

            if($dateNow > $greater5){
                return false;
            }else{
                return true;
            }
        }
        // print_r($queryUserData);
    }

    public function updateVideo($userID,$videoID,$updateData){
        $this->db->where('userID', $userID);
        $this->db->where('videoID', $videoID);
        $this->db->update('video', $updateData);
        return ($this->db->affected_rows() > 0) ? true : false;
    }


    public function reportVideo($userID,$videoID){
        // $this->db->where('userID', $userID);
        // $this->db->where('videoID', $videoID);
        // $this->db->update('video', $updateData);
        $created = date('Y-m-d H:i:s');
        $sql = "INSERT INTO reportVideo (userID,videoID,created) VALUES (?,?,?)";
        $query = $this->db->query($sql,array($userID,$videoID,$created));

        // $sql1 = "UPDATE video set isReported = ? WHERE videoID = ?";
	$sql1 = "UPDATE video set isReported = ?, reportCount = reportCount + 1 WHERE videoID = ?";
        $query1 = $this->db->query($sql1,array('1',$videoID));

        return ($this->db->affected_rows() > 0) ? true : false;
    }


    public function isVideoReported($userID,$videoID)
    {
        $sql = "SELECT * FROM reportVideo WHERE userID = ? AND videoID = ?";
        $query = $this->db->query($sql,array($userID,$videoID));
        if ($query->num_rows() >= 1) {
            return true;
        } else {
            return false;
        }

    }


    // public function replaceSpaceEmail(){
    //     $sql = "SELECT userID,email FROM userProfile";
    //     $query = $this->db->query($sql);
    //     echo "<pre>";
    //     print_r($query->result_array());
    // }


    public function updateConvertedVideoLink(){

        $sql1 = "SELECT * FROM `video` WHERE isUpdatedLink = ?";
        $query1 = $this->db->query($sql1,array('0'));
        // print_r($query1->result_array());

        foreach ($query1->result_array() as $value) {
            //---------------------
            $formated1 = str_replace("thumbnail/","",$value['thumbnail']);
            $videoLinkUpdated = str_replace("jpg","mp4",$formated1);
            //---------------------

            //converting video duration to seconds * 2
            $videoDurationSeconds =  strtotime($value['duration']) - strtotime('TODAY');
            $videoDurationSeconds = $videoDurationSeconds * 2;

            //adding seconds * 2 video created date
            $completedTime = date('Y-m-d H:i:s',strtotime("{$videoDurationSeconds} seconds",strtotime($value['created'])));

            //declaring current time and approximateConvertedTime
            $approCompletedTime = strtotime($completedTime);
            $ctime = date('Y-m-d H:i:s');
            $currentTime = strtotime($ctime);

            //updating if current time is > approximate completed time
            if($currentTime > $approCompletedTime){
                $sql = "UPDATE video SET videoLink = ?,isUpdatedLink = ? WHERE videoID = ? ";
                $query = $this->db->query($sql,array($videoLinkUpdated,'1',$value['videoID']));
            }
        }
    }


    public function updateConvertedWishVideoLink(){
        $sql1 = "SELECT * FROM `wishVideo` WHERE isUpdatedLink = ?";
        $query1 = $this->db->query($sql1,array('0'));
        // print_r($query1->result_array());

        foreach ($query1->result_array() as $value) {
            //---------------------
            $formated1 = str_replace("thumbnail/","",$value['thumbnail']);
            $videoLinkUpdated = str_replace("jpg","mp4",$formated1);
            //---------------------

            //converting video duration to seconds * 2
            $videoDurationSeconds =  strtotime($value['duration']) - strtotime('TODAY');
            $videoDurationSeconds = $videoDurationSeconds * 2;

            //adding seconds * 2 video created date
            $completedTime = date('Y-m-d H:i:s',strtotime("{$videoDurationSeconds} seconds",strtotime($value['created'])));

            //declaring current time and approximateConvertedTime
            $approCompletedTime = strtotime($completedTime);
            $ctime = date('Y-m-d H:i:s');
            $currentTime = strtotime($ctime);

            //updating if current time is > approximate completed time
            if($currentTime > $approCompletedTime){
                $sql = "UPDATE wishVideo SET videoLink = ?,isUpdatedLink = ? WHERE wishVideoID = ? ";
                $query = $this->db->query($sql,array($videoLinkUpdated,'1',$value['wishVideoID']));
            }
        }
    }


    public function extraUpdateDuration(){
        //start by getting videoID, link,
        //run ffmpeg
        //update database values where videoID


        // $time = exec("ffmpeg -i {$input} 2>&1 | grep Duration | cut -d ' ' -f 4 | sed s/,//");
        // //HH:MM:SS.miliseconds
        // // echo $time;
        // return $time;

        $sql = "SELECT * FROM `video`";
        $query = $this->db->query($sql);
        // print_r($query->result_array());
        // http://188.166.250.72/rockabyte/admin/uploads/video/j8ZqxaIEtest2.mp4

        foreach ($query->result_array() as $value) {
            $videoLink = $value['videoLink'];
            $videoPath = str_replace('http://188.166.250.72/','/var/www/html/',$videoLink);
            // echo $videoPath; echo "<br>";
            $time = exec("ffmpeg -i {$videoPath} 2>&1 | grep Duration | cut -d ' ' -f 4 | sed s/,//");

            // UPDATE Customers
            // SET City='Hamburg'
            // WHERE CustomerID=1;
            // echo $time; exit;
            if(!empty($time)){
                $timeSql = "UPDATE video SET duration = ? WHERE videoID = ?";
                $timeQuery = $this->db->query($timeSql,array($time,$value['videoID']));
            }
        }

    }


    public function extraUpdateDurationWish(){
        $sql = "SELECT * FROM `wishVideo` WHERE wishVideoID < '283'";
        $query = $this->db->query($sql);
        // print_r($query->result_array());
        // http://188.166.250.72/rockabyte/admin/uploads/video/j8ZqxaIEtest2.mp4

        foreach ($query->result_array() as $value) {
            $videoLink = $value['videoLink'];
            $videoPath = str_replace('http://188.166.250.72/','/var/www/html/',$videoLink);
            // echo $videoPath; echo "<br>";
            $time = exec("ffmpeg -i {$videoPath} 2>&1 | grep Duration | cut -d ' ' -f 4 | sed s/,//");

            // UPDATE Customers
            // SET City='Hamburg'
            // WHERE CustomerID=1;
            // echo $time; exit;
            if(!empty($time)){
                $timeSql = "UPDATE wishVideo SET duration = ? WHERE wishVideoID = ?";
                $timeQuery = $this->db->query($timeSql,array($time,$value['wishVideoID']));
            }
        }
    }


    public function extraUpdateThumbnailVideo(){

        $sql = "SELECT * FROM `video` WHERE isActive = '1' AND videoID < '287' ";
        $query = $this->db->query($sql);
        // print_r($query->result_array());
        // http://188.166.250.72/rockabyte/admin/uploads/video/j8ZqxaIEtest2.mp4

        foreach ($query->result_array() as $value) {
            $videoLink = $value['videoLink'];

            $videoPath = str_replace('http://faarbetterfilms.com/','/var/www/html/',$videoLink);
            $thumbnailPath = str_replace('/var/www/html/rockabyte/admin/uploads/video/','/var/www/html/rockabyte/admin/uploads/video/thumbnail/',$videoPath);


            $thumbnailPathJpg = substr($thumbnailPath, 0, strpos($thumbnailPath, "."));
            //$thumbnailPathJpg =
            $thumbnailPathJpg = $thumbnailPathJpg . '.jpg';

            $thumbnailLink = str_replace('/var/www/html/','http://faarbetterfilms.com/',$thumbnailPathJpg);
            // call a function to create thumbnail

            $input = $videoPath;
            $output = $thumbnailPathJpg;

            $status = $this->extraCreateThumbnail($input, $output, '1');
            if($status){
                $timeSql = "UPDATE video SET thumbnail = ? WHERE videoID = ?";
                $timeQuery = $this->db->query($timeSql,array($thumbnailLink,$value['videoID']));
            }
        }
    }


    public function extraUpdateThumbnailWishVideo(){

        $sql = "SELECT * FROM `wishVideo` WHERE wishVideoID >'342' ";
        $query = $this->db->query($sql);
        // print_r($query->result_array());
        // http://188.166.250.72/rockabyte/admin/uploads/video/j8ZqxaIEtest2.mp4

        foreach ($query->result_array() as $value) {
            $videoLink = $value['videoLink'];

            $videoPath = str_replace('http://faarbetterfilms.com/','/var/www/html/',$videoLink);
            $thumbnailPath = str_replace('/var/www/html/rockabyteServicesV2/uploads/wishvideo/','/var/www/html/rockabyteServicesV2/uploads/wishvideo/thumbnail/',$videoPath);


            $thumbnailPathJpg = substr($thumbnailPath, 0, strpos($thumbnailPath, "."));
            //$thumbnailPathJpg =
            $thumbnailPathJpg = $thumbnailPathJpg . '.jpg';

            $thumbnailLink = str_replace('/var/www/html/','http://faarbetterfilms.com/',$thumbnailPathJpg);
            // call a function to create thumbnail

            $input = $videoPath;
            $output = $thumbnailPathJpg;

            $status = $this->extraCreateThumbnail($input, $output, '1');
            if($status){
                $timeSql = "UPDATE wishVideo SET thumbnail = ? WHERE wishVideoID = ?";
                $timeQuery = $this->db->query($timeSql,array($thumbnailLink,$value['wishVideoID']));
            }
        }
    }


    private function extraCreateThumbnail($input, $output, $fromdurasec){


        // $command = "ffmpeg -ss -{$fromdurasec} -i {$input} -y -lavfi '[0:v]scale=ih*16/9:-1,boxblur=luma_radius=min(h\,w)/20:luma_power=1:chroma_radius=min(cw\,ch)/20:chroma_power=1[bg];[bg][0:v]overlay=(W-w)/2:(H-h)/2,crop=h=iw*9/16' -strict -2 -vb 800K -vframes 1 {$output}";


        // exec($command);

        // $portout = $output
        $portout = strstr($output, '.', true);
        $portout = $portout.'-portrait'.'.jpg';
        // echo $portout;exit;



        $command = "ffmpeg -ss -{$fromdurasec} -i {$input} -y -lavfi '[0:v]scale=ih*16/9:-1,boxblur=luma_radius=min(h\,w)/20:luma_power=1:chroma_radius=min(cw\,ch)/20:chroma_power=1[bg];[bg][0:v]overlay=(W-w)/2:(H-h)/2,crop=h=iw*9/16' -strict -2 -vb 800K -vframes 1 {$portout}";

        exec($command);

        if (file_exists($portout)){
            $portfinalout = str_replace('-portrait','',$portout);
            $command = "ffmpeg -i {$portout} -y -vf scale=500:300 {$portfinalout}";
            // $command = "ffmpeg  -itsoffset -4  -i {$portout} -y -vcodec mjpeg -vframes 1 -an -f rawvideo -s scale=500:-1 {$portfinalout} ";

            exec($command);

        }


        $fromdurasec = 1;
        if (!file_exists($output)){
            $command = "ffmpeg -itsoffset -{$fromdurasec} -i {$input} -y -vframes 1 -filter:v scale='500:-1' {$output}";
            exec($command);
        }
        if (filesize($output) == 0){
            $command = "ffmpeg -itsoffset -{$fromdurasec} -i {$input} -y -vframes 1 -filter:v scale='500:-1' {$output}";
            exec($command);
        }


        if (!file_exists($output)){
            return false;
        }else if (filesize($output) == 0){
            return false;
        }else{
            return true;
        }

    }

    public function extraUpdateVideoLink(){
        $sql = "SELECT * FROM `video` WHERE videoID < '302' ";
        $query = $this->db->query($sql);
        // print_r($query->result_array());
        // http://188.166.250.72/rockabyte/admin/uploads/video/j8ZqxaIEtest2.mp4
        //http://faarbetterfilms.com/rockabyteServicesV2/uploads/video/38542b45596e8edd2e84f63634f60530.mp4


        foreach ($query->result_array() as $value) {
            $videoLink = $value['videoLink'];
            $videoPath = str_replace('http://188.166.250.72/','http://faarbetterfilms.com/',$videoLink);

            $timeSql = "UPDATE video SET videoLink = ? WHERE videoID = ?";
            $timeQuery = $this->db->query($timeSql,array($videoPath,$value['videoID']));
        }
    }


    public function extraUpdateWishVideoLink(){
        $sql = "SELECT * FROM `wishVideo` WHERE wishVideoID < '283' ";
        $query = $this->db->query($sql);
        // print_r($query->result_array());
        // http://188.166.250.72/rockabyte/admin/uploads/video/j8ZqxaIEtest2.mp4
        //http://faarbetterfilms.com/rockabyteServicesV2/uploads/video/38542b45596e8edd2e84f63634f60530.mp4


        foreach ($query->result_array() as $value) {
            $videoLink = $value['videoLink'];
            echo $videoLink; exit;
            $videoPath = str_replace('http://188.166.250.72/','http://faarbetterfilms.com/',$videoLink);

            $timeSql = "UPDATE wishVideo SET videoLink = ? WHERE wishVideoID = ?";
            $timeQuery = $this->db->query($timeSql,array($videoPath,$value['wishVideoID']));
        }
    }


    public function extraInterestAllSelected(){
        $sql = "SELECT * FROM userProfile WHERE isActive = 1 AND userID < '3801'";
        $query = $this->db->query($sql);

        // foreach ($query->result_array() as $value) {
        //     // echo $value['userID'];echo "<br>";
        //     // foreach update

        //     INSERT INTO `userInterest` (`userID`, `categoryID`, `isSelected`, `updated`,`created`) values ('3800', '2','1','now()','now()') ON DUPLICATE KEY UPDATE `userID` = '3800'
        // }

        $categoryIDArray = ['1','2','3','4','5','6'];
        // print_r($categoryIDArray);
        foreach ($query->result_array() as $value) {

            $userID = $value['userID'];
            foreach ($categoryIDArray as $value) {
                $categoryID = $value;
                // print_r($userID);echo "<br>";
                // print_r($categoryID);echo "<br>";
                $isInterestPresent = $this->isInterestPresent($userID,$categoryID);

                //true means user with this category exists
                if($isInterestPresent){
                    //we will have to use update query
                    $isSelectUpdate = $this->isSelectUpdate($userID,$categoryID,'1');
                }else{
                    //we will have to use insert query
                    $insertUserInterest = $this->insertUserInterest($userID,$categoryID,'1');
                }
            }
        }
    }


    // public function pankajClearData($userName,$keyword)
    // {
    //     // $this->db->where('userID', $userName);
    //     // $this->db->delete('socialLogin');

    //     $sql = "DELETE FROM socialLogin WHERE userID=? and socialType = ?";
    //     $query = $this->db->query($sql,array($userName,$keyword));
    // }


    public function commonSocialType($oldUserID ,$newUserID){
        $oldUserIDSocialSync = $this->socialLoginData($oldUserID);
        $newUserIDSocialSync = $this->socialLoginData($newUserID);

        // print_r($oldUserIDSocialSync);
        // echo "<br>";
        // print_r($newUserIDSocialSync);
        // exit();

        // ($oldUserIDSocialSync['facebook']==$newUserIDSocialSync['facebook'])||($oldUserIDSocialSync['twitter']==$newUserIDSocialSync['twitter'])||($oldUserIDSocialSync['google']==$newUserIDSocialSync['google'])

        if(($oldUserIDSocialSync['facebook']==$newUserIDSocialSync['facebook'])||($oldUserIDSocialSync['twitter']==$newUserIDSocialSync['twitter'])||($oldUserIDSocialSync['google']==$newUserIDSocialSync['google'])){
            // print_r("true");exit;
            return true;
        }else{
            // print_r("false");exit;
            return false;
        }
    }

    // phase V3

    public function updateFollowing($userID, $followUserID, $updateData)
    {

        // $data = array(
        //     'isFollowing' => $isFollowing
        // );

        $this->db->where('userID', $userID);
        $this->db->where('followUserID', $followUserID);
        $this->db->update('following', $updateData);
        // echo $this->db->last_query();exit;
    }

    public function insertFollowing($insertData)
    {
        // $data = array(
        //     'userID' => $userID,
        //     'followUserID' => $followUserID,
        //     'isFollowing' => $isFollowing
        // );

        $this->db->insert('following', $insertData);
        // echo $this->db->last_query();exit;
    }


    public function selectFromFollowing($userID, $followUserID)
    {
        $sql = "SELECT * FROM following WHERE userID = ? AND followUserID = ?";
        $query = $this->db->query($sql, array($userID, $followUserID));
        // echo $this->db->last_query();exit;
        $row = $query->row();
        if($query->num_rows()>0){
            // $res = $query->row_array();
            return $row->followingID;
        } else {
            return 0;
        }
    }

    public function getFollowingList($userID)
    {
        $sqlFollowing = "SELECT * FROM following WHERE userID = ? AND isFollowing = ?";
        $queryFollowing = $this->db->query($sqlFollowing, array($userID,'1'));

        // echo $this->db->last_query();
        // print_r($queryFollowing->result_array());


        $results = array();
        foreach ($queryFollowing->result_array() as $value) {
            $sql = "SELECT * FROM userProfile WHERE isActive = 1 AND userID = ?";
            $query = $this->db->query($sql, array($value['followUserID']));

            // echo $this->db->last_query();
            $row = $query->row_array();
            $results[]=array(
                "userID"=> $row['userID'],
                "fullName"=> $row['fullName'],
                "userName"=> $row['userName'],
                "occupation"=> $row['occupation'],
                "phoneNo"=> $row['phoneNo'],
                "gender"=> $row['gender'],
                "email"=> $row['email'],
                "password"=> $row['password'],
                "profilePic"=> $row['profilePic'],
                "country"=> $row['country'],
                "dob"=> $row['dob'],
                "state"=> $row['state'],
                "aboutMe"=> $row['aboutMe'],
                "accessToken"=> $row['accessToken'],
                "androidKey"=> $row['androidKey'],
                "iosKey"=> $row['iosKey'],
                "isActive"=> $row['isActive'],
                "forgotPassword"=> $row['forgotPassword'],
                "validationLink"=> $row['validationLink'],
                "isVerified"=> $row['isVerified'],
                "twitterHandler"=> $row['twitterHandler'],
                "newID"=> $row['newID'],
                "lastLogin"=> $row['lastLogin'],
                "updated"=> $row['updated'],
                "created"=> $row['created']
            );
        }

        return $results;
    }

    public function getFollowerList($userID)
    {
        $sqlFollowing = "SELECT * FROM following WHERE followUserID = ? AND isFollowing = ?";
        $queryFollowing = $this->db->query($sqlFollowing, array($userID,'1'));

        // echo $this->db->last_query();
        // print_r($queryFollowing->result_array());


        $results = array();
        foreach ($queryFollowing->result_array() as $value) {
            $sql = "SELECT * FROM userProfile WHERE isActive = 1 AND userID = ?";
            $query = $this->db->query($sql, array($value['userID']));

            // echo $this->db->last_query();
            $row = $query->row_array();
            $results[]=array(
                "userID"=> $row['userID'],
                "fullName"=> $row['fullName'],
                "userName"=> $row['userName'],
                "occupation"=> $row['occupation'],
                "phoneNo"=> $row['phoneNo'],
                "gender"=> $row['gender'],
                "email"=> $row['email'],
                "password"=> $row['password'],
                "profilePic"=> $row['profilePic'],
                "country"=> $row['country'],
                "dob"=> $row['dob'],
                "state"=> $row['state'],
                "aboutMe"=> $row['aboutMe'],
                "accessToken"=> $row['accessToken'],
                "androidKey"=> $row['androidKey'],
                "iosKey"=> $row['iosKey'],
                "isActive"=> $row['isActive'],
                "forgotPassword"=> $row['forgotPassword'],
                "validationLink"=> $row['validationLink'],
                "isVerified"=> $row['isVerified'],
                "twitterHandler"=> $row['twitterHandler'],
                "newID"=> $row['newID'],
                "lastLogin"=> $row['lastLogin'],
                "updated"=> $row['updated'],
                "created"=> $row['created']
            );
        }

        return $results;
    }

    public function insertNotifications($insertData)
    {
        # code...
        $this->db->insert('notifications', $insertData);
    }

    public function selectFromNotification($userID)
    {
        $sql = "SELECT * FROM notifications WHERE userID = ?";
        $query = $this->db->query($sql, array($userID));
        if($query->num_rows()>0){
            // $res = $query->row_array();
            return true;
        } else {
            return false;
        }
    }

}
