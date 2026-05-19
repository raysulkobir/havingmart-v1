<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\OtpConfiguration;
use App\Models\User;
use Illuminate\Http\Request;
use Nexmo;
use Twilio\Rest\Client;

class SmsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $users = User::all();
        $users = Customer::select('id','first_name as name','phone')->where('sms_status', 'pending')->get()->map(function ($customer) {
            $customer->phone = $customer->phone;
            return $customer;
        });

        return view('otp_systems.sms.index',compact('users'));
    }

    //send message to multiple users
    public function send(Request $request)
    {
        $request->validate([
            'send_to' => 'required|in:selected,range',
            'user_phones' => 'required_if:send_to,selected|array',
            'customer_range' => 'required_if:send_to,range|nullable|regex:/^\s*[0-9]+\s*-\s*[0-9]+\s*$/',
            'content' => 'required|string',
            'template_id' => 'nullable|string',
        ]);

        $phones = collect();

        if ($request->send_to == 'range') {
            [$start, $end] = array_map('intval', preg_split('/\s*-\s*/', $request->customer_range));

            if ($start < 1 || $end < $start) {
                flash(translate('Please enter a valid customer range.'))->error();
                return back()->withInput();
            }

            $phones = Customer::orderBy('id')
                ->where('sms_status', 'pending')
                ->skip($start - 1)
                ->take($end - $start + 1)
                ->pluck('phone');
        } else {
            $phones = collect($request->user_phones);
        }

        $phones = $phones->filter()->unique()->values();

        if ($phones->isEmpty()) {
            flash(translate('No customers found for SMS sending.'))->error();
            return back()->withInput();
        }

        foreach ($phones as $phone) {
            sendSMS($phone, env('APP_NAME'), $request->content, $request->template_id);
        }

    	flash(translate('SMS has been sent.'))->success();
    	return redirect()->route('admin.dashboard');
    }
}
