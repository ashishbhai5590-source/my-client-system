<?php
header('Content-Type: application/json');

// --- ⚙️ अपनी गुप्त जानकारी यहाँ डालें ⚙️ ---
// 1. BotFather से मिला हुआ अपना API टोकन यहाँ डालें।
// 2. अपनी चैट आईडी यहाँ डालें (जिस पर आप नोटिफिकेशन पाना चाहते हैं)।

$bot_token = '7978691086:AAGB8030R6DBhADBnVcSN-2zd75aVl4OEwA'; // 👈 अपना बॉट टोकन यहाँ डालें
$chat_id = '7197184333';          // 👈 अपनी चैट आईडी यहाँ डालें
// ---------------------------------------------

$response = ['status' => 'error', 'message' => 'Invalid Request'];

// यह सुनिश्चित करता है कि डेटा POST मेथड से आ रहा है।
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // HTML पेज से भेजे गए JSON डेटा को पढ़ता है।
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    if ($data) {
        // सभी जानकारी को सुरक्षित तरीके से निकालता है।
        $platform = htmlspecialchars($data['platform'] ?? 'N/A');
        $service_value = htmlspecialchars($data['service'] ?? 'N/A');
        $username = htmlspecialchars($data['username'] ?? 'N/A');
        $quantity = htmlspecialchars($data['quantity'] ?? 'N/A');
        $customer_name = htmlspecialchars($data['customer_name'] ?? 'N/A');
        $customer_contact = htmlspecialchars($data['customer_contact'] ?? 'N/A');
        $utr = htmlspecialchars($data['utr'] ?? 'N/A');
        $price = htmlspecialchars($data['price'] ?? '₹0');

        // टेलीग्राम पर भेजने के लिए एक प्रोफेशनल मैसेज बनाता है।
        $message = "🔔 *Miss SMM पैनल - नया ऑर्डर!* 🔔\n\n";
        $message .= "========================\n\n";
        $message .= "👤 *ग्राहक की जानकारी:*\n";
        $message .= "   - *नाम:* " . $customer_name . "\n";
        $message .= "   - *व्हाट्सएप:* " . $customer_contact . "\n\n";
        
        $message .= "📦 *ऑर्डर का विवरण:*\n";
        $message .= "   - *प्लेटफार्म:* " . $platform . "\n";
        $message .= "   - *सर्विस:* " . str_replace('_', ' ', ucwords($service_value)) . "\n";
        $message .= "   - *यूजरनेम/लिंक:* `" . $username . "`\n";
        $message .= "   - *मात्रा:* " . $quantity . "\n\n";

        $message .= "💳 *पेमेंट की जानकारी:*\n";
        $message .= "   - *कुल राशि:* *" . $price . "*\n";
        $message .= "   - *UTR/Txn ID:* *" . $utr . "*\n\n";
        
        $message .= "========================\n";
        $message .= "⏰ " . date('d-m-Y h:i A');

        // टेलीग्राम API को मैसेज भेजने के लिए URL तैयार करता है।
        $url = "https://api.telegram.org/bot" . $bot_token . "/sendMessage";
        
        $post_fields = [
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => 'Markdown' // यह मैसेज में bold (*), italic (_) आदि फॉर्मेटिंग को सपोर्ट करता है।
        ];
        
        // cURL का उपयोग करके मैसेज को टेलीग्राम पर भेजता है।
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:multipart/form-data"));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        $api_response = curl_exec($ch);
        curl_close($ch);
        
        $response = ['status' => 'success', 'message' => 'Notification sent successfully.'];
    } else {
        $response['message'] = 'No data received.';
    }
}

// अंत में एक JSON रिस्पॉन्स भेजता है, जिसे JavaScript पढ़ सकता है।
echo json_encode($response);
?>