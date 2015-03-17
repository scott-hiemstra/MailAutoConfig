<?php

$default_server = 'mail.something.com';
$setup_logo = 'http://some.image.com/image.jpg';
//get raw POST data so we can extract the email address
$data = file_get_contents("php://input");
// Get the schema from the request.
preg_match("/\<AcceptableResponseSchema\>(.*?)\<\/AcceptableResponseSchema\>/", $data, $schema);
preg_match("/\<EMailAddress\>(.*?)\<\/EMailAddress\>/", $data, $matches);
if (isset($matches[1])) {
        $email_address = $matches[1];
        $parts = explode('@', $email_address);
        $user = $parts[0];
        $domain = $parts[1];
        $mail_server = "mail." . $domain;
} else {
        $email_address = '';
        $mail_server = $default_server;
}


//set Content-Type
header("Content-Type: application/xml");
?>
<?php echo '<?xml version="1.0" encoding="utf-8" ?>'; ?>
<Autodiscover xmlns="http://schemas.microsoft.com/exchange/autodiscover/responseschema/2006">
        <Response xmlns="http://schemas.microsoft.com/exchange/autodiscover/outlook/responseschema/2006a">
                <Account>
                        <AccountType>email</AccountType>
                        <Action>settings</Action>
                        <Image><?php echo $setup_logo; ?></Image>
                        <Protocol>
                                <Type>POP3</Type>
                                <Server><?php echo $mail_server; ?></Server>
                                <Port>995</Port>
                                <LoginName><?php echo $email_address; ?></LoginName>
                                <DomainRequired>on</DomainRequired>
                                <SPA>off</SPA>
                                <SSL>on</SSL>
                                <AuthRequired>on</AuthRequired>
                                <DomainRequired>on</DomainRequired>
                        </Protocol>
                        <Protocol>
                                <Type>IMAP</Type>
                                <Server><?php echo $mail_server; ?></Server>
                                <Port>993</Port>
                                <DomainRequired>on</DomainRequired>
                                <LoginName><?php echo $email_address; ?></LoginName>
                                <SPA>off</SPA>
                                <SSL>on</SSL>
                                <AuthRequired>on</AuthRequired>
                        </Protocol>
                        <Protocol>
                                <Type>SMTP</Type>
                                <Server><?php echo $mail_server; ?></Server>
                                <Port>587</Port>
                                <DomainRequired>on</DomainRequired>
                                <LoginName><?php echo $email_address; ?></LoginName>
                                <SPA>off</SPA>
                                <SSL>on</SSL>
                                <Encryption>TLS</Encryption>
                                <AuthRequired>on</AuthRequired>
                                <UsePOPAuth>off</UsePOPAuth>
                                <SMTPLast>off</SMTPLast>
                        </Protocol>
                </Account>
        </Response>
</Autodiscover>
