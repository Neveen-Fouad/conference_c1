<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSettingsRequest;
use App\Http\Requests\UpdateSettingsRequest;
use App\Interfaces\SettingRepositoryInterface;
use App\Models\Setting;

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

        // There is one brand configuration for the whole website. Reuse the
        // existing record so repeated saves cannot create competing settings.
        $settings = Setting::query()->latest('updated_at')->latest('id')->first();
        if ($settings) {
            $settings->update($validated);
            return response()->json($settings);
        }

        return response()->json($this->SettingRepository->create($validated));
    }

    // site setting
    public function index()
    {
        return Response()->json(
            Setting::query()->latest('updated_at')->latest('id')->get()

        );
    }

    /** Public, read-only branding data for guest and authenticated pages. */
    public function publicSettings()
    {
        return response()->json([
            'data' => Setting::query()->latest('updated_at')->latest('id')->first(),
        ]);
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
