<?php
require('config.php');
  function CZ_prepare_mail($contact_zero_config){
    //check required fields
    function check_req(){
      //returns an error respective to the first encountered issue
      //if no issues are encountered it returns the string "PASS" 
      //returning "PASS" will let the emails send
      //these values should also be checked via JS on the front-end before $_POST
      //
      //First we need to make sure that the user has entered a valid email and is not hijacking our mail server by injecting CC's
      if (!$_POST["user_email"] || !filter_var($_POST["user_email"], FILTER_VALIDATE_EMAIL)){
        return "user_email";
      }
      if (!$_POST["user_name"]){
        return "user_name";
      }
      if (!$_POST["user_msg"]){
        return "user_msg";
      }
      return "PASS";
  }//end check_req()
  function prepare_CC($contact_zero_config){
    $res = "";
    foreach ($contact_zero_config["CC"] as &$value) {
      if ($value!="Array"){
        $res.='Cc:'.$value."\r\n";
      }
    }
    //echo $res;//DEBUG
    return($res);
  }//end prepare_CC()
  function prepare_msg($sub_info){
    //this could do without being a function but it helps with organization
      $msg = "NAME: ".$sub_info["user_name"]."\r\n";
      $msg .= "EMAIL: ".$sub_info["user_email"]."\r\n";
      $msg .= "PHONE: ".$sub_info["user_phone"]."\r\n";
      $msg .= "SUBJECT: ".$sub_info["user_subject"]."\r\n";
      $msg .= "MESSAGE: ".$sub_info["user_msg"]."\r\n";
      return($msg);
  }//end prepare_msg()
  //
  //
  //
  //
  //dependant on all above functions
  function send_email($contact_zero_config){
    if (check_req()=="PASS"){
      $sub_info = array(
        "user_name" => $_POST["user_name"],
        "user_email" => $_POST["user_email"],
        "user_phone" => $_POST["user_phone"],
        "user_subject" => $_POST["user_subject"],
        "user_msg" => $_POST["user_msg"],
      );
      $CC_list = prepare_CC($contact_zero_config);
      $final_msg = prepare_msg($sub_info);
      $subject_line = "New Message From ".$sub_info["user_name"]." About ".$sub_info["user_subject"].".";
      /////////////////////////
      //DATABASE write should come before mail() incase the send fails
      //
      //
      //PUT DB CODE HERE
      //needs to protect against SQL injection
      //writes the message data to the DB here
      //
      //if ($contact_zero_config["Master_backup"]==true){//DO BACKUP}
      /////////////////////////
      //
      //ATTEMPT TO SEND MAIL
      if(mail($contact_zero_config["Master_email"], $subject_line, $final_msg, $CC_list)){
        return "SENT";
      }
      else{
        return "ERROR WITH: mail()";
      }
    }
    else{
      //maybe check_req() should be cached in a variable instead of asking again
      return "ERROR WITH:".check_req();
    }
  }//end send_email();
  //attemts to send the email and returns the response, 
  //"SENT" if it worked and an error if not
  return send_email($contact_zero_config);//DO IT!!!
}//end 

echo CZ_prepare_mail($contact_zero_config);
