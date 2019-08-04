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
| 0x3 | 3   | No meaning           | OpcodeEnum::OPCODE_3   | false   |
| 0x4 | 4   | No meaning           | OpcodeEnum::OPCODE_4   | false   |
| 0x5 | 5   | No meaning           | OpcodeEnum::OPCODE_5   | false   |
| 0x6 | 6   | No meaning           | OpcodeEnum::OPCODE_6   | false   |
| 0x7 | 7   | No meaning           | OpcodeEnum::OPCODE_7   | false   |
| 0x8 | 8   | Close                | OpcodeEnum::CLOSE      | true    |
| 0x9 | 9   | Ping                 | OpcodeEnum::PING       | true    |
| 0xA | 10  | Pong                 | OpcodeEnum::PONG       | true    |
| 0xB | 11  | No meaning           | OpcodeEnum::OPCODE_11  | true    |
| 0xC | 12  | No meaning           | OpcodeEnum::OPCODE_12  | true    |
| 0xD | 13  | No meaning           | OpcodeEnum::OPCODE_13  | true    |
| 0xE | 14  | No meaning           | OpcodeEnum::OPCODE_14  | true    |
| 0xF | 15  | No meaning           | OpcodeEnum::OPCODE_15  | true    |

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

| Event class     | Reason                                                                                                                                        |
|-----------------|-----------------------------------------------------------------------------------------------------------------------------------------------|
| HandshakeEvent  | first data sent over a new connection will be treated as handshake request                                                                    |
| ConnectEvent    | new connection (handshake is already done when this event is triggered)                                                                       |
| DataEvent       | received data (triggered when data is received)                                                                                               |
| FrameEvent      | received frame (triggered when a frame is received)                                                                                           |
| MessageEvent    | received message (only works when the FrameEventSubscriber is an active listener, triggered when a frame is received with TEXT opcode)        |
| DisconnectEvent | disconnected (triggered when a connection is disconned)                                                                                       |

For all events to be dispatched there are 4 event dispatchers. The following is a list of the dispatchters and their in-/outgoning events:

| Dispatcher                    | Incomming events              | Outgoing events            | Purpose                                                                         |
|-------------------------------|-------------------------------|----------------------------|---------------------------------------------------------------------------------|
| HandshakeEventSubscriber      | HandshakeEvent                | ConnectEvent               | Handle handshake and dispatch a connect event when successfull                  |
| ConnectionEventSubscriber     | ConnectEvent, DisconnectEvent | DataEvent, DisconnectEvent | Handle connection and map connection events                                     |
| DataEventSubscriber           | DataEvent                     | FrameEvent                 | Parse incomming data to a frame and dispatch as frame event                     |
| FrameEventSubscriber          | FrameEvent                    | MessageEvent               | Handle incomming frame event and dispatch a Message event if the opcode is TEXT |

### Running the example
There is a simple chat application included in this project. To run this, use the following commands:
```bash
php example/socket.php
php -S 127.0.0.1:8080 -t ./example/web
```
__You must run both commands separate from each other__

### Known issues
1. Fragmentations is not supported yet
2. Encode function of the encoder does not yet support masking
3. Strings longer than 65536 cannot be received due to buffering
