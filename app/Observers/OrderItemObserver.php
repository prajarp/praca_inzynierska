<?php

namespace App\Observers;

use App\Models\OrderItem;

class OrderItemObserver
{
    public function created(OrderItem $orderItem): void
    {
        //
    }

    public function updated(OrderItem $orderItem): void
    {
        //
    }

    public function saved(OrderItem $orderItem)
    {
        if ($orderItem->item_type === 'window') {
            $orderItem->order->updateWindowsWeight();
        }
    }

    public function deleted(OrderItem $orderItem)
    {
        if ($orderItem->item_type === 'window') {
            $orderItem->order->updateWindowsWeight();
        }
    }

    public function restored(OrderItem $orderItem): void
    {
        //
    }

    public function forceDeleted(OrderItem $orderItem): void
    {
        //
    }
}
