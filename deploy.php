<?php
/**
 * GitHub Webhook Receiver
 * Handles automatic deployment when code is pushed to GitHub
 *
 * Setup in GitHub:
 * Settings → Webhooks → Add webhook
 * Payload URL: https://livingkost.com/deploy.php
 * Content type: application/json
 * Events: Push events only
 */

// Security: Verify webhook secret
define('WEBHOOK_SECRET', 'your-secret-key-here'); // Change this to a random string

// Log file untuk debugging
$log_file = __DIR__ . '/deploy.log';

function log_message($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
}

// Verify GitHub signature
function verify_github_signature($payload, $signature) {
    $hash = 'sha256=' . hash_hmac('sha256', $payload, WEBHOOK_SECRET);
    return hash_equals($hash, $signature);
}

// Get webhook payload
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

log_message('Webhook received');
log_message('Signature: ' . $signature);

// Verify signature if secret is set
if (WEBHOOK_SECRET !== 'your-secret-key-here') {
    if (!verify_github_signature($payload, $signature)) {
        log_message('❌ Invalid signature');
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Invalid signature']);
        exit;
    }
    log_message('✅ Signature verified');
}

// Parse JSON payload
$data = json_decode($payload, true);

// Check if this is a push event to main branch
if ($data['ref'] === 'refs/heads/main') {
    log_message('✅ Push to main branch detected');

    // Change to repository directory
    $repo_dir = __DIR__;
    chdir($repo_dir);

    // Execute git pull
    $output = shell_exec('cd ' . escapeshellarg($repo_dir) . ' && git pull 2>&1');
    log_message('Git pull output: ' . $output);

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Deployment completed',
        'output' => $output
    ]);
    log_message('✅ Deployment completed successfully');
} else {
    log_message('⏭️ Ignoring push to: ' . $data['ref']);
    http_response_code(200);
    echo json_encode([
        'status' => 'ignored',
        'message' => 'Only main branch is deployed',
        'branch' => $data['ref']
    ]);
}
?>
