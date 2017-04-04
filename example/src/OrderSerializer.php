<?php

namespace App;

class OrderSerializer
{
    public function serialize(Order $order): string
    {
        $data = [
            'id' => $order->id(),
            'email' => $order->email(),
            'paid' => $order->paid() ? 1 : 0,
            'items' => [],
        ];

        foreach ($order->orderLines() as $orderLine) {
            $data['items'][] = [
                'id' => $orderLine->id(),
                'description' => $orderLine->description(),
                'price' => $orderLine->totalPrice(),
            ];
        }

        return json_encode($data);
    }
}
