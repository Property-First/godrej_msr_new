
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data safely
    $name  = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $ip = getUserIP();

    // -----------------------------------
    // 1. SEND EMAIL
    // -----------------------------------

    $to = "lmt@property-first.com, che@azurechennai.officialswebsite.info";


    $subject = "New Enquiry from - Godrej MSR  ad";

    $message = "
Adarsh Palm Acres :

Name: $name
Phone: $phone
Email: $email
IP_Address : $ip
";

    $headers = "From: noreply@yourdomain.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    $mailSent = mail($to, $subject, $message, $headers);


    // -----------------------------------
    // 2. SEND DATA TO ZOHO FLOW
    // -----------------------------------

  
    $zohoWebhookUrl = "https://flow.zoho.in/60079714926/flow/webhook/incoming?zapikey=1001.dd5be17d8d7a2180bb47ca012193f6ac.9c2c7b78751551d306d7e1ddb0846168&isdebug=false";

    // Data matching your Zoho Flow webhook fields
    $data = [
        "Project"   => "Godrej MSR : HS",
        "Email"     => $email,
        "LeadSource" => "ads",
        "Phone"     => $phone,
        "Name"      => $name
    ];
    
   

    // Initialize cURL
    $ch = curl_init($zohoWebhookUrl);
//      echo "<pre>";
// print_r($ch);
// echo "</pre>";

// exit;

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Accept: application/json"
    ]);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $zohoResponse = curl_exec($ch);

    $zohoHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    $zohoError = curl_error($ch);

    curl_close($ch);
      
    
    // DEBUG
// echo "<pre>";

// echo "===== DATA SENT =====\n";
// print_r($data);

// echo "\n===== HTTP CODE =====\n";
// echo $zohoHttpCode;

// echo "\n===== CURL ERROR =====\n";
// echo $zohoError ?: "No error";

// echo "\n===== ZOHO RESPONSE =====\n";
// print_r($zohoResponse);

// echo "</pre>";

// exit;


    // -----------------------------------
    // 3. REDIRECT USER
    // -----------------------------------

    if ($mailSent) {

        echo "<script>
                window.location.href='thank_you.html';
              </script>";

    } else {

        echo "Something went wrong. Please try again.";
    }

} else {

    echo "Invalid Request";
}


function getUserIP()
{
    // Cloudflare IP
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        return $_SERVER["HTTP_CF_CONNECTING_IP"];
    }


    // Client IP
    if (
        isset($_SERVER['HTTP_CLIENT_IP']) &&
        filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)
    ) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }


    // Forwarded IP
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {

        $forwarded_ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

        foreach ($forwarded_ips as $forwarded_ip) {

            $forwarded_ip = trim($forwarded_ip);

            if (filter_var($forwarded_ip, FILTER_VALIDATE_IP)) {
                return $forwarded_ip;
            }
        }
    }


    // Remote IP
    return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
}

?>
