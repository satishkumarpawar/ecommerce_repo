<?php

namespace Webkul\API\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

use Webkul\Core\Repositories\SliderRepository;

use JWTAuth; #SKP

class SliderController extends Controller
{
   
   /**
     * Contains route related configuration.
     *
     * @var array
     */
    protected $_config;

    /**
     * Slider repository instance.
     *
     * @var \Webkul\Core\Repositories\SliderRepository
     */
    protected $sliderRepository;

    /**
     * Channels.
     *
     * @var array
     */
    protected $channels;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Core\Repositories\SliderRepository  $sliderRepository
     * @return void
     */
    public function __construct(SliderRepository $sliderRepository)
    {
        $this->sliderRepository = $sliderRepository;

        $this->_config = request('_config');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return response()->json([
            'message' => 'Page no found.',
        ]);
    }

    public function getList()
    {
        $sliders = $this->sliderRepository->getActiveSliders();
        return $sliders;
    }

    public function get()
    {
        $slider = $this->sliderRepository->getActiveSlider(request()->id);
        return (count($slider)>0?$slider[0]:$slider);
    }
    
}
