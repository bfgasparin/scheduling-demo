<?php

namespace App\Http\Controllers\API\Salon;

use App\Salon\Service;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

/**
 * Controller for App\Salon\Worker users to manage Salon services
 *
 * @see App\Salon\Service
 * @see App\Salon\Worker
 */
class ServiceController extends Controller
{
    /**
     * Creates a new instance of the controller
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api-salon-admins');
    }

    /**
     * Display a listing services of the salon
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Service::paginate();
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Service::class);

        $this->validateRequest($request);

        return response(
            $request->user()->getSalon()->services()->create($request->all()),
            201
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Salon\Service  $salonService
     * @return \Illuminate\Http\Response
     */
    public function show(Service $salonService)
    {
        return $salonService;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Salon\Service  $salonService
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $salonService)
    {
        $this->authorize('update', $salonService);

        $this->validateRequest($request);

        return tap($salonService)->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Salon\Service  $salonService
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $salonService)
    {
        $salonService->delete();

        return response()->json(['message' => __('Service removed successfully')]);
    }

    protected function validateRequest(Request $request) : void
    {
        $this->validate($request, [
            'name' => 'required|unique:salon_services|string|max:100',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric|between:0,999.99',
            'duration' => 'required|integer|min:5',
            'cost' => 'required|numeric|between:0,999.99',
            'client_visibility' =>[
                'string',
                Rule::in(Service::CLIENT_VISIBILITIES)
            ],
        ]);
    }
}
