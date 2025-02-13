<?php
namespace Ratchet\Server;
use Psr\Http\Message\RequestInterface;
use Ratchet\ConnectionInterface;
use React\Socket\ConnectionInterface as ReactConn;

/**
 * Monkey-patched for php 8.2 fixes vendor class from react/socket.
 *
 * @inheritDoc
 */
class IoConnection implements ConnectionInterface {
    /*
     * Patches for run with php 8.2
     */
    public int $resourceId;
    public string $remoteAddress;
    public bool $httpHeadersReceived;
    public string $httpBuffer;
    public RequestInterface $httpRequest;
    public \stdClass $WebSocket;
    /*
     * End of patched fields. Below vendor class as is.
     */

    /**
     * @var \React\Socket\ConnectionInterface
     */
    protected $conn;

    /**
     * @param \React\Socket\ConnectionInterface $conn
     */
    public function __construct(ReactConn $conn) {
        $this->conn = $conn;
    }

    /**
     * {@inheritdoc}
     */
    public function send($data) {
        $this->conn->write($data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function close() {
        $this->conn->end();
    }
}
