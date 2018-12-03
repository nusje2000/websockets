let socket = null;
let timeout = null;
const enterKeycode = 13;

window.onkeypress = event => {
    enterKeycode === event.key && sendMessageFromInput('socket-message');
};

function startConnection() {
    if (null !== socket) {
        socket.close();
    }

    if (null !== timeout) {
        clearTimeout(timeout);
        timeout = null;
    }

    document.getElementById('socket-errors').innerHTML = '';
    document.getElementById('messages').innerHTML = '';
    document.getElementById('socket-status').className = 'connecting';

    socket = new WebSocket('ws://127.0.0.1:8001');

    socket.onopen = () => {
        document.getElementById('socket-status').className = 'open';
        document.getElementById('dashboard').style.display = 'block';
    };

    socket.onmessage = e => handleData(e.data);

    socket.onclose = () => {
        document.getElementById('socket-status').className = 'closed';
        document.getElementById('dashboard').style.display = 'none';
        document.getElementById('socket-errors').innerHTML = 'closed';

        if (null === timeout) {
            timeout = setTimeout(startConnection, 1000);
        }
    };

    socket.onerror = () => {
        document.getElementById('socket-status').className = 'closed';
        document.getElementById('dashboard').style.display = 'none';

        if (null === timeout) {
            timeout = setTimeout(startConnection, 1000);
        }
    };

}

function addToInput(id, text) {
    document.getElementById(id).value += text;
}


function sendMessageFromInput(id) {
    if (null !== socket && document.getElementById('socket-message').value) {
        let element = document.getElementById(id);
        sendMessage(element.value);
        document.getElementById(id).value = ''
    }
}

function sendMessage(message) {
    socket.send(message);
}

function handleData(message) {
    let element = document.getElementById('messages');
    element.innerHTML += `<li class="message-item">${message}</li>`;
    element.scrollTo(0, element.scrollHeight);
}

startConnection();