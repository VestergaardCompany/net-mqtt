<?php

namespace BinSoul\Net\Mqtt\Packet;

use BinSoul\Net\Mqtt\Exception\MalformedPacketException;
use BinSoul\Net\Mqtt\PacketStream;
use BinSoul\Net\Mqtt\Packet;

/**
 * Represents the SUBSCRIBE packet.
 */
class SubscribeRequestPacket extends BasePacket
{
    use IdentifiablePacket;

    /** @var string */
    private $topic;
    /** @var int */
    private $qosLevel;

    protected $packetType = Packet::TYPE_SUBSCRIBE;
    protected $packetFlags = 2;

    public function read(PacketStream $stream)
    {
        parent::read($stream);
        $this->assertPacketFlags(2);
        $this->assertRemainingPacketLength();

        $this->identifier = $stream->readWord();
        $this->topic = $stream->readString();
        $this->qosLevel = $stream->readByte();

        $this->assertValidString($this->topic);
        if ($this->qosLevel > 2) {
            throw new MalformedPacketException(
                sprintf(
                    'Malformed quality of service level: type=%02x, flags=%02x, length=%02x',
                    $this->packetType,
                    $this->packetFlags,
                    $this->remainingPacketLength
                )
            );
        }
    }

    public function write(PacketStream $stream)
    {
        $data = new PacketStream();

        $data->writeWord($this->generateIdentifier());
        $data->writeString($this->topic);
        $data->writeByte($this->qosLevel);

        $this->remainingPacketLength = $data->length();

        parent::write($stream);
        $stream->write($data->getData());
    }

    /**
     * Returns the Topic.
     *
     * @return string
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * Sets the Topic.
     *
     * @param string $topic
     */
    public function setTopic($topic)
    {
        $this->topic = $topic;
    }

    /**
     * Returns the QosLevel.
     *
     * @return int
     */
    public function getQosLevel()
    {
        return $this->qosLevel;
    }

    /**
     * Sets the QosLevel.
     *
     * @param int $qosLevel
     */
    public function setQosLevel($qosLevel)
    {
        $this->qosLevel = $qosLevel;
    }
}