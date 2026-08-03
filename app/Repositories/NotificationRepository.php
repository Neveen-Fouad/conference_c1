<?php

namespace App\Repositories;

use App\Models\notifications;

class NotificationRepository implements NotificationRepositoryInterface
{
public function create(array $data){
    return Notifications::create($data);
}
public function getByClient(int $clientId){
    return Notifications::where('client_id',$clientId)->latest()->get();
}

}
