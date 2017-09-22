<?php
namespace App\Http\Controllers\API;

use App\Salon;
use App\Salon\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Salon Service`s actions to be used by anyone
 *
 * @see App\User;
 * @see Controller
 */
class SalonServiceController extends Controller
{
    /**
     * Display a listing of the services of a given Salon
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Salon $salon)
    {
        return $salon->services()->paginate();
    }

    /**
     * Display the specified resource of the given Salon
     *
     * @param  \App\Salon\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Salon $salon, int $service)
    {
        return $salon->services()->findOrFail($service);
    }
}
