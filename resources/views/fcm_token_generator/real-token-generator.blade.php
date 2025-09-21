<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCM Token Generator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .token-display {
            margin-top: 20px;
            padding: 15px;
            background-color: #f0f0f0;
            border-radius: 5px;
            display: none;
        }

        .token {
            font-family: monospace;
            word-break: break-all;
            background-color: #ffffff;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }

        button {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            display: inline-block;
            font-size: 16px;
            margin: 10px 0;
            cursor: pointer;
            border-radius: 4px;
        }

        .status {
            margin-top: 10px;
            padding: 10px;
            border-radius: 4px;
        }

        .error {
            background-color: #ffebee;
            color: #d32f2f;
        }

        .success {
            background-color: #e8f5e9;
            color: #388e3c;
        }
    </style>
</head>

<body>
    <h1>FCM Token Generator</h1>
    <p>This page will help you generate a real FCM token for testing.</p>

    <button id="requestToken">Request Permission & Generate Token</button>

    <div id="statusMessage" class="status" style="display: none;"></div>

    <div id="tokenDisplay" class="token-display">
        <h3>Your FCM Token:</h3>
        <div id="tokenValue" class="token"></div>
        <button id="copyToken">Copy Token</button>
        <button id="testNotification">Send Test Notification</button>
    </div>

    <!-- Firebase SDK -->
    <script type="module">
        import {
            initializeApp
        } from "https://www.gstatic.com/firebasejs/11.5.0/firebase-app.js";
        import {
            getMessaging,
            getToken,
            onMessage
        } from "https://www.gstatic.com/firebasejs/11.5.0/firebase-messaging.js";

        // Your Firebase configuration from Laravel
        const firebaseConfig = @json($firebaseConfig);

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);

        // DOM elements
        const requestTokenBtn = document.getElementById('requestToken');
        const tokenDisplay = document.getElementById('tokenDisplay');
        const tokenValue = document.getElementById('tokenValue');
        const copyTokenBtn = document.getElementById('copyToken');
        const testNotificationBtn = document.getElementById('testNotification');
        const statusMessage = document.getElementById('statusMessage');

        // Show status message
        function showStatus(message, isError = false) {
            statusMessage.textContent = message;
            statusMessage.className = 'status ' + (isError ? 'error' : 'success');
            statusMessage.style.display = 'block';
        }

        // Request permission and get token
        requestTokenBtn.addEventListener('click', async function() {
            try {
                // Request permission
                const permission = await Notification.requestPermission();
                console.log('permission', permission);

                if (permission !== 'granted') {
                    showStatus('Notification permission denied. Please allow notifications and try again.',
                        true);
                    return;
                }

                showStatus('Permission granted. Generating token...');

                // Initialize messaging
                const messaging = getMessaging(app);

                // Get token
                // You need to create a VAPID key in Firebase console
                // Project settings > Cloud Messaging > Web configuration > Generate key pair
                // When you need the VAPID key
                const vapidKey = "{{ $vapidKey }}";
                try {
                    const currentToken = await getToken(messaging, {
                        vapidKey: vapidKey
                    });

                    if (currentToken) {
                        // Display the token
                        tokenValue.textContent = currentToken;
                        tokenDisplay.style.display = 'block';
                        showStatus('FCM token generated successfully!');

                        // Set up onMessage handler for foreground messages
                        onMessage(messaging, (payload) => {
                            console.log('Message received in foreground:', payload);
                            showStatus('Notification received in foreground!');
                        });
                    } else {
                        showStatus('No registration token available. Request permission first.', true);
                    }
                } catch (tokenError) {
                    console.error('Error getting token:', tokenError);
                    showStatus(`Error getting token: ${tokenError.message}`, true);
                }
            } catch (error) {
                console.error('Error:', error);
                showStatus(`Error: ${error.message}`, true);
            }
        });

        // Copy token button
        copyTokenBtn.addEventListener('click', function() {
            const token = tokenValue.textContent;
            navigator.clipboard.writeText(token).then(function() {
                copyTokenBtn.textContent = 'Copied!';
                setTimeout(() => {
                    copyTokenBtn.textContent = 'Copy Token';
                }, 2000);
            }).catch(function(err) {
                console.error('Could not copy text:', err);
                showStatus('Failed to copy token', true);
            });
        });

        // Test notification button
        testNotificationBtn.addEventListener('click', function() {
            const token = tokenValue.textContent;

            if (!token) {
                showStatus('No token available. Generate a token first.', true);
                return;
            }

            showStatus('Sending test notification...');

            // Send to your Laravel test endpoint
            fetch('/test-notification', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        token: token
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showStatus('Notification sent successfully!');
                    } else {
                        showStatus(`Error: ${data.message}`, true);
                    }
                })
                .catch(error => {
                    showStatus(`Error: ${error.message}`, true);
                });
        });
    </script>
</body>

</html>
