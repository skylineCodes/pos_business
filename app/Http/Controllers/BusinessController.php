<?php

namespace App\Http\Controllers;

use App\Utils\BusinessUtils;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BusinessController extends Controller
{
    protected $businessUtils;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BusinessUtils $businessUtils)
    {
        $this->businessUtils = $businessUtils;
    }

    /**
     * Handles the registration of a new business and its owner
     * 
     * @return \Illuminate\Http\Response
     */
    public function postRegister(Request $request)
    {
        try {
            $validator = $request->validate([
                'name' => 'required|max:255',
                'currency_id' => 'required|numeric',
                'country' => 'required|max:255',
                'state' => 'required|max:255',
                'city' => 'required|max:255',
                'zip_code' => 'required|max:255',
                'landmark' => 'required|max:255',
                'time_zone' => 'required|max:255',
                'surname' => 'max:10',
                'email' => 'sometimes|nullable|email|max:255',
                'first_name' => 'required|max:255',
                'username' => 'required|min:4|max:255',
                'password' => 'required|min:4|max:255',
                'fy_start_month' => 'required',
                'accounting_method' => 'required'
            ]);

            DB::beginTransaction();

            // Create Owner
            // $owner_details = $request->only(['surname', 'first_name', 'last_name', 'username', 'email', 'password', 'language']);
            // $user = User::create_user($owner_details);

            $business_details = $request->only(['name', 'start_date', 'currency_id', 'time_zone']);
            $business_details['fy_start_month'] = 1;

            $business_location = $request->only(['name', 'country', 'state', 'city', 'zip_code', 'landmark', 'website', 'mobile', 'alternate_number']);

            // Create the business
            // $business_details['owner_id'] = $user->id;

            if (!empty($business_details['start_date'])) {
                $business_details['start_date'] = Carbon::createFromFormat(config('constants.default_date_format'), $business_details['start_date'])->toDateString();
            }

            // Upload Logo
            $logo_name = $this->businessUtils->uploadFile($request, 'business_logo', 'business_logo');
            if (!empty($logo_name)) {
                $business_details['logo'] = $logo_name;
            }

            $business = $this->businessUtils->createNewBusiness($business_details);

            // Update user with business id
            // $user->business_id = $business->id;
            // $user->save();

            $this->businessUtils->newBusinessDefaultResources($business->id, $user->id);
            $new_location = $this->businessUtils->addLocation($business->id, $business_location);

            
        } catch (Exception $e) {
            DB::rollback();

            Log::emergency("File:" . $e->getFile() .
                "Line:" . $e->getLine() .
                "Message:" . $e->getMessage());

            $response = response()->json(['error: ' => $e->getMessage()], 500);
        }

        return $response;
    }

}
