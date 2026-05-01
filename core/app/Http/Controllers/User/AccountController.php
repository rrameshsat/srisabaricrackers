<?php

namespace App\Http\Controllers\User;

use App\{
    Http\Requests\UserRequest,
    Http\Controllers\Controller,
    Repositories\Front\UserRepository
};
use App\Helpers\ImageHelper;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AccountController extends Controller
{

    /**
     * Constructor Method.
     *
     * Setting Authentication
     *
     * @param  \App\Repositories\Back\UserRepository $repository
     *
     */
    public function __construct(UserRepository $repository)
    {
        $this->middleware('auth');
        $this->middleware('localize');
        $this->repository = $repository;
    }

    public function index()
    {
        return view('user.dashboard.dashboard',[
            'allorders' => Order::whereUserId(Auth::user()->id)->count(),
            'pending' => Order::whereUserId(Auth::user()->id)->whereOrderStatus('pending')->count(),
            'progress' => Order::whereUserId(Auth::user()->id)->whereOrderStatus('In Progress')->count(),
            'delivered' => Order::whereUserId(Auth::user()->id)->whereOrderStatus('Delivered')->count(),
            'canceled' => Order::whereUserId(Auth::user()->id)->whereOrderStatus('Canceled')->count()

        ]);

    }


    public function profile()
    {
        $user = Auth::user();
        $check_newsletter = Subscriber::where('email',$user->email)->exists();
        return view('user.dashboard.index',[
            'user' => $user,
            'check_newsletter' => $check_newsletter,
        ]);
    }



    public function profileUpdate(UserRequest $request)
    {   
     
        $this->repository->profileUpdate($request);
        Session::flash('success',__('Profile Updated Successfully.'));
        return redirect()->back();
    }

    public function addresses()
    {
        $user = Auth::user();
        return view('user.dashboard.address',[
            'user' => $user
        ]);
    }

    public function billingSubmit(Request $request)
    {
        $rules = [
            'bill_address1' => 'required|max:100',
            'bill_address2' => 'nullable|max:100',
            'bill_zip'      => 'nullable|max:100',
            'bill_city'      => 'required|max:100',
            'bill_company'   => 'nullable|max:100',
            'bill_country'   => 'required|max:100',
        ];
// If India has states, make state_id optional
        try {
            $india = \DB::table('countries')->where('name','India')->first();
            if ($india) {
                $stateCount = \DB::table('states')->where('country_id', (int)$india->id)->count();
                if ($stateCount > 0) {
                    $rules['bill_state_id'] = 'nullable|exists:states,id';
                }
            }
        } catch (\Exception $e) {
            // ignore
        }
        $request->validate($rules);

        // Handle bill_state_id - use state_id if bill_state_id not set
        $input = $request->all();
        if (!isset($input['bill_state_id']) || empty($input['bill_state_id'])) {
            $input['bill_state_id'] = $input['state_id'] ?? null;
        }
        
        $user =  Auth::user();
        $user->update($input);
        Session::flash('success',__('Address update successfully'));
        return back();
    }

    public function shippingSubmit(Request $request)
    {
        $rules = [
            'ship_address1' => 'required|max:100',
            'ship_address2' => 'nullable|max:100',
            'ship_zip'      => 'nullable|max:100',
            'ship_city'      => 'required|max:100',
            'ship_company'   => 'nullable|max:100',
            'ship_country'   => 'required|max:100',
        ];
        // If India has states, require ship_state_id
        try {
            $india = \DB::table('countries')->where('name', 'India')->first();
            if ($india) {
                $stateCount = \DB::table('states')->where('country_id', (int)$india->id)->count();
                if ($stateCount > 0) {
                    $rules['ship_state_id'] = 'nullable|exists:states,id';
                }
            }
        } catch (\Exception $e) {
            // ignore
        }
        $request->validate($rules);
        
        // Handle ship_state_id - use state_id if ship_state_id not set
        $input = $request->all();
        if (!isset($input['ship_state_id']) || empty($input['ship_state_id'])) {
            $input['ship_state_id'] = $input['state_id'] ?? null;
        }
        
        $user =  Auth::user();
        $user->update($input);
        Session::flash('success',__('Address update successfully'));
        return back();
    }


    public function removeAccount()
    {
        $user = User::where('id',Auth::user()->id)->first();
        ImageHelper::handleDeletedImage($user,'photo','assets/images/');
        $user->delete();
        Session::flash('success',__('Your account successfully remove'));
        return redirect(route('front.index'));
    }


}
