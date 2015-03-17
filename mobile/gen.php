<?php
/*
This little script requires PHP > 5.2.
Usage: Put this and the included index.html page into a directory on your server,
the the variables on lines 10-12 and enjoy an autoconfigured iPhone, iPod or iPad.
This process is cryptically defined at:
https://developer.apple.com/library/ios/featuredarticles/iPhoneConfigurationProfileRef/Introduction/Introduction.html
*/
#Set these
$hosting_company_name = 'Your Company Name';
$hosting_company_domain = 'YourDomain.com';
$hosting_company_support_url = 'http://www.YourDomain.com';

############################################
#You shouldn't need to touch anything else.#
############################################
#phpinfo();
#exit();
$error = '';
if (!filter_var($_POST['EmailAddress'], FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format\n";
} else {
        $email_address = $_POST['EmailAddress'];
}
if ($error != '') {
        die($error);
}
$full_name = $_POST['FullName'];
$company_name = $_POST['CompanyName'];
$parts = explode('@', $email_address);
$user = $parts[0];
$domain = $parts[1];
$mail_server = "mail." . $domain;
$reverse_domain = array_reverse(explode('.', $domain));
$reverse_hosting_company_domain = array_reverse(explode('.', $hosting_company_domain));
header('Content-type: application/x-apple-aspen-config; chatset=utf-8');
header('Content-Disposition: attachment; filename="company.mobileconfig"');
$incoming_type = 'pop';
$incoming_port = '995';
if ($_REQUEST["incoming"] == 'pop') {
        $incoming_type = 'EmailTypePOP';
        $incoming_port = '995';
} elseif ($_REQUEST["incoming"] == 'imap') {
        $incoming_type = 'EmailTypeIMAP';
        $incoming_port = '993';
}
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>

<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
        <key>PayloadContent</key>
        <array>
                <dict>
                        <key>EmailAccountDescription</key>
                        <string><?php echo $full_name . " - " . $company_name; ?></string>
                        <key>EmailAccountName</key>
                        <string><?php echo $email_address; ?></string>
                        <key>EmailAccountType</key>
                        <string><?php echo $incoming_type; ?></string>
                        <key>EmailAddress</key>
                        <string><?php echo $email_address; ?></string>
                        <key>IncomingMailServerAuthentication</key>
                        <string>EmailAuthPassword</string>
                        <key>IncomingMailServerHostName</key>
                        <string><?php echo $mail_server; ?></string>
                        <key>IncomingMailServerPortNumber</key>
                        <integer><?php echo $incoming_port; ?></integer>
                        <key>IncomingMailServerUseSSL</key>
                        <true/>
                        <key>IncomingMailServerUsername</key>
                        <string><?php echo $email_address; ?></string>
                        <key>OutgoingMailServerAuthentication</key>
                        <string>EmailAuthPassword</string>
                        <key>OutgoingMailServerHostName</key>
                        <string><?php echo $mail_server; ?></string>
                        <key>OutgoingMailServerPortNumber</key>
                        <integer>587</integer>
                        <key>OutgoingMailServerUseSSL</key>
                        <true/>
                        <key>OutgoingMailServerUsername</key>
                        <string><?php echo $email_address; ?></string>
                        <key>OutgoingPasswordSameAsIncomingPassword</key>
                        <true/>
                        <key>PayloadDescription</key>
                        <string><?php echo $email_address; ?> mail</string>
                        <key>PayloadDisplayName</key>
                        <string><?php echo $email_address; ?> mail</string>
                        <key>PayloadIdentifier</key>
                        <string><?php echo $reverse_hosting_company_domain; ?>.profile.mail.<?php echo $reverse_domain; ?></string>
                        <key>PayloadOrganization</key>
                        <string><?php echo $hosting_company_name; ?></string>
                        <key>PayloadType</key>
                        <string>com.apple.mail.managed</string>
                        <key>PayloadUUID</key>
                        <string><?php echo gen_uuid(); ?></string>
                        <key>PayloadVersion</key>
                        <integer>1</integer>
                        <key>PreventAppSheet</key>
                        <false/>
                        <key>PreventMove</key>
                        <false/>
                        <key>SMIMEEnabled</key>
                        <false/>
                </dict>
        </array>
        <key>PayloadDescription</key>
        <string><?php echo $email_address; ?> Email configuration for <?php echo $full_name; ?> generated by <?php echo curPageURL(); ?></string>
        <key>PayloadDisplayName</key>
        <string><?php echo $email_address; ?> Email configuration</string>
        <key>PayloadIdentifier</key>
        <string><?php echo $reverse_hosting_company_domain; ?>.profile.<?php echo $reverse_domain; ?></string>
        <key>PayloadOrganization</key>
        <string><?php echo $hosting_company_name; ?></string>
        <key>PayloadRemovalDisallowed</key>
        <false/>
        <key>PayloadType</key>
        <string>Configuration</string>
        <key>PayloadUUID</key>
        <string><?php echo gen_uuid(); ?></string>
        <key>PayloadVersion</key>
        <integer>1</integer>
</dict>
</plist>

<?php
//Functions
function gen_uuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                // 32 bits for "time_low"
                mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
                // 16 bits for "time_mid"
                mt_rand( 0, 0xffff ),
                // 16 bits for "time_hi_and_version",
                // four most significant bits holds version number 4
                mt_rand( 0, 0x0fff ) | 0x4000,
                // 16 bits, 8 bits for "clk_seq_hi_res",
                // 8 bits for "clk_seq_low",
                // two most significant bits holds zero and one for variant DCE1.1
                mt_rand( 0, 0x3fff ) | 0x8000,
                // 48 bits for "node"
                mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
}

function curPageURL() {
        $pageURL = 'http';
        if (@$_SERVER["HTTPS"] == "on") {
                $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
                $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
                $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        return $pageURL;
}
?>
