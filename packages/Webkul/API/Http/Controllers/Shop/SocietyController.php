<?php

namespace Webkul\API\Http\Controllers\Shop;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

use Webkul\Core\Repositories\SocietyRepository;
use Webkul\API\Http\Resources\Core\Society as SocietyResource;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SocietyController extends Controller
{
    use DispatchesJobs, ValidatesRequests;

    protected $SocietyRepository;
    

    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct( SocietyRepository $SocietyRepository)
    {
        $this->_config = request('_config');

        $this->SocietyRepository = $SocietyRepository;
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
       
           return SocietyResource::collection($this->SocietyRepository->getAll());
               
    }

    public function search()
    {
       
           return SocietyResource::collection($this->SocietyRepository->search());
       
               
    }

       
    public function get()
    {
        
        if (!is_null(request()->input('id'))) {
            return SocietyResource::collection($this->SocietyRepository->get(request()->input('id')));
        } else {
           return [];
        }
        
    }

    public function create()
    {
       
        $this->validate(request(), [
            'name' => 'string|required',
            'sector' => 'string|nullable',
            'city' => 'string|nullable',
            'district' => 'string|nullable',
            'state' => 'string|nullable',
            'description' => 'string|nullable',
            //'postcode' => 'required',
            
        ]);
    
           
        $Society=$this->SocietyRepository->create(request());

        return response()->json([
            'message' => 'Society created successfully.',
            'data'    => new SocietyResource($Society),
        ]);
        
    }

    public function update()
    {
       
        $this->validate(request(), [
            'name' => 'string|required',
            'sector' => 'string|nullable',
            'city' => 'string|nullable',
            'district' => 'string|nullable',
            'state' => 'string|nullable',
            'description' => 'string|nullable',
            //'postcode' => 'required',
            
        ]);
    
    
        $Society=$this->SocietyRepository->update(request());

        return response()->json([
            'message' => 'Society updated successfully.',
            'data'    => new SocietyResource($Society),
        ]);
        
    }

    public function delete()
    {
        $Society = $this->SocietyRepository->findOrFail(request()->id);

        try {
           
            $this->SocietyRepository->delete(request()->id);

            return response()->json(['message' => true], 200);
        } catch (\Exception $e) {
            report($e);
        }

        return response()->json(['message' => false], 200);

    }

}
