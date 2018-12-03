### Websocket server
Almost fully compatible with https://tools.ietf.org/html/rfc6455

Simple version can be found here:
https://developer.mozilla.org/en-US/docs/Web/API/WebSockets_API/Writing_WebSocket_servers

#### Data framing
The following data frame was used to parse messages from and
to the clients.
```
 0                   1                   2                   3
 0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1
+-+-+-+-+-------+-+-------------+-------------------------------+
|F|R|R|R| opcode|M| Payload len |    Extended payload length    |
|I|S|S|S|  (4)  |A|     (7)     |             (16/64)           |
|N|V|V|V|       |S|             |   (if payload len==126/127)   |
| |1|2|3|       |K|             |                               |
+-+-+-+-+-------+-+-------------+ - - - - - - - - - - - - - - - +
|   Extended payload length continued, if payload len == 127    |
+ - - - - - - - - - - - - - - - +-------------------------------+
|                               |Masking-key, if MASK set to 1  |
+-------------------------------+-------------------------------+
| Masking-key (continued)       |          Payload Data         |
+-------------------------------- - - - - - - - - - - - - - - - +
:                     Payload Data continued ...                :
+ - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - +
|                     Payload Data continued ...                |
+---------------------------------------------------------------+
```

##### Opcodes
| hex | int | text                 | const                  | control |
|-----|-----|----------------------|------------------------|---------|
| 0x0 | 0   | Continuation         | OpcodeEnum::CONTINUE   | true    |
| 0x1 | 1   | Text                 | OpcodeEnum::TEXT       | false   |
| 0x2 | 2   | Binary               | OpcodeEnum::BIN        | false   |
| 0x3 | 3   | No meaning           | OpcodeEnum::CONTROL_1  | false   |
| 0x4 | 4   | No meaning           | OpcodeEnum::CONTROL_2  | false   |
| 0x5 | 5   | No meaning           | OpcodeEnum::CONTROL_3  | false   |
| 0x6 | 6   | No meaning           | OpcodeEnum::CONTROL_4  | false   |
| 0x7 | 7   | No meaning           | OpcodeEnum::CONTROL_5  | false   |
| 0x8 | 8   | Close                | OpcodeEnum::CLOSE      | true    |
| 0x9 | 9   | Ping                 | OpcodeEnum::PING       | true    |
| 0xA | 10  | Pong                 | OpcodeEnum::PONG       | true    |
| 0xB | 11  | No meaning           | OpcodeEnum::CONTROL_6  | true    |
| 0xC | 12  | No meaning           | OpcodeEnum::CONTROL_7  | true    |
| 0xD | 13  | No meaning           | OpcodeEnum::CONTROL_8  | true    |
| 0xE | 14  | No meaning           | OpcodeEnum::CONTROL_9  | true    |
| 0xF | 15  | No meaning           | OpcodeEnum::CONTROL_10 | true    |

##### Mapping
All incomming frames are being mapped to the Frame class, this class
is basically an oop version of the dataframe displayed above. The
following mapping is used:

 - `FIN` => Frame::isFinal(): bool
 - `RSV1` => none
 - `RSV2` => none
 - `RSV3` => none
 - `OPCODE` => Frame::getOpcode(): int
 - `MASK` => Frame::isMasked(): bool
 - `PAYLOAD_LENGTH` => Frame::getPayloadLength(): int
 - `MASKING_KEY` => Frame::getMaskingKey(): string
 - `PAYLOAD_DATA` => Frame::getPayload(): string

#### Additional functionallity
There are a few functions added to the Frame for ease of use:
 - __Frame::isClosing(): bool__ => is true when opcode = `0x8`
 - __Frame::isControl(): bool__ => is true when opcode is for contol
 - __Frame::isNonControl(): bool__ => is true when opcode is not
 for contol

#### Events
The websocket makes use of the symfony event dispatcher. Because of this,
each incomming dataframe wil dispatch an event. This event can be used
to react on incomming frames. The following is a list of events that
can be listened to:

| Event                  | Event class     | Reason                                                                                                                                        |
|------------------------|-----------------|-----------------------------------------------------------------------------------------------------------------------------------------------|
| socket.connect         | ConnectionEvent | new connection (handshake is already done when this event is triggered)                                                                       |
| socket.disconnect      | ConnectionEvent | disconnected (triggered when a connection is disconned)                                                                                       |
| socket.frame.receive   | FrameEvent      | received frame (triggered when a frame is received)                                                                                           |
| socket.message.receive | MessageEvent    | received message (only works when the FrameEventSubscriber is an active listener, triggered when a frame is received with TEXT opcode)        |

__All event names are available as a constant on the WebSocketEventInterface__

### Running the example
There is a simple chat application included in this project. To run this, use the following commands:
```bash
php bin/example.php
php -S 127.0.0.1:8080 -t ./web
```
__You must run both commands separate from each other__

### Known issues
1. Fragmentations is not supported yet
2. Encode function of the encoder does not yet support masking
3. Strings longer than 65536 cannot be received due to buffering