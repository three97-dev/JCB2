<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ZohoSerivce;
use Hash;
use Auth;
use Mail;

use DB;
use App\Models\User;
use App\Models\Car;
use App\Models\Company;
use App\Models\Address;


class UserController extends Controller
{

    public function syncUserInfo() {
        $zohoService = new ZohoSerivce();
        $zohoService->refreshUserData();
        return 'success';
    }

    public function login(Request $request) {
        $username = $request->username;
        if (!$username) return ['error' => 'Username required'];
        $password = $request->password;
        if (!$password) return ['error' => 'Password required'];

        $user = User::where('email', $username)->first();

        if (!$user) return ['error' => 'invalid_username'];

        if (!Hash::check($password, $user->password)) {
            return ['error' => 'invalid_password'];
        }

        if(!$user->api_token){
            $user->resetToken();
        }
        Auth::login($user);
        $res = $this->refreshAccountInfo($user);

        return ['success' => true, 'data' => $user, 'res'=> $res] ;
    }

    private function refreshCompanyData() {
        Company::where('owner_id', Auth::user()->zoho_index)->delete();

        $zohoService = new ZohoSerivce();
        $page = 0;
        $length = 200;
        while (true) {
            try {
                $records = $zohoService->searchAccounts(Auth::user()->zoho_index, $page++, $length);
                var_dump($records[0]);die;
            } catch (\Throwable $th) {
                break;
            }

            if (!is_array($records)) break;

            foreach ($records as $record) {
                $company_id = $record->getKeyValue('id');
                $company = Company::withTrashed()->where('owner_id')->where('zoho_index', $company_id)->first();
                if ($company) {
                    $company->restore();
                } else {
                    Company::create([
                        'owner_id' => Auth::user()->zoho_index,
                        'zoho_index' => $company_id
                    ]);
                }
            }
            if (count($records) < $length) break;
        }
    }

    public function forgotPassword(Request $request) {
        if (!$request->username) return ['error' => 'username required'];

        $user = User::where('email', $request->username)->first();
        if (!$user) {
            $zohoService = new ZohoSerivce();

            $account = $zohoService->getAccount($request->username);
            if (!$account) return ['error' => 'invalid email'];

            $user = new User();
            $user->email = $request->username;
            $user->name = '';
            $user->password = 'not set';
            $user->save();

            $user = $this->refreshAccountInfo($user, $account);
        }

        $user->resetRememberToken();

        Mail::send('mail-tmp-password', ['password' => $user->remember_token], function ($m) use ($user) {
            $m->to($user->email, $user->name)->subject('JCB App temporary password');
        });
        return ['success' => true];
    }

    private function refreshAccountInfo($user, $account = null) {
        $zohoService = new ZohoSerivce();
        if (!$account) {
            $account = $zohoService->getAccount($user->email);
            if (!$account) return null;
        }

        $user->zoho_index = $account->getKeyValue('id');
        $user->name = $account->getKeyValue('Account_Name');
        $user->country = $account->getKeyValue('Shipping_Country');
        $user->state = $account->getKeyValue('Shipping_State');
        $user->city = $account->getKeyValue('Shipping_City');
        $user->street = $account->getKeyValue('Shipping_Street');

        $zip_code = $account->getKeyValue('Shipping_Code');
        if($zip_code) {
            $code_data = $zohoService->getLocationByZipCode($zip_code);
            if ($code_data) {
                $user->lat = $code_data['lat'];
                $user->lng = $code_data['lng'];
            }
        }

        $user->save();
        return $user;
    }

    public function checkTempPassword(Request $request) {
        $username = $request->username;
        if (!$username) return ['error' => 'Username required'];
        $password = $request->password;
        if (!$password) return ['error' => 'Password required'];

        $user = User::where('email', $username)->first();
        if (!$user) return ['error' => 'Invalid email'];

        if ($user->remember_token != $password) {
            return ['error' => 'Invalid password'];
        }

        return ['success' => true];
    }

    public function resetPassword(Request $request) {
        $username = $request->username;
        if (!$username) return ['error' => 'Username required'];

        $temp_password = $request->temp_password;
        if (!$temp_password) return ['error' => 'Temp password required'];

        $password = $request->password;
        if (!$password) return ['error' => 'Password required'];

        $user = User::where('email', $username)->first();
        if (!$user) return ['error' => 'Invalid email'];

        if ($user->remember_token != $temp_password) {
            return ['error' => 'Invalid temp password'];
        }

        $user->password = bcrypt($password);
        $user->save();

        return ['success' => true];
    }
    public function getProfileSettings() {
        $zohoService = new ZohoSerivce();
        $account = $zohoService->getAccount(Auth::user()->email); 
        $get_address = Address::where('user_id', Auth::user()->id)->get(); 
        $SecondaryAddress = array();
        foreach($get_address as $address){
            // $SecondaryAddress[$address->tab_id] = [
            $SecondaryAddress[] = [
                'id'=> $address->id,
                'user_id' => Auth::user()->id,
                'tab_id' => $address->tab_id,
                'billing_name' => $address->billing_name,
                'billing_suite' => $address->billing_suite,
                'secondary_radius' => $address->secondary_radius,
                'secondary_street' => $address->secondary_street,
                'secondary_city' => $address->secondary_city,
                'secondary_state' => $address->secondary_state,
                'secondary_code' => $address->secondary_code
            ];
        }
        // echo "<pre>";print_r($SecondaryAddress);die;
        $shippingAddress = [
            "street"=>$account->getKeyValue("Shipping_Street"),
            "city"=>$account->getKeyValue("Shipping_City"),
            "state"=>$account->getKeyValue("Shipping_State"),
            "code"=>$account->getKeyValue("Shipping_Code"),            
        ];
        $shippingAddress['shipping_name'] = Auth::user()->shipping_name;
        $shippingAddress['shipping_suite'] = Auth::user()->shipping_suite;
        $shippingAddress['primary_radius'] = Auth::user()->primary_radius;
        return json_encode(['user'=>["username" => $account->getKeyValue("Account_Name"), "companyName" => $account->getKeyValue("Owners_Name"), "photo" => Auth::user()->photo,'default_address'=>Auth::user()->default_address,'SecondaryAddress'=> $SecondaryAddress, 'shippingAddress'=> $shippingAddress ]]);
    }
    public function saveProfileSettings(Request $request) {
        $user = User::where('email', Auth::user()->email)->first();
        $user->name = $request->username;
        $user->photo = $request->photo;
        if($request->shippingAddress['default_address'] == 1){
            $user->default_address = 0;
            $defaultAddress = 0;
        }
        $user->shipping_name = $request->shippingAddress['shipping_name'];
        $user->shipping_suite = $request->shippingAddress['shipping_suite'];
        $user->primary_radius = $request->shippingAddress['primary_radius'];
        $user->save();
        
       
        // echo "<pre>";print_r($request->SecondaryAddress);die;
        // if(!empty($request->selectedtabid) && $request->selectedtabid =='Primary Address'){
        //     if (!$request->shippingAddress['primary_radius'])
        //      return ['error2' => 'Please select Radius from location'];
        //     if ($request->shippingAddress['primary_radius'] == '0')
        //      return ['error2' => 'Please select Radius greater than 1'];
        //     if ($request->shippingAddress['primary_radius'] > '150')
        //      return ['error2' => 'Please select Radius less than 150'];
        // }
        // if(!empty($request->billingAddress)  && $request->selectedtabid =='Alt Address'){
        //     if (!$request->billingAddress['secondary_radius'])
        //      return ['error3' => 'Please select Radius from location'];
        //     if ($request->billingAddress['secondary_radius'] == 0)
        //      return ['error3' => 'Please select Radius greater than 1'];
        //     if ($request->billingAddress['secondary_radius'] > 150)
        //      return ['error3' => 'Please select Radius less than 150'];
        // }
        $address = array();

       
        if(isset($request->SecondaryAddress) && !empty($request->SecondaryAddress)){
            // Address::where('user_id',Auth::user()->id)->delete();
            foreach($request->SecondaryAddress as $secondary){
                $address = array(
                    'user_id' => Auth::user()->id,
                    'tab_id' => $secondary['tab_id'],
                    'billing_name' => $secondary['billing_name'],
                    'billing_suite' => $secondary['billing_suite'],
                    'secondary_radius' => $secondary['secondary_radius'],
                    'secondary_street' => $secondary['secondary_street'],
                    'secondary_city' => $secondary['secondary_city'],
                    'secondary_state' => $secondary['secondary_state'],
                    'secondary_code' => $secondary['secondary_code']

                );
                if(isset($secondary['id']) != ''){
                    Address::where('id', $secondary['id'])->update($address);
                    $id = $secondary['id'];
                }else{
                    $id = Address::insertGetId($address);
                }
                if($secondary['default_address'] == 1){
                    $defaultAddress = $id;
                    User::where('email', Auth::user()->email)->update(['default_address'=>$id]);
                }
            }
        }

        $zohoService = new ZohoSerivce();
        $updateProfileSettings = $zohoService->updateProfileSettings($user->zoho_index, $request->username, $request->companyName,$request->shippingAddress);
        $altAddress = Address::select('id','user_id','tab_id','billing_name','billing_suite','secondary_radius','secondary_street','secondary_city','secondary_state','secondary_code')->where('user_id',Auth::user()->id)->get();
        if($altAddress){
            $addressData = $altAddress->toArray();
        }else{
            $addressData = [];
        }
        return json_encode(['res'=>"success", 'data'=>$addressData, 'default_address'=>$defaultAddress]);
    }
  
    public function uploadPhoto(Request $request) {
        $time = new \DateTime();
        $imageName = time().'.'.$request->form->extension();
        $request->form->move(public_path('img/profiles'), $imageName);
        return $imageName;
    }
    public function deleteAddress(Request $request) {
        $id = $request->id;
        if (!$id) return ['error' => 'Address id required'];
        $user = User::where('email', Auth::user()->email)->first();
        if($user && $user->default_address == $id){
            User::where('email', Auth::user()->email)->update(['default_address'=>0]);
        }
        Address::where('id',$id)->delete();
        return json_encode(['res'=>"success"]);
    }



    public function logout(Request $request) {
        Auth::logout();
        return redirect('/login');
    }

}
