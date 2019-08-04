<html lang="en">
<head>
    <title>Socket example!</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>

<div class="controls">
    <button onclick="startConnection()">connect</button>
    <div id="socket-status" class="none"></div>
    <div id="socket-errors"></div>
</div>

<div id="dashboard" style="display: none;">
    <input placeholder="message" type="text" id="socket-message">
    <button onclick="addToInput('socket-message', '😀')">😀</button>
    <button onclick="addToInput('socket-message', '😁')">😁</button>
    <button onclick="addToInput('socket-message', '😂')">😂</button>
    <button onclick="addToInput('socket-message', '😉')">😉</button>
    <button onclick="addToInput('socket-message', '😛')">😛</button>
    <button onclick="addToInput('socket-message', '😎')">😎</button>
    <br>
    <button onclick="sendMessageFromInput('socket-message')">
        Send
    </button>
    <div id="messages"></div>
</div>

<script src="script.js"></script>
</body>
</html>
