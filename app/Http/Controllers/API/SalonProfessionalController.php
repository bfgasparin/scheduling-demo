<?php
namespace App\Http\Controllers\API;

use App\Salon;
use App\Salon\Professional;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Salon Professional`s actions to used by anyone
 *
 * @see Salon
 */
class SalonProfessionalController extends Controller
{
    /**
     * Display a listing of the professionals of a given Salon
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Salon $salon)
    {
        return $salon->employees()->professional()->paginate();
    }

    /**
     * Display the specified professional of the given Salon
     *
     * @param int $professional
     * @return \Illuminate\Http\Response
     */
    public function show(Salon $salon, int $professional)
    {
        return $salon->employees()->professional()->findOrFail($professional);
    }
}
