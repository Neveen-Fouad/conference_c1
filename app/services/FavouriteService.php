<?php

namespace App\Services;

use App\Enum\FavouriteType;
use App\Interfaces\FavouriteRepositoryInterface;
use App\Models\trip;
use App\Services\FlightService;
use App\Services\HotelService;
use App\Services\RestaurantService;
use App\Services\CountryServices;



class FavouriteService
{
    protected $favouriteRepository;
    protected $hotelService;
    protected $restaurantService;
    protected $flightService;
    protected $countryService;

    public function __construct(
        FavouriteRepositoryInterface $favouriteRepository,
        HotelService $hotelService,
        RestaurantService $restaurantService,
        FlightService $flightService,
        CountryServices $countryService
    ){
        $this->favouriteRepository = $favouriteRepository;
        $this->hotelService = $hotelService;
        $this->restaurantService = $restaurantService;
        $this->flightService = $flightService;
        $this->countryService = $countryService;
    }
    public function index()
    {
       return $this->withItemDetails($this->favouriteRepository->getAll());
    }
    public function filterFavouriteByType(string $type)
    {
       return $this->withItemDetails($this->favouriteRepository->filterFavouriteByType($type));
    }
    public function store(array $data)
    {
        $details = $this->getItemDetails($data['type'],$data['favouriteable_id']);
        if(empty($details)){
            throw new \InvalidArgumentException('The item being added to favourites does not exist.');
        }
        return $this->favouriteRepository->create($data);
    }
    public function destroy(int $favourite_id)
    {
        return $this->favouriteRepository->delete($favourite_id);
    }
    public function withItemDetails($favourites)
    {
        $favourites->getCollection()->transform(function ($favourite){
            $favourite->item_details = $this->getItemDetails(
                $favourite->type,
                $favourite->favouriteable_id
            );
            return $favourite;
        });
        return $favourites;
    }
    protected function getItemDetails(string $type , string $favouriteable_id):mixed
    {
        return match ($type){
            FavouriteType::Trip->value => trip::find($favouriteable_id),
            FavouriteType::Hotel->value => $this->hotelService->getHotelDetails((string) $favouriteable_id) ,
            FavouriteType::Restaurant->value => $this->restaurantService->getRestaurantDetails((string) $favouriteable_id) ,
            FavouriteType::Flight->value => $this->flightService->getFlightDetails((string) $favouriteable_id) ,
            FavouriteType::Country->value => $this->countryService->getCountryInfo((string) $favouriteable_id) ,
            default => null,
        };

    }

}
