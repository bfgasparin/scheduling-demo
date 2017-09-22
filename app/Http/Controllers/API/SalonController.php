<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Salon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Salon`s actions to used by anyone
 *
 * @see Salon
 */
class SalonController extends Controller
{
    /*
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Salon::paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateRequest($request);

        return response(
            Salon::create($request->all()),
            201
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  Salon  $salon
     * @return \Illuminate\Http\Response
     */
    public function show(Salon $salon)
    {
        return $salon;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Salon $salon)
    {
        $this->validateRequest($request);

        return tap($salon)->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Salon $salon)
    {
        $salon->delete();

        return response()->json(['message' => __('Salon removed successfully')]);
    }

    protected function validateRequest(Request $request) : void
    {
        $this->validate($request, [
            'name' => 'required|string|max:100|unique:salons',
            'email' => 'required|email|max:100|unique:salons',
            'description' => 'required|string|max:255',
            'category' => [
                'required',
                'string',
                Rule::in(config('salon.categories'))
            ],
        ]);
    }
}
