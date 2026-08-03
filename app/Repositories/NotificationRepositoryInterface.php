<?php

namespace App\Repositories;

interface NotificationRepositoryInterface
{
public function create(array $data);

 public function getByClient(int $clientId);
}
