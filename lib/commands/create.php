<?php
function pf_create($argv) {
    $phpfog = new PHPFog();

    switch (strtolower(array_shift($argv))) {
        case "app":

            $raw_cloud_id = array_shift($argv);

            if (is_numeric($raw_cloud_id)) {
                $cloud_id = intval($raw_cloud_id);
                $domain_name = array_shift($argv);
            } else {
                $domain_name = $raw_cloud_id;
            }

            $jump_start_id = 16;
            $app_username = "Custom App";

            do {
                if (empty($domain_name)) $domain_name = prompt("Domain Name:");
                if (empty($domain_name)) { failure_message('New app aborted.'); return true; }

                if (!$phpfog->domain_available($domain_name)) {
                    failure_message('Domain name not available. Try again.');
                    $domain_name = null;
                }
            } while(empty($domain_name));

            while (empty($mysql_password)) {
                $pass1 = prompt("MySQL Password: ", true);
                if (empty($pass1)) { failure_message('New app aborted.'); return true; }
                $pass2 = prompt("Re-enter MySQL Password: ", true);

                if ($pass1 != $pass2) {
                    failure_message('Passwords did not match. Try again.');
                } else {
                    $mysql_password = $pass1;
                }
            }

            try {
                $response = $phpfog->create_app($cloud_id, $jump_start_id, $app_username, $mysql_password, $domain_name);
                echo "New app created: ";
                echo_item("{$domain_name}", $response['id']);
            } catch (Exception $e) {
                $raw = $phpfog->last_response();
                $response = json_decode($raw['body'], true);
                failure_message($response['message']);
            }

        default:
            return false;
    }
}