<?php

use App\Order;
use App\OrderLine;
use App\OrderSerializer;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class OrderSerializerTest extends TestCase
{
    use MatchesSnapshots;

    /** @test */
    public function it_can_serialize_an_order()
    {
        $orderSerializer = new OrderSerializer;

        $order = new Order(1, 'sebastian@spatie.be', true, [
            new OrderLine(1, 'Sublime Text License', 70, 3),
            new OrderLine(2, 'PHPStorm License', 199, 2),
        ]);

        $this->assertMatchesJsonSnapshot($orderSerializer->serialize($order));
    }
}
