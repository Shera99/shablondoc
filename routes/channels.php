<?php

use App\Broadcasting\NewOrderChannel;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('new-orders', NewOrderChannel::class);
