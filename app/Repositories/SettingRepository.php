<?php
namespace App\Repositories;

use App\Models\Setting;
use App\Interfaces\SettingRepositoryInterface;
class SettingRepository extends BaseRepository implements SettingRepositoryInterface
{
    public function __construct(Setting $setting)
    {
        parent::__construct($setting);
    }
    
}