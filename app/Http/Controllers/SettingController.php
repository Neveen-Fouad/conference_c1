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


class SettingController extends Controller
{
    protected $SettingRepository;

    public function __construct(SettingRepositoryInterface$settingRepository){
         $this->SettingRepository = $settingRepository;
    }

    //site setting 
    public function index(){
        return Response()->json(
            $this->SettingRepository->getAll()

        );
    }

    //set logo
    public function setLogo(StoreLogoRequest $request){
        return response()->json(
            $this->SettingRepository->create($request->validated())
        );
    }

    // update logo 
    public function updateLogo(UpdateLogoRequest $request, $id){
        return response()->json(
            $this->SettingRepository->update($id, $request->validated())
        );
        }

        //set site name 

    public function setSiteName(StoreSiteNameRequest $request){
        return response()->json(
            $this->SettingRepository->create($request->validated())
        );
    }
    // update site name 
    public function updateSiteName(UpdateSiteNameRequest $request, $id)
    {
        return response()->json(
            $this->SettingRepository->update($id, $request->validated())
        );
    }
    //set contact info
    public function setContactInfo(StoreContactInfoRequest $request){
        return response()->json(
            $this->SettingRepository->create($request->validated())
        );
    }
    //update contact information 
    public function updateContactInfo(UpdateContactInfoRequest $request, $id){
        return response()->json(
             $this->SettingRepository->update($id, $request->validated())

        );
    }
     // set social links 
    public function setSocialLinks(SocialLinksRequest $request){
        return response()->json(
            $this->SettingRepository->create($request->validated())

        );
    }

    // update social links
    public function updateSocialLinks(SocialLinksRequest $request, $id){
        return response()->json(
             $this->SettingRepository->update($id, $request->validated())

        );
    
    }
    public function setBanner(BannerRequest $request){
                return response()->json(
            $this->SettingRepository->create($request->validated())
        );

    }
    public function updateBanner(BannerRequest $request, $id){
         return response()->json(
             $this->SettingRepository->update($id, $request->validated())
         );

    }

        
    }

