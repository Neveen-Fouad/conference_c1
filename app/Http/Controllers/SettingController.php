<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSettingsRequest;
use App\Http\Requests\UpdateSettingsRequest;
use App\Interfaces\SettingRepositoryInterface;

class SettingController extends Controller
{
    protected $SettingRepository;

    public function __construct(SettingRepositoryInterface $settingRepository)
    {
        $this->SettingRepository = $settingRepository;
    }

    public function storeSettings(StoreSettingsRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        return response()->json(
            $this->SettingRepository->create($validated)
        );
    }

    // site setting
    public function index()
    {
        return Response()->json(
            $this->SettingRepository->getAll()

        );
    }

    public function UpdateSettings(UpdateSettingsRequest $request, $id)
    {
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        return response()->json(
            $this->SettingRepository->update($id, $validated)
        );
    }
}
