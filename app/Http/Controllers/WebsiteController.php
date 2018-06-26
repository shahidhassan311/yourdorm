<?php
/**
 * Created by PhpStorm.
 * User: Hassan Shahid
 * Date: 6/13/2018
 * Time: 2:36 AM
 */

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Stripe\Stripe;
use App\User;
use App\Customer;
use App\Customer_charge_history;
use App\Request_form;
use Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class WebsiteController extends Controller
{
    public function panel(){


        $type_check = Auth::user()->role;
        if ($type_check == "member") {

            $property = DB::table('detail_links')
                ->select('*')
                ->paginate(9);

//            $request = DB::table('form_request')
//                ->select('*')
//                ->where('user_id',Auth::user()->id)
//                ->paginate(9);
//            $query = '';
//            $type = '';
//            $no_of_bedroom = '';
//            $property_type = '';
//            foreach ($request as $properties){
//                $no_of_bedroom = $properties->no_of_bedroom;
//                $property_type = $properties->property_type;
//            }
//
//            if(empty($property_type)){
//                $type = 'SELECT * FROM detail_links';
//            }else{
//                $query = "SELECT * FROM detail_links  WHERE property_type = '$property_type'";
//            }
//
//            if(!empty($no_of_bedroom)){
//                $query .= " AND bedroom = $no_of_bedroom";
//            }
//
//            if(!empty($query)) {
//                $result = DB::select(DB::raw($query));
//            }else{
//                $result = "Please select any property type!";
//            }

            return view('dashboard_panel',compact('property'));
        } else {
           return redirect('/login');
       }

    }
    public function index_page()
    {
        $property = DB::table('detail_links')
            ->select('*')
            ->get();
        return view('website.index',compact('property'));
    }
    public function request_page($string){
        $package = $string;
        return view('website.submit_a_request',compact('package'));
    }


    public function request_search(Request $request){

        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|min:6',
            'phone' => 'required',
        ]);
        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $password = $request->input('password');
        $property_type = $request->input('property_type');
        $no_of_bedroom = $request->input('no_of_bedroom');
        $uni_college = $request->input('uni_college');
        $transportation = $request->input('transportation');
        $distance_of_uni = $request->input('distance_of_uni');
        $price_range_per_month = $request->input('price_range_per_month');
        $length_of_lease = $request->input('length_of_lease');
        $move_in_date = $request->input('move_in_date');
        $parking = $request->input('parking');
        $additional_comments = $request->input('additional_comments');
        $package = $request->input('package');
        if ($package == 'international'){
            $packege_price = "635.00";
        }elseif ($package == 'domestic'){
            $packege_price = "119.95";
        }
        if(count($property_type) > 0){
            $new_property_type = "";
            if(!empty($property_type)) {
                foreach ($property_type as $val){
                    $new_property_type .= $val . ",";
                }

            }

        }
        try {
            Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
            $s_type = "member";
            $user = User::create([
                'name' => $first_name.' '.$last_name,
                'contact' => $phone,
                'email' => $email,
                'password' => \Hash::make($password),
                'role' => $s_type,
                'packege_type' => $package
            ]);
            $stripe_email = $request->stripeEmail;
            $stripe_token = $request->stripeToken;
            $customer = Customer_charge_history::create([
                'user_id' => $user->id,
                'email' => $stripe_email,
                'source' => $stripe_token,
                'amount' => $packege_price,
                'currency' => 'usd'
            ]);

            $request_form =  Request_form::create([
                'user_id' => $user->id,
                'property_type' => $new_property_type,
                'no_of_bedroom' => $no_of_bedroom,
                'uni_college' => $uni_college,
                'transportation' => $transportation,
                'distance_of_uni' => $distance_of_uni,
                'price_range_per_month' => $price_range_per_month,
                'length_of_lease' => $length_of_lease,
                'move_in_date' => $move_in_date,
                'parking' => $parking,
                'additional_comments' => $additional_comments
            ]);

            $s_link = "http://localhost:8000/login";
            $data =  array(
                'name' => $first_name.' '.$last_name,
                'email' => $email,
                'password' => $password,
                'Link'  => $s_link
            );
            Mail::send('website.email_template',$data, function ($message) {

                $message->from('mail@bergestategroup.com', 'Account Detail');

                $message->to('shahidhassan311@gmail.com')->subject('Yourdorm');

            });

            $request->session()->flash('alert-success', 'successfully transaction Please check your mail..!');
            return redirect('/login');

        } catch (\Exception $ex) {
            $request->session()->flash('alert-danger', 'Your transaction is failed please try again..!');
            return redirect('/request/'.$package)->withInput($request->all);
        }

    }

}