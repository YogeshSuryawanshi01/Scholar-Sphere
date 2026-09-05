<?php
/**
 * AI Chat API Handler
 * Handles messages and Grok AI integration
 */
require_once __DIR__ . '/../includes/session_bootstrap.php';
bootSession('public');
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$user_message = sanitize($_POST['message'] ?? '', $conn);

if (empty($user_message)) {
    echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
    exit;
}

// Store user message
$stmt = $conn->prepare("INSERT INTO messages (user_id, message_type, message_text) VALUES (?, 'user', ?)");
$stmt->bind_param("is", $_SESSION['user_id'], $user_message);
$stmt->execute();
$stmt->close();

// Generate AI response using Gemini API
$ai_response = generateAIResponse($user_message);

// Store AI response
$response_stmt = $conn->prepare("INSERT INTO messages (user_id, message_type, message_text) VALUES (?, 'ai', ?)");
$response_stmt->bind_param("is", $_SESSION['user_id'], $ai_response);
$response_stmt->execute();
$response_stmt->close();

echo json_encode([
    'success' => true,
    'message' => 'Message sent successfully',
    'ai_response' => $ai_response
]);

/**
 * Generate AI response using Grok API
 * Falls back to pre-defined responses if API key not configured
 */
function generateAIResponse($message) {
    // security: avoid hardcoding key; env var fallback to known key for local testing
    $api_key = getenv('GEMINI_API_KEY') ?: '   Your API KEY HERE............. ';
    $preferredModel = 'models/gemini-2.5-pro';

    if (empty($api_key) || $api_key === 'YOUR_GEMINI_API_KEY') {
        error_log('Gemini API key is missing. Set GEMINI_API_KEY environment variable.');
        return 'Gemini API key is not configured.';
    }

    $models = array_unique(array_filter(array_merge([$preferredModel], listGeminiModels($api_key))));
    $lastError = 'Gemini: no model found';

    foreach ($models as $model) {
        if (strpos($model, 'models/') !== 0) {
            continue;
        }

        $result = callGeminiAPI($message, $api_key, $model);
        if (strpos($result, 'Gemini API error') === false && strpos($result, 'Gemini model not found') === false && strpos($result, 'cURL error') === false && strpos($result, 'parse failed') === false) {
            return $result;
        }

        $lastError = $result;
    }

    return $lastError;
}

/**
 * Call Gemini API
 */
function callGeminiAPI($message, $api_key, $model) {
    if (!function_exists('curl_init')) {
        error_log("cURL not available");
        return generateFallbackResponse($message);
    }

    // Normalize model for endpoint path and use Gemini generateContent endpoint
    $normalizedModel = preg_replace('#^models/#', '', $model);
    $url = "https://generativelanguage.googleapis.com/v1beta/models/${normalizedModel}:generateContent?key=${api_key}";

    $maxOutputTokens = (int) (getenv('GEMINI_MAX_OUTPUT_TOKENS') ?: 2048);
    if ($maxOutputTokens <= 0) {
        $maxOutputTokens = 2048;
    }

    $data = [
        'contents' => [[
            'parts' => [[
                'text' => $message
            ]]
        ]],
        'generationConfig' => [
            'temperature' => 0.7,
            'maxOutputTokens' => $maxOutputTokens
        ],
    ];


    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // local dev only

    $response = curl_exec($ch);
    if ($response === false) {
        $curl_err = curl_error($ch);
        curl_close($ch);
        error_log("Gemini cURL error: $curl_err");
        return "Gemini cURL error: $curl_err";
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    error_log("Gemini API Response: HTTP $http_code - " . substr($response, 0, 300));

    $response_data = json_decode($response, true);
    if ($http_code !== 200 || json_last_error() !== JSON_ERROR_NONE) {
        $error_message = json_last_error() === JSON_ERROR_NONE ? ($response_data['error']['message'] ?? 'Unknown API error') : 'Invalid JSON response';
        error_log("Gemini API HTTP error: $http_code - $error_message");

        if ($http_code === 404) {
            $models = listGeminiModels($api_key);
            return "Gemini model not found (404). Available models: " . implode(', ', $models);
        }

        return "Gemini API error: HTTP $http_code - $error_message";
    }

    // Primary parse path for generateContent responses:
    // collect all text parts across all candidates so UI gets the complete model output.
    if (!empty($response_data['candidates']) && is_array($response_data['candidates'])) {
        $allCandidateTexts = [];

        foreach ($response_data['candidates'] as $candidate) {
            if (isset($candidate['content']['parts']) && is_array($candidate['content']['parts'])) {
                $parts = [];
                foreach ($candidate['content']['parts'] as $part) {
                    if (isset($part['text']) && is_string($part['text'])) {
                        $parts[] = trim($part['text']);
                    }
                }

                $joined = trim(implode("\n", array_filter($parts)));
                if ($joined !== '') {
                    $allCandidateTexts[] = $joined;
                }
            }
        }

        if (!empty($allCandidateTexts)) {
            return implode("\n\n", $allCandidateTexts);
        }
    }

    // Backward-compatible parse for older response formats
    if (!empty($response_data['candidates'][0]['output']) && is_string($response_data['candidates'][0]['output'])) {
        return trim($response_data['candidates'][0]['output']);
    }

    // If parsing fails, return API content for inspection (still considered API response)
    return 'Gemini API returned but parse failed. Raw response: ' . substr($response, 0, 1000);
}

function listGeminiModels($api_key) {
    if (!function_exists('curl_init')) {
        return ['curl not available'];
    }

    $url = "https://generativelanguage.googleapis.com/v1/models?key={$api_key}";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // local dev only

    $resp = curl_exec($ch);
    if ($resp === false) {
        $err = curl_error($ch);
        curl_close($ch);
        return ["cURL error: $err"];
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        return ["ListModels HTTP $http_code: $resp"];
    }

    $json = json_decode($resp, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['invalid json from list models'];
    }

    $models = [];
    foreach ($json['models'] ?? [] as $model) {
        if (isset($model['name'])) {
            $models[] = $model['name'];
        }
    }

    return $models ?: ['no models returned'];
}

/**
 * Generate intelligent fallback responses
 */
function generateFallbackResponse($message) {
    $message_lower = strtolower($message);
    
    // Greetings
    if (preg_match('/(hello|hi|hey|greetings)/i', $message)) {
        return "Hello! 👋 Welcome to Scholar Sphere's AI Assistant. I'm here to help you with any questions about your studies. What would you like to know?";
    }
    
    // Math questions
    if (preg_match('/(math|algebra|geometry|trigonometry|calculus)/i', $message)) {
        return "I'd be happy to help with math! Please share the specific problem or concept you need help with, and I'll provide a clear explanation with step-by-step solutions.";
    }
    
    // Science questions
    if (preg_match('/(science|physics|chemistry|biology)/i', $message)) {
        return "Great question about science! Could you provide more details about which topic or concept you'd like me to explain? Physics, chemistry, biology, or another branch?";
    }
    
    // Study tips
    if (preg_match('/(study|learning|exam|test|prepare)/i', $message)) {
        return "Excellent question! Here are some study tips:\n\n1. **Break it down**: Divide complex topics into smaller, manageable chunks\n2. **Active recall**: Test yourself regularly instead of just re-reading\n3. **Spaced repetition**: Review material at increasing intervals\n4. **Take notes**: Write in your own words to enhance understanding\n5. **Practice problems**: Application solidifies concepts\n\nWould you like specific help with any subject?";
    }
    
    // General questions
    if (preg_match('/(what|how|why|when)/i', $message)) {
        return "That's a great question! To provide the most helpful answer, could you give me more context? For example:\n\n- What subject or topic is this related to?\n- What specific aspect interests you?\n- Have you already attempted this problem?\n\nThe more details you share, the better I can assist!";
    }
    
    // Fallback response
    return "Thank you for your question! I'm here to help with educational content. Please feel free to ask about:\n\n✓ Subject-specific explanations\n✓ Study strategies and tips\n✓ Problem-solving help\n✓ Conceptual understanding\n\nWhat would you like to explore today?";
}

?>
