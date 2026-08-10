<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\SettingRepositoryInterface;
use App\Http\Requests\StoreLogoRequest;
use App\Http\Requests\UpdateLogoRequest;
use App\Http\Requests\StoreSiteNameRequest;
use App\Http\Requests\UpdateSiteNameRequest;
use App\Http\Requests\StoreContactInfoRequest;
use App\Http\Requests\UpdateContactInfoRequest;
use App\Http\Requests\SocialLinksRequest;
use App\Http\Requests\BannerRequest;
use App\Http\Requests\StoreSettingsRequest;
use App\Http\Requests\UpdateSettingsRequest;

class SettingController extends Controller
{
    protected $SettingRepository;

    public function __construct(SettingRepositoryInterface$settingRepository){
         $this->SettingRepository = $settingRepository;
    }

    function storeSettings(StoreSettingsRequest $request){
        $validated = $request->validated();
        
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        return response()->json(
            $this->SettingRepository->create($validated)
        );
    }
    //site setting 
    public function index(){
        return Response()->json(
            $this->SettingRepository->getAll()

        );
    }

    function UpdateSettings(UpdateSettingsRequest $request, $id){
        $validated = $request->validated();
        
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        return response()->json(
            $this->SettingRepository->update($id, $validated)
        );
    }
  


  
    
    

        
    
    
}