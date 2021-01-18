<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ZohoSerivce;

use Auth;
use DB;
use App\Models\User;
use App\Models\Car;
use App\Models\LikeBind;
use App\Models\Filter;
use App\Models\Company;
use GuzzleHttp\Client;
use Psr\Cache;



class CarController extends Controller
{
    private $ZohoBooksAccessToken;
    function __construct() {
        if (!isset($this->ZohoBooksAccessToken)) {
            $AccessToken = $this->getZohoBooksAccessToken();
            if ($AccessToken !== false) $this->ZohoBooksAccessToken = $AccessToken;
        }
    }

    private function getZohoBooksAccessToken()
    {
        try {
            $Request = (new Client())->post('https://accounts.zoho.com/oauth/v2/token', [
                'form_params' => [
                    'client_id' => env('ZOHO_CRM_CLIENT_ID'),
                    'client_secret' => env('ZOHO_CRM_CLIENT_SECRET'),
                    'grant_type' => 'refresh_token',
                    'refresh_token' => env('ZOHO_REFRESH_TOKEN')
                ]
            ]);
            $JSON = $Request->getBody()->getContents();
            $Response = json_decode($JSON, true);
            return sprintf('Zoho-oauthtoken %s', $Response['access_token']) ?? false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function test() {
        $zohoService = new ZohoSerivce();
        $res = $zohoService->queryTest("Deals")[0]->getKeyValue('id');
        return $res;
    }

    public function sync(Request $request) {
        $id = $request->id;
        $price = $request->price;
        $tow_company = $request->tow_company;
        $stage = $request->stage;
        $car = Car::where('Reference_Number', $id)->first();
        if(!$car) return null;
        $car->Buyers_Quote = $price;
        $car->save();
        return $car;
    }

    public function index(Request $request)
    {
        // return json_encode(['res' => $this->test()]);

        $page = ($request->page ?: 1) - 1;
        $records_per_page = $request->records_per_page ?: 8;
        $select = [
            'index',
            'Year',
            'Make',
            'Model',
            'City',
            'Does_the_Vehicle_Run_and_Drive',
            'Miles',
            'Buyers_Quote',
            'Zip_Code',
            'Reference_Number',
            'Closing_Date',
            'Stage',
        ];
        // $quey = null;
        if ($request->type == 'like') {
            $query = LikeBind::where('user_id', Auth::user()->id)->leftJoin('cars', 'car_id', '=', 'cars.id');
            $select[] = 'like_binds.id as is_liked';
            $select[] = 'Airbags_Deployed';
            $select[] = 'Is_There_Any_Body_Damage_Broken_Glass_2';
            $select[] = 'Is_there_any_Body_Damage_Broken_Glass';
            $select[] = 'Is_There_Any_Broken_Glass_Windows_etc';
            $select[] = 'Are_all_the_tires_mounted';
            $select[] = 'Which_tires_are_missing';
            $select[] = 'Are_All_the_Tires_Inflated';
            $select[] = 'Which_ones_are_flat';
            $select[] = 'Fire_or_Flood_Damage';
            $select[] = 'Any_Missing_Body_Panels_Interior_or_Engine_Parts';
            $select[] = 'What_Kind_of_Mechanical_Issues_Are_There';
        } else {
            if (!$request->page_type) $request->page_type = 'cars';

            if ($request->page_type == 'cars') {
                $stage = 'Given Quote';
                $duration = -30;
                $select[] = 'like_binds.id as is_liked';
                $select[] = 'Airbags_Deployed';
                $select[] = 'Is_There_Any_Body_Damage_Broken_Glass_2';
                $select[] = 'Is_there_any_Body_Damage_Broken_Glass';
                $select[] = 'Is_There_Any_Broken_Glass_Windows_etc';
                $select[] = 'Are_all_the_tires_mounted';
                $select[] = 'Which_tires_are_missing';
                $select[] = 'Are_All_the_Tires_Inflated';
                $select[] = 'Which_ones_are_flat';
                $select[] = 'Fire_or_Flood_Damage';
                $select[] = 'What_Kind_of_Mechanical_Issues_Are_There';
                $select[] = 'Scheduled_Time';
                $select[] = 'Scheduled_Notes';

                $query = Car::leftJoin('like_binds', 'cars.id', '=', 'like_binds.car_id')
                            ->where('Stage', $stage)
                            ->where('Tow_Company_id', '<>', Auth::user()->zoho_index)
                            ->whereNotNull('Buyers_Quote')
                            ->where('Closing_Date', '>=', date('Y-m-d', strtotime($duration . ' days')));
            } elseif ($request->page_type == 'bids') {
                $query = Car::where(function($sub_query) {
                    $sub_query->where('Stage', 'Given Quote')->
                                orwhere('Stage', 'Deal Made');
                });

                $query = $query->where('Tow_Company_id', Auth::user()->zoho_index);

                if ($request->status && $request->status == 'Won') {
                    $query = $query->where('Stage', 'Deal Made');
                } elseif ($request->status && $request->status == 'Active') {
                    $query = $query->where('Stage', 'Given Quote');
                }
            } elseif ($request->page_type == 'schedulings') {
                $select[] = 'Scheduled_Time';
                $select[] = 'Scheduled_Notes';

                $query = Car::where(function($sub_query) {
                                $sub_query->where('Stage', 'Picked Up');
                                $sub_query->orwhere('Stage', 'Deal Made');
                                return $sub_query;
                            });

                $query = $query->where('Tow_Company_id', Auth::user()->zoho_index)
                                ->whereNotNull('Buyers_Quote');

                if ($request->status == 'Unscheduled') {
                    $query = $query->where('Stage', 'Deal Made')->whereNull('Scheduled_Time');
                } elseif ($request->status == 'Scheduled') {
                    $query = $query->where('Stage', 'Deal Made')->whereNotNull('Scheduled_Time');;
                } elseif ($request->status == 'Picked-Up') {
                    $query = $query->where('Stage', 'Picked Up');
                }

            } elseif ($request->page_type == 'payments') {
                $query = Car::where(function($sub_query) {
                    $sub_query->where('Stage', 'Paid');
                    $sub_query->orwhere('Stage', 'Deal Made');
                    $sub_query->orwhere('Stage', 'Picked Up');
                    return $sub_query;
                });

                $query = $query->where('Tow_Company_id', Auth::user()->zoho_index)
                                ->whereNotNull('Buyers_Quote');

                if ($request->status == 'Paid') {
                    $query = $query->where('Stage', 'Paid');
                } elseif ($request->status == 'Unpaid') {
                    $query = $query->where('Stage', 'Deal Made');
                } elseif ($request->status == 'Overdue') {
                    $query = $query->where('Stage', 'Picked Up');
                }
            }
        }

        $query = $query->select($select)
                    // ->orderby('Closing_Date', 'desc')
                       ->orderby('cars.index', 'desc');

        if ($request->Miles) $query = $query->where('Miles', '<', $request->Miles);
        if ($request->Does_the_Vehicle_Run_and_Drive) $query = $query->where('Does_the_Vehicle_Run_and_Drive', $request->Does_the_Vehicle_Run_and_Drive);
        if ($request->buyers_quote) $query = $query->where('buyers_quote', '<', $request->buyers_quote);
        if ($request->Any_Missing_Body_Panels_Interior_or_Engine_Parts) $query = $query->where('Any_Missing_Body_Panels_Interior_or_Engine_Parts', $request->Any_Missing_Body_Panels_Interior_or_Engine_Parts);
        if ($request->Do_they_have_a_Title) $query = $query->where('Do_they_have_a_Title', $request->Do_they_have_a_Title);
        if ($request->Fire_or_Flood_Damage) $query = $query->where('Fire_or_Flood_Damage', $request->Fire_or_Flood_Damage);
        if ($request->Reference_Number) $query = $query->where('Reference_Number', $request->Reference_Number);
        if ($request->Year) $query = $query->where('Year', $request->Year);
        if ($request->Make) $query = $query->where('Make', $request->Make);
        if ($request->Model) $query = $query->where('Model', $request->Make);
        if ($request->distance) {
            $lat_min = Auth::user()->lat -  $request->distance / 69;
            $lat_max = Auth::user()->lat +  $request->distance / 69;
            $lng_min = Auth::user()->lng -  $request->distance / 54.6;
            $lng_max = Auth::user()->lng +  $request->distance / 54.6;

            $query = $query->where('cars.lat', '>=', $lat_min);
            $query = $query->where('cars.lat', '<=', $lat_max);
            $query = $query->where('cars.lng', '>=', $lng_min);
            $query = $query->where('cars.lng', '>=', $lng_max);

            // var_dump($lat_min);
            // var_dump($lat_max);
            // var_dump($lng_min);
            // var_dump($lng_max);
        }

        $total = $query->count();
        $cars = $query->skip($page * $records_per_page)->take($records_per_page)->get();
        return ['total' => $total,  'data' => $cars, 'user'=> Auth::user()];
    }


    private function createInvoice($Data)
    {
        try {
            $Request = (new Client())->post(env('ZOHO_BOOKS_API_URI').'invoices?organization_id='.env('ZOHO_ORGANIZATION_ID'), [
                'json' => $Data,
                'headers' => [
                    'Authorization' => $this->ZohoBooksAccessToken
                ]
            ]);
            $JSON = $Request->getBody()->getContents();
            return json_decode($JSON, true);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $er = $e->getResponse()->getBody()->getContents();
            print_r(json_decode($er));
            return false;
        }
    }

    private function getItem($Id)
    {
        try {
            $Request = (new Client())->get(env('ZOHO_BOOKS_API_URI').'items', [
                'query' => [
                    'cf_crm_car_id' => $Id,
                    'per_page' => 1,
                    'organization_id' => env('ZOHO_ORGANIZATION_ID')
                ],
                'headers' => [
                    'Authorization' => $this->ZohoBooksAccessToken
                ]
            ]);
            $JSON = $Request->getBody()->getContents();
            $Response = json_decode($JSON, true);
            $Item = $Response['items'][0] ?? false;
            if (!$Item) return false;
            if (!isset($Item['cf_crm_car_id'])) return false;
            if ($Item['cf_crm_car_id'] != $Id) return false;
            return $Item;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return false;
        }
    }
    private function createItem($Data)
    {
        try {
            $Request = (new Client())->post(env('ZOHO_BOOKS_API_URI').'items', [
                'json' => $Data,
                'headers' => [
                    'Authorization' => $this->ZohoBooksAccessToken
                ]
            ]);
            if ($Request->getStatusCode() != 201) return false;
            $JSON = $Request->getBody()->getContents();
            $Response = json_decode($JSON, true);
            return $Response['item'] ?? false;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            print_r($e->getResponse()->getBody()->getContents());
            return false;
        }
    }
    private function getZohoBooksCustomer($Id)
    {
        try {
            $Request = (new Client())->get(env('ZOHO_BOOKS_API_URI').'contacts', [
                'query' => [
                    'cf_zoho_crm_id' => $Id,
                    'per_page' => 1,
                    'organization_id' => env('ZOHO_ORGANIZATION_ID')
                ],
                'headers' => [
                    'Authorization' => $this->ZohoBooksAccessToken
                ]
            ]);
            $JSON = $Request->getBody()->getContents();
            $Response = json_decode($JSON, true);
            $Customer = $Response['contacts'][0] ?? false;
            if (!$Customer) return false;
            if ($Customer['cf_zoho_crm_id'] != $Id) return false;
            return $Customer;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return false;
        }
    }
    private function createZohoBooksCustomer($Name, $Email, $Id)
    {
        try {
            $Request = (new Client())->post(env('ZOHO_BOOKS_API_URI').'contacts?organization_id='.env('ZOHO_ORGANIZATION_ID'), [
                'json' => [
                    'contact_name' => $Name,
                    'email' => $Email,
                    'custom_fields' => [
                        [
                            'label' => 'Zoho CRM ID',
                            'value' => $Id
                        ]
                    ]
                ],
                'headers' => [
                    'Authorization' => $this->ZohoBooksAccessToken
                ]
            ]);
            if ($Request->getStatusCode() != 201) return false;
            $JSON = $Request->getBody()->getContents();
            $Response = json_decode($JSON, true);
            return $Response['contact'] ?? false;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            print_r($e->getResponse()->getBody()->getContents());

            return false;
        }
    }
    public function like(Request $request, $id) {
        $car = Car::where('index',$id)->first();
        if(!$car) return 'invalid car';

        $link =  LikeBind::where('car_index', $id)->first();

        $zohoService = new ZohoSerivce();
        // $zohoService->updateInvoice('1061914000238914058', 'Book and crm test', Auth::user());
        // return 'success';
        $deals = $zohoService->getRecords('Deals', 1, 5);
        return $deals[0]->getKeyValue('id');
        if ($request->like) {
            if (!$link) {
                $link = new LikeBind();
                $link->user_id = Auth::user()->id;
                $link->car_index = $car->index;
                $link->car_id = $car->id;
                $link->save();

            } else {
                if ($link) {
                    $link->delete();
                }
            }

        }
        // return $deals[0]->getKeyValue('Closing_Date');
        // $zohoService->createInvoices('1061914000238437019', "first Create Test", Auth::user());
        return 'success';
    }


    public function bid(Request $request, $id) {
        $car = Car::where('index', $id)->first();
        if(!$car) return 'invalid car';

        $price = $request->price;
        $user_id = Auth::user()->zoho_index;
        $user_name = Auth::user()->name;
        $user_email = Auth::user()->email;
        $now = new \DateTime();

        // update local DB
        $car->Buyers_Quote = $price;
        $car->Modified_By_id = $user_id;
        $car->Modified_By_name = $user_name;
        $car->Modified_By_email = $user_email;
        $car->Tow_Company_id = $user_id;
        $car->Tow_Company_name = $user_name;
        $car->Modified_Time = $now->format('Y-m-d H:i:s');
        $car->save();

        $zohoService = new ZohoSerivce();
        $deal = $zohoService->updateDealInfo($id, $price, $user_id, $user_name, $user_email, $now);

        return 'success';

    }

    public function setSchedule(Request $request, $id) {
        $car = Car::where('index', $id)->first();
        if (!$car) return ['error' => 'invalid car'];

        $car->Scheduled_Time = $request->Scheduled_Time;
        $car->Scheduled_Notes = $request->Scheduled_Notes;
        $car->save();
        $zohoService = new ZohoSerivce();
        $now = new \DateTime($request->Scheduled_Time);
        $schedule = $zohoService->scheduleTime($id, $now);

        return json_encode(array('res' => $car));
    }

    public function pick(Request $request, $id) {
        $car = Car::where('index', $id)->first();
        if (!$car) return ['error' => 'invalid car'];

        $car->Scheduled_Time = $request->Scheduled_Time;
        $car->Stage = "Picked Up";
        $car->save();
        $zohoService = new ZohoSerivce();
        $now = new \DateTime($request->Scheduled_Time);
        $pick = $zohoService->scheduleTime($id, $now, true);

        return json_encode(array('res' => $car));
    }

    public function pay(Request $request) {
        $arr = $request->cars;
        $zohoService = new ZohoSerivce();

        $now = new \DateTime($request->Scheduled_Time);
        foreach($arr as $car_id) {
            $car = Car::where('index', $car_id)->first();
            if($car) {
                $car->Stage = "Paid";
                $car->save();
            }
        }

        // change in crm deals
        $deals = $zohoService->pay4Car($arr);

        // create books invoice

        $InvoiceItems = [];
        $InvoiceTotal = 0;
        foreach ($request->cars as $car) {
            $Car = $this->getItem($car['index']);
            if (!$Car) {
                $Car = $this->createItem([
                    'name' => '#'.implode(" ", array($car['Reference_Number'], $car['Year'], $car['Make'], $car['Model'])),
                    'rate' => $car['Buyers_Quote'],
                    'custom_fields' => [
                        [
                            'label' => 'CRM Car ID',
                            'value' => $car['index']
                        ],
                        [
                            'label' => 'Car Ref#',
                            'value' => $car['Reference_Number']
                        ]
                    ]
                ]);
                if (!$Car) {
                    echo 'Error creating car';
                    exit;
                }
            }
            $InvoiceItems[] = [
                'item_id' => $Car['item_id']
            ];
            $InvoiceTotal += $Car['rate'];
        }
        $User = Auth::user();
        $ContactId = $User->zoho_index;
        $BooksCustomer = $this->getZohoBooksCustomer($ContactId);
        if (!$BooksCustomer) {
            $BooksCustomer = $this->createZohoBooksCustomer($User->name, $User->email, $User->zoho_index);
            if (!$BooksCustomer) {
                echo 'Error creating customer';
                exit;
            }
        }
        $Invoice = $this->createInvoice([
            'customer_id' => $BooksCustomer['contact_id'],
            'line_items' => $InvoiceItems,
            'custom_fields' => [
                [
                    'label' => 'Amount Owed to Junk Car Boys:',
                    'value' => $InvoiceTotal
                ]
            ],
            'payment_options' => [
               'payment_gateways' => [
                   [
                        'gateway_name' => 'stripe'
                   ]
               ]
            ]
        ]);

        // var_dump($BooksCustomer);
        // var_dump($Invoice);


        return json_encode(array('res' => "success"));
    }


    public function saveFilter(Request $request) {
        $params = $request->input();
        $params['user_id'] = Auth::user()->id;
        $filter = Filter::create($params);
        return ['success' => true, 'filter' => $filter];
    }

    public function getFilters() {
        $filters = Filter::where('user_id', Auth::user()->id)->get();
        return ['success' => true, 'filters' => $filters];
    }

    public function deleteFilter($id) {
        $filter = Filter::where('user_id', Auth::user()->id)->find($id);
        if (!$filter) return ['error' => 'Invalid filter'];
        $filter->delete();
        return ['success' => true];
    }

    public function refreshCarData(Request $request) {
        $bulk_insert_mode = true;
        echo 'MODE : ' . ($bulk_insert_mode ? 'Bluk' : 'No Bulk') . '<br/><br/>';

        $zohoService = new ZohoSerivce();
        $page = intval($request->start_page);
        $end_page = intval($request->end_page);
        $length = 200;
        while (true) {
            $zip_codes = [];

            echo time().'---------'.$page . '<br/>';

            $records = $zohoService->getRecords('Deals', $page++, $length);


            if (!is_array($records)) {
                echo "end: resulte is invalid: not array";
                break;
            }

            echo time().'--------- get '.count($records).' records from zoho <br/>';

            $save_array = [];
            $break_time = false;

            $before_day = $request->from_days;
            if (!$before_day) $before_day = 365;

            $bulk_cars = [];
            foreach ($records as $record) {
                $car = $zohoService->saveCarToMysql($record);
                if ($car && $car->Created_Time && strtotime($car->Created_Time) < strtotime("-".$before_day." day", time())) {
                    $break_time = true;
                    echo 'end: date is before a year' . $car->Created_Time;
                    break;
                }
                if ($car) {
                    if($car->Zip_Code) {
                        $zip_codes[] = $car->Zip_Code;
                    }

                    if ($bulk_insert_mode) {
                        $bulk_cars[] = $car->toArray();
                    } else {
                        $check_count =  Car::select('id')->where('index', $car->index)->count();
                        if ($check_count == 0) $car->save();
                    }
                }
            }

            $zohoService->refreshCarLocationFromDB();

            //save bulk cars
            if ($bulk_insert_mode) {
                // check bulk existing index on db;
                $bulk_indexs = [];
                foreach ($bulk_cars as $one) {
                    $bulk_indexs[] = $one['index'];
                }
                $bulk_check_res = Car::select('index')->whereIn('index', $bulk_indexs)->get();
                $existing_indexes = [];
                foreach ($bulk_check_res as $one) {
                    $existing_indexes[] = $one->index;
                }

                $bulk_valid_cars = [];
                foreach ($bulk_cars as $car) {
                    if ( !in_array($car['index'], $existing_indexes) ) {
                        $bulk_valid_cars[] = $car;
                    }
                }
                Car::insert($bulk_valid_cars);
            }

            if ($break_time) break;
            echo time().'--------- save records to db <br/>';

            if ($records < $length) {
                echo "end: count less than 200";
                break;
            }

            if ($page > $end_page) break;
        }

    }

    public function refreshCarLocation() {
        $zohoService = new ZohoSerivce();
        $payload['id'] = "1061914000292793765";
        $payload['price'] = "250";
        $response =  $zohoService->Placebids($payload);
       // echo "<pre>"; print_r($response); echo "</pre>";
        return 'Bid placed successfully';
    }
}
