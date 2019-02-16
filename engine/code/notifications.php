<?php
/**
* Firebase notifications processor.
* @path /engine/code/notifications.php
*
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*/
require_once("engine/nodes/mysql.php");
require_once("engine/nodes/session.php");
header("Content-type: text/javascript");
if(!empty($_POST["token"])){
    $token = engine::escape_string($_POST["token"]);
    $query = 'SELECT * FROM `nodes_firebase` WHERE `token` = "'.$token.'"';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    if(!empty($data) && $data["user_id"] != $_SESSION["user"]["id"]){
        $query = 'UPDATE `nodes_firebase` SET `user_id` = "'.$_SESSION["user"]["id"].'" WHERE `token` = "'.$token.'"';
        engine::mysql($query);
    }else if(empty($data)){
        $query = 'INSERT INTO `nodes_firebase`(`user_id`, `token`, `lang`) '
                . 'VALUES("'.$_SESSION["user"]["id"].'", "'.$token.'", "'.$_SESSION["Lang"].'")';
        engine::mysql($query);
    } 
    die();
}
$query = 'SELECT * FROM `nodes_config` WHERE `name` = "firebase_sender_id"';
$res = engine::mysql($query);
$data = mysqli_fetch_array($res);
$sender_id = $data["value"];
if(empty($sender_id)) die();
?>
firebase.initializeApp({
    'messagingSenderId': '<?php echo $sender_id; ?>'
});
if ('Notification' in window) {
    var messaging = firebase.messaging();
    subscribe();
}
function subscribe() {
    messaging.requestPermission()
        .then(function () {
            messaging.getToken()
                .then(function (currentToken) {
                    if (currentToken) {
                        sendTokenToServer(currentToken);
                    } else {
                        console.warn('Unable to receive token');
                        setTokenSentToServer(false);
                    }
                })
                .catch(function (err) {
                    console.warn('Error while receiving token.', err);
                    setTokenSentToServer(false);
                });
    });
}
function sendTokenToServer(currentToken) {
    if (!isTokenSentToServer(currentToken) || <?php echo intval($_SESSION["user"]["id"]); ?>) {
        var url = '<?php echo $_SERVER["DIR"]; ?>/notifications.php';
        jQuery.post(url, { token: currentToken });
        setTokenSentToServer(currentToken);
    }
}
function isTokenSentToServer(currentToken) {
    return window.localStorage.getItem('sentFirebaseMessagingToken') == currentToken;
}
function setTokenSentToServer(currentToken) {
    window.localStorage.setItem(
        'sentFirebaseMessagingToken',
        currentToken ? currentToken : ''
    );
}