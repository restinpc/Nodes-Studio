<?php
/**
* firebase-messaging-sw.js script page.
* @path /engine/code/firebase.php
*
* @name    Nodes Studio    @version 2.0.8
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*/
require_once("engine/nodes/mysql.php");
require_once("engine/nodes/session.php");
Header("Content-type: application/x-javascript");
$query = 'SELECT * FROM `nodes_config` WHERE `name` = "firebase_sender_id"';
$res = engine::mysql($query);
$data = mysql_fetch_array($res);
$sender_id = $data["value"];
if(empty($sender_id)) die();
?>
importScripts('https://www.gstatic.com/firebasejs/3.7.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/3.7.2/firebase-messaging.js');
firebase.initializeApp({
    'messagingSenderId': '<?php echo $sender_id; ?>'
});
firebase.messaging();
self.addEventListener('notificationclick', function(event) {
    var target = event.notification.data.click_action || '/';
    event.notification.close();
    event.waitUntil(clients.matchAll({
        type: 'window',
        includeUncontrolled: true
    }).then(function(clientList) {
        for (var i = 0; i < clientList.length; i++) {
            var client = clientList[i];
            if (client.url == target && 'focus' in client) {
                return client.focus();
            }
        }
        return clients.openWindow(target);
    }));
});