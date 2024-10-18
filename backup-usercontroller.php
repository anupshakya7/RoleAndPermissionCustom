<?php

namespace App\Http\Controllers\API;

use App\CountryRegion;
use App\CountryUser;
use App\Indicator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class UserController extends Controller
{
    //check user api_token is valid
    public function checkUser(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'api_token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $check = User::where('api_token', $request->api_token)
            ->where('status', 1)
            ->first();

        if (!is_null($check)) {
            return response()->json([
                'status' => 200,
                'message' => true

            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => false
            ]);
        }

    }

    //firstly fetching parent_id

    public function ParentData(Request $request)
    {
        // $validator = Validator::make($request->all(),[
        //     'api_token'=>'required',
        // ]);

        // if($validator->fails())
        // {
        //     return response()->json($validator->errors(),400);
        // }

        $check = User::where('api_token', $request->api_token)
            ->where('status', 1)
            ->first();

        //  if(!is_null($check)){

        $parent_id = $request->country_id ?? null;

        if ($request->filled('fields')) {
            $fields = $request->fields;
            $selectFields = explode(',', $fields);

            $countryindicatorsQuery = CountryRegion::select($selectFields)->where('parent_id', $parent_id);
            if ($request->filled('sub_region')) {
                $countryindicatorsQuery->where('id', $request->sub_region);
            }
            $countryindicators = $countryindicatorsQuery->orderBy('title', 'Asc')->get();
        } else {
            $countryindicatorsQuery = CountryRegion::where('parent_id', $parent_id);
            if ($request->filled('sub_region')) {
                $countryindicatorsQuery->where('id', $request->sub_region);
            }
            $countryindicators = $countryindicatorsQuery->get();
            $countryindicators = $countryindicators->sortBy('tilte'); // Sort ascending
            $countryindicators = $countryindicators->values()->all();


        }

        /**if($parent_id){
         $countryindicators = CountryRegion::where('parent_id',$parent_id)->orderBy('title','Asc')->get();

        }else{
         $countryindicators = CountryRegion::whereNull('parent_id')->orderBy('title','Asc')->get();
        }**/

        return response()->json([
            'success' => true,
            'data' => $countryindicators
        ], 200);

        // }else{
        //     return response()->json([
        //         'status'=>404,
        //         'message'=>false
        //     ]);
        // }
    }

    public function CountryData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'api_token' => 'required',
            'country_id' => 'required',
            'sub_region' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $check = User::where('api_token', $request->api_token)
               ->where('status', 1)
               ->first();

        if (!is_null($check)) {
            $parentId = $request->country_id;
            $select = '';
            if (isset($request->fields_id)) {
                $fields = explode(',', request->fields_id);
            }
            $userId = $check->id;
            $resultQuery = DB::table('country_region as cr')
            ->select(
                'cr.id as country_region_id',
                'cr.title',            // Title from country_region
                'cr.geo_code',         // Geo code from country_region
                'cr.parent_id as country_primary_id',        // Parent ID from country_region
                'cr.latitude',         // Latitude from country_region
                'cr.longitude',        // Longitude from country_region
                'cr.bounding_box',     // Bounding box from country_region
                'cr.geometry',         // Geometry from country_region
                'cr.population',       // Population from country_region
                'cr.area_size',        // Area size from country_region
                'cr.flag_image',
                'cu.data_year',
                'cu.country_rank',
                'cu.body as country_body',
                'i.title as indicator_title',
                'iu.body as indicator_body',
                'icr.*'
            )
            ->join('country_user as cu', 'cr.id', '=', 'cu.country_id')
            ->leftJoin('indicator_country_region as icr', 'cr.id', '=', 'icr.country_region_id')
            ->join('indicators as i', 'icr.indicator_id', '=', 'i.id')
            ->join('indicators_user as iu', 'iu.indicator_id', '=', 'icr.indicator_id')
            ->where('cr.parent_id', $parentId);
            if ($request->filled('sub_region')) {
                $resultQuery->where('cr.id', $request->sub_region);
            }
            $results = $resultQuery->where('cu.created_by', $userId)
            ->where('icr.created_by', $userId)
            ->get();
            return response()->json([
                'success' => true,
                'data' => $results
            ]);

        } else {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ]);
        }
    }

    //Indicators
    public function Indicators(Request $request)
    {
        $indicators = Indicator::where('parent_id', $request->parent_id ?? 1)->get();

        if (count($indicators) > 0) {
            return response()->json([
                'success' => true,
                'indicators' => $indicators
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Indicator Not Found'
            ]);
        }


    }

    //get all data
    public function getData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'api_token' => 'required',
            'country_id' => 'required',
            'indicator_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $check = User::where('api_token', $request->api_token)
            ->where('status', 1)
            ->first();

        if (!is_null($check)) {

            $countryindicators = CountryRegion::where('created_by', $check->id)
                                   ->where('parent_id', $request->country_id)
               ->with(['indicators' => function ($query) use ($request) {
                   $query->wherePivot('indicator_id', $request->indicator_id);
               }])
               ->get();


            /*$countryindicators = CountryRegion::where('created_by', $check->id)
    ->where('id', $request->country_id)
    ->whereHas('indicators', function ($query) use ($request) {
        $query->where('indicator_country_region.indicator_id', $request->indicator_id)
             ->where('indicator_country_region.country_region_id', $request->country_id);
    })
    ->with(['indicators' => function ($query) use ($request) {
        $query->where('indicator_country_region.indicator_id', $request->indicator_id)
             ->where('indicator_country_region.country_region_id', $request->country_id);
    }])
    ->get();



            $countryindicatorsQuery = CountryRegion::where('created_by', $check->id)
    ->where('parent_id', $request->country_id)
    ->with(['indicators' => function ($query) use ($request){
        $query->wherePivot('indicator_id', $request->indicator_id);
    }]);


        sql = $countryindicatorsQuery->toSql();
        $bindings = $countryindicatorsQuery->getBindings();

        $rawSql = vsprintf(str_replace('?', '%s', $sql), array_map(function($binding) {
            return is_numeric($binding) ? $binding : "'$binding'";
}, $bindings));*/

            return response()->json([
                    'success' => true,
                    'data' => $countryindicators
                ], 200);

        } else {
            return response()->json([
                'status' => 404,
                'message' => false
            ]);
        }
    }

    public function GeometryFile(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'api_token' => 'required',
            'country' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Check if the user exists and is active
        $check = User::where('api_token', $request->api_token)
            ->where('status', 1)
            ->first();

        if (!is_null($check)) {
            // Retrieve the country indicators
            $countryindicators = CountryRegion::where('title', $request->country)->first();

            if ($countryindicators) {
                // Remove BOM and spaces
                $cleanTitle = preg_replace('/\x{FEFF}/u', '', $countryindicators->title);
                $file_path = url('public/geometry/' . str_replace(' ', '', $cleanTitle) . '.js');

                return response()->json([
                    'success' => true,
                    'data' => $file_path,
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Country not found.',
                ]);
            }
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'User not found or inactive.',
            ]);
        }
    }


    public function GeometryData(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'api_token' => 'required',
            'country' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Check if the user exists and is active
        $check = User::where('api_token', $request->api_token)
            ->where('status', 1)
            ->first();

        if (!is_null($check)) {
            // Retrieve the country indicators
            $countryindicators = CountryRegion::where('title', $request->country)->first();

            if ($countryindicators) {
                // Remove BOM and spaces
                $cleanTitle = preg_replace('/\x{FEFF}/u', '', $countryindicators->title);
                $file_path = url('public/geometry/' . str_replace(' ', '', $cleanTitle) . '.js');
                $geoData = file_get_contents($file_path);

                return response()->json([
                    'success' => true,
                    'data' => $geoData,
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Country not found.',
                ]);
            }
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'User not found or inactive.',
            ]);
        }
    }


    //Main New Filter data
    public function filter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parent_id' => 'nullable',
            'subregion_id' => 'nullable',
            'indicator_id' => 'nullable',
            'subindicator_id' => 'nullable',
        ]);

        $check = User::where('api_token', $request->api_token)->where('status', 1)->first();

        if (!is_null($check)) {
            $userId = $check->id;

            $query = DB::table('country_region as cr')->select('cr.id as country_region_id', 'cr.title', 'cr.geo_code', 'cr.parent_id', 'cr.latitude', 'cr.longitude', 'cr.bounding_box', 'cr.geometry', 'cr.population', 'cr.area_size', 'cu.data_year', 'cu.country_rank', 'cu.body as county_body', 'i.id as indicator_id', 'i.title as indicator_title', 'i.parent_id as indicatory_parent_id', 'iu.body as indicator_body', 'icr.*')
            ->join('country_user as cu', 'cr.id', '=', 'cu.country_id')
            ->join('indicator_country_region as icr', 'cr.id', '=', 'icr.country_region_id')
            ->join('indicators as i', 'icr.indicator_id', '=', 'i.id')
            ->join('indicators_user as iu', 'iu.indicator_id', '=', 'icr.indicator_id');


            if (!isset($request->parent_id)) {
                $query->where('cr.parent_id', null);
            } else {
                $query->where('cr.parent_id', $request->parent_id);

                if ($request->filled('subregion_id')) {
                    $query->where('cr.id', $request->subregion_id);
                }
            }

            if ($request->filled('indicator_id')) {
                $query->where('i.parent_id', $request->indicator_id);

                if ($request->filled('subindicator_id')) {
                    $query->where('i.id', $request->subindicator_id);
                }
            }

            if (!empty($userId)) {
                $query->where('cu.created_by', $userId)
                ->where('icr.created_by', $userId);
            }

            $result = $query->distinct()->get();

            return response()->json([
                'success' => true,
                'count' => count($result),
                'data' => $result
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found'
            ]);
        }
    }




    //Used API Start
    //Individual Filter
    public function individualFilter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'region_id' => 'required',
            'country_id' => 'nullable',
            'sub_country_id' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 404);
        }

        //Individual Start
        $country_region_query = DB::table('country_region as cr')->select('cr.id', 'cr.title', 'cr.geo_code', 'cr.parent_id', 'cr.latitude', 'cr.longitude', 'cr.bounding_box', 'cr.geometry', 'cr.population', 'cr.area_size', 'cr.level', 'cu.country_score', 'cu.data_year', 'cu.country_rank', 'cu.body')->join('country_user as cu', 'cr.id', '=', 'cu.country_id');


        if ($request->filled(['region_id','country_id','sub_country_id'])) {
            $country_region_query->where('cr.level', '=', 2)->where('cr.parent_id', '=', $request->country_id)->where('cr.id', '=', $request->sub_country_id);

        } elseif ($request->filled(['region_id','country_id'])) {
            $country_region_query->where('cr.level', '=', 1)->where('cr.parent_id', '=', $request->region_id)->where('cr.id', '=', $request->country_id);
        } elseif ($request->filled('region_id')) {
            $country_region_query->where('cr.level', '=', 0)->where('cr.id', '=', $request->region_id);
        }

        $country_region_details = $country_region_query->distinct()->get();
        //Individual end

        return response()->json([
            'success' => true,
            'data' => $country_region_details
        ]);
    }
    //Used API End

    //Geometry Filter
    public function geometryFilter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'region_id' => 'required',
            'country_id' => 'nullable',
            'sub_country_id' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 404);
        }

        //Individual Start
        $country_region_geo_query = CountryRegion::query();
        $country_region_geo_query->select('latitude', 'longitude', 'geometry');

        if ($request->filled(['region_id','country_id','sub_country_id'])) {
            $country_region_geo_query->where('level', '=', 2)->where('parent_id', '=', $request->country_id)->where('id', '=', $request->sub_country_id);

        } elseif ($request->filled(['region_id','country_id'])) {
            $country_region_geo_query->where('level', '=', 1)->where('parent_id', '=', $request->region_id)->where('id', '=', $request->country_id);
        } elseif ($request->filled('region_id')) {
            $country_region_geo_query->where('level', '=', 0)->where('id', '=', $request->region_id);
        }

        $country_region_geo_details = $country_region_geo_query->distinct()->get();
        //Individual end

        return response()->json([
            'success' => true,
            'data' => $country_region_geo_details
        ]);
    }

    //Countries List Filter
    public function countryList(Request $request)
    {
        die;
        $validator = Validator::make($request->all(), [
            'region_id' => 'nullable',
            'country_id' => 'nullable',
            'sub_country_id' => 'nullable',
            'countriesOrder' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 404);
        }

        // Country List Start
        if ($request->filled('year')) {
            $countryList = CountryRegion::query()
                ->select(
                    'country_region.id as c_id',
                    'country_region.title',
                    'country_region.latitude',
                    'country_region.longitude',
                    'country_region.geometry',
                    'country_region.level',
                    DB::raw('COALESCE(country_user.country_score, 0) as country_score'),
                    'country_user.body as description'
                )->leftJoin('country_user', function ($join) use ($request) {
                    $join->on('country_region.id', '=', 'country_user.country_id')
                         ->where('country_user.data_year', $request->year);
                });

            $country =  CountryRegion::query()
            ->select(
                'country_region.id as c_id',
                'country_region.title',
                'country_region.latitude',
                'country_region.longitude',
                'country_region.geometry',
                'country_region.level',
                DB::raw('COALESCE(country_user.country_score, 0) as country_score'),
                'country_user.body as description'
            )
            ->leftJoin('country_user', function ($join) use ($request) {
                $join->on('country_region.id', '=', 'country_user.country_id')
                     ->where('country_user.data_year', $request->year);
            });
        } else {

            $countryList = CountryRegion::query()
        ->select(
            'country_region.id as c_id',
            'country_region.parent_id',
            'country_region.title',
            'country_region.latitude',
            'country_region.longitude',
            'country_region.geometry',
            'country_region.level',
            DB::raw('COALESCE(country_user.country_score, 0) as country_score'),
            'country_user.body as description'
        )->leftJoin('country_user', function ($join) {
            $join->on('country_region.id', '=', 'country_user.country_id')
                 ->where('country_user.data_year', function ($query) {
                     $query->select(DB::raw('MAX(data_year)'))
                           ->from('country_user as cu')
                           ->whereColumn('cu.country_id', 'country_user.country_id');
                 });
        });

            $country =  CountryRegion::query()
            ->select(
                'country_region.id as c_id',
                'country_region.title',
                'country_region.latitude',
                'country_region.longitude',
                'country_region.geometry',
                'country_region.level',
                DB::raw('COALESCE(country_user.country_score, 0) as country_score'),
                'country_user.body as description'
            )->leftJoin('country_user', function ($join) {
                $join->on('country_region.id', '=', 'country_user.country_id')
                     ->where('country_user.data_year', function ($query) {
                         $query->select(DB::raw('MAX(data_year)'))
                               ->from('country_user as cu')
                               ->whereColumn('cu.country_id', 'country_user.country_id');
                     });
            });

        }

        if ($request->filled(['region_id', 'country_id', 'sub_country_id'])) {
            $country = $country->where('country_region.id', $request->sub_country_id)->first();
            $countryList->where('level', 2)->where('parent_id', $request->sub_country_id);
        } elseif ($request->filled(['region_id', 'country_id'])) {
            $country = $country->where('country_region.id', $request->country_id)->first();
            $countryList->where('level', 2)->where('parent_id', $request->country_id);
        } elseif ($request->filled('region_id')) {
            $country = $country->where('country_region.id', $request->region_id)->first();
            $countryList->where('level', 1)->where('parent_id', $request->region_id);
        } else {
            $country = null;
            $countryList->where('level', 1);
        }

        $totalScore = 0;
        $countriesList = $countryList->orderBy('country_score', $request->countriesOrder)->get();

        $countriesList_data = $countriesList->map(function ($data) use (&$totalScore) {
            $country_score = $data->country_score;

            if ($data->level == 1) {
                $countryrank = CountryUser::query()
                    ->select('country_user.country_score')
                    ->join('country_region', 'country_user.country_id', '=', 'country_region.id')
                    ->where('country_region.parent_id', $data->c_id)
                    ->whereNotNull('country_user.country_score')
                    ->first();

                $country_score = $countryrank ? $countryrank->country_score : null;
            }

            if ($country_score !== null) {
                $totalScore += $country_score;
            }

            return [
                'title' => $data->title,
                'latitude' => $data->latitude,
                'longitude' => $data->longitude,
                'geometry' => $data->geometry,
                'country_score' => number_format($country_score, 3),
                'description' => $data->description,
            ];
        });

        // Country List end

        return response()->json([
            'success' => true,
            'country' => $country,
            'data' => $countriesList_data,
            'overall_score' => count($countriesList) > 0 ? number_format($totalScore / count($countriesList), 2) : 0,
            'score_range' => count($countriesList) > 0 ? $countriesList->max('country_score') . ' - ' . $countriesList->min('country_score') : '0 - 0'
        ]);
    }


    //Country List New API
    //Country List New API


    //Indicator filter
    public function indicatorFilter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => 'nullable',
            'indicator_id' => 'nullable',
            'sub_indicator_id' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 404);
        }


        //Country List Start
        //Individual Start
        $country_region_indicator_query = DB::table('indicators as in')->select('in.id', 'in.title', 'in.level', 'in.parent_id', 'iu.body', 'icr.country_region_id', 'icr.value', 'icr.raw', 'icr.units', 'icr.source', 'icr.year')->join('indicators_user as iu', 'in.id', '=', 'iu.indicator_id')->join('indicator_country_region as icr', 'in.id', '=', 'icr.indicator_id');


        /**if($request->filled(['country_id','indicator_id','sub_indicator_id'])){
            return 'a';
            $country_region_indicator_query->where('in.level',2)->where('in.parent_id',$request->indicator_id)->where('in.id',$request->sub_indicator_id)->where('icr.country_region_id',$request->country_id);
        }
        elseif($request->filled(['country_id','indicator_id'])){
            return 'b';
            $country_region_indicator_query->where('in.level',1)->where('in.id',$request->indicator_id)->where('icr.country_region_id',$request->country_id);
        }
        else{
            return 'c';
            $country_region_indicator_query->where('in.level',0)->where('icr.country_region_id',$request->country_id);
        }**/

        /**if($request->filled('year')){
            $country_region_indicator_query->where('icr.year',$request->year);

        }**/

        $country_region_indicator = $country_region_indicator_query->distinct()->get();

        return $country_region_indicator;
        //Country List end

        return response()->json([
            'success' => true,
            'country' => $country,
            'data' => $countriesList,
            'overall_score' => number_format($countriesList->sum('country_score') / count($countriesList), 2),
            'score_range' => $countriesList->max('country_score').' - '.$countriesList->min('country_score')

        ]);
    }

    public function getYears(Request $request)
    {
        // Optionally validate any request parameters if needed
        $validator = Validator::make($request->all(), [
            'country_id' => 'nullable|integer', // Example validation if you want to filter by country
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400); // Bad Request
        }

        // Retrieve distinct years from CountryUser
        $distinctYears = CountryUser::select('data_year as year') // Adjust to your actual year field
            ->distinct()
            ->when($request->filled('country_id'), function ($query) use ($request) {
                return $query->where('country_id', $request->country_id);
            })
            ->orderBy('year') // Optional: order by year
            ->pluck('year'); // Use pluck to get a simple array

        return response()->json([
            'success' => true,
            'data' => $distinctYears,
        ]);
    }

    public function getAverageDomain(Request $request)
    {
        // Validate request parameters
        $request->validate([
            'year' => 'required|integer',
            'country_id' => 'required|integer',
        ]);

        $year = $request->input('year');
        $countryId = $request->input('country_id');

        // Call the stored procedure
        $results = DB::select('CALL GetAverageIndicatorValues(?, ?)', [$year, $countryId]);

        // Return the results as JSON
        return response()->json([
        'success' => true,
        'data' => $results,
    ]);
    }

    public function getAverageIndividualIndicator(Request $request)
    {
        // Validate request parameters
        $request->validate([
            'year' => 'required|integer',
            'country_id' => 'required|integer',
            'indicator_id' => 'required|integer',
        ]);

        $year = $request->input('year');
        $countryId = $request->input('country_id');
        $indicator_id = $request->input('indicator_id');

        // Call the stored procedure
        $results = DB::select('CALL GetIndividualIndicatorValues(?, ?, ?)', [$year, $countryId, $indicator_id]);

        // Return the results as JSON
        return response()->json([
        'success' => true,
        'data' => $results,
    ]);
    }



    //All the used New API Start
    //Maps and Indicator
    public function mapsData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'region_id' => 'nullable|integer',
            'country_id' => 'nullable|integer',
            'sub_country_id' => 'nullable|integer',
            'indicator_id' => 'nullable|integer',
            'sub_indicator_id' => 'nullable|integer',
            'year' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $mapsQuery = DB::table('country_region as cr')->join('country_user as cu', 'cr.id', '=', 'cu.country_id')->select('cr.title', 'cr.geo_code', 'cr.latitude', 'cr.longitude', 'cr.geometry', 'cu.data_year', 'cu.country_rank', 'cu.country_score', 'cu.body', 'cu.country_color', 'cu.country_category');

        //Set Year if Choose
        $year = $request->year ?? 2023;

        if ($request->filled(['region_id','country_id','sub_country_id'])) {
            $mapsQuery->where('cr.level', '=', 2)->where('cu.data_year', '=', $year)->where('cr.parent_id', '=', $request->country_id)->where('cr.id', '=', $request->sub_country_id);
        } elseif ($request->filled(['region_id','country_id'])) {
            $mapsQuery->where('cr.level', '=', 2)->where('cu.data_year', '=', $year)->where('cr.parent_id', '=', $request->country_id);
        } elseif ($request->filled('region_id')) {
            $mapsQuery->where('cr.level', '=', 1)->where('cu.data_year', '=', $year)->where('cr.parent_id', '=', $request->region_id);
        } else {
            $mapsQuery->where('cr.level', '=', 1)->where('cu.data_year', '=', $year);
        }

        //
        $results = $mapsQuery->distinct()->get();
        foreach ($results as $result) {
            $result->geometry = json_decode($result->geometry);
        }

        return response()->json([
            'status' => true,
            'maps' => $results
        ]);
    }

    //Maps Main API

    public function demoMapData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'region_id' => 'nullable|integer',
            'country_id' => 'nullable|integer',
            'sub_country_id' => 'nullable|integer',
            'indicator_id' => 'nullable|integer',
            'sub_indicator_id' => 'nullable|integer',
            'year' => 'nullable|integer',
            'order' => 'nullable|string|min:3'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        // Set Year if not provided
        $year = $request->year ?? 2023;
        //Parent Country if there is any
        $parentQuery = DB::table('country_region as cr')
            ->leftJoin('country_user as cu', function ($join) use ($year) {
                $join->on('cr.id', '=', 'cu.country_id')
                     ->where('cu.data_year', '=', $year); // Apply year filter here
            })
            ->select(
                'cr.id',
                'cr.parent_id',
                'cr.level',
                'cr.title',
                'cr.geo_code',
                'cr.latitude',
                'cr.longitude',
                'cr.geometry',
                'cu.data_year',
                'cu.country_rank',
                'cu.country_score',
                'cu.body',
                'cu.country_color',
                'cu.country_category'
            );

        // Initialize maps query
        $mapsQuery = DB::table('country_region as cr')
            ->leftJoin('country_user as cu', function ($join) use ($year) {
                $join->on('cr.id', '=', 'cu.country_id')
                     ->where('cu.data_year', '=', $year); // Apply year filter here
            })
            ->select(
                'cr.id',
                'cr.parent_id',
                'cr.level',
                'cr.title',
                'cr.geo_code',
                'cr.latitude',
                'cr.longitude',
                'cr.geometry',
                'cu.data_year',
                'cu.country_rank',
                'cu.country_score',
                'cu.body',
                'cu.country_color',
                'cu.country_category'
            );



        if ($request->filled('indicator_id')) {
            $mapsQuery->leftJoin('indicator_country_region as icr', 'cr.id', '=', 'icr.country_region_id')
            ->selectRaw('cr.id, cr.parent_id, cr.level, cr.title, cr.geo_code, cr.latitude, cr.longitude, cr.geometry, 
                      icr.value as country_score, icr.in_country_rank, icr.statements, 
                      icr.description, icr.year, icr.admin_col as country_color, cu.country_category');



            // Adjust the query based on request parameters
            if ($request->filled('region_id')) {
                $childCountryLists = CountryRegion::query()->select('id', 'title');

                // Handle different request parameter combinations
                if ($request->filled(['region_id', 'country_id', 'sub_country_id', 'indicator_id', 'sub_indicator_id'])) {
                    $mapsQuery->where('cr.level', '=', 2)
                              ->where('icr.indicator_id', $request->sub_indicator_id)
                              ->where('icr.country_region_id', $request->sub_country_id);
                    $parent = $parentQuery->where('cr.id', $request->country_id)->first();

                } elseif ($request->filled(['region_id', 'country_id', 'indicator_id', 'sub_indicator_id'])) {
                    $childLists = $childCountryLists->where('parent_id', '=', $request->country_id)->get();
                    $level = 2;
                    $indicator_value = $request->sub_indicator_id;
                    $parent = $parentQuery->where('cr.id', $request->country_id)->first();

                } elseif ($request->filled(['region_id', 'indicator_id', 'sub_indicator_id'])) {
                    $childLists = $childCountryLists->where('parent_id', '=', $request->region_id)->first();
                    $level = 1;
                    $indicator_value = $request->sub_indicator_id;
                    $parent = $parentQuery->where('cr.id', $request->region_id)->first();

                } elseif ($request->filled(['region_id', 'country_id', 'sub_country_id', 'indicator_id'])) {
                    $mapsQuery->where('cr.level', '=', 2)
                              ->where('icr.indicator_id', $request->indicator_id)
                              ->where('icr.country_region_id', $request->sub_country_id);
                    $parent = $parentQuery->where('cr.id', $request->country_id)->first();

                } elseif ($request->filled(['region_id', 'country_id', 'indicator_id'])) {
                    $childLists = $childCountryLists->where('parent_id', '=', $request->country_id)->get();
                    $level = 2;
                    $indicator_value = $request->indicator_id;
                    $parent = $parentQuery->where('cr.id', $request->country_id)->first();

                } elseif ($request->filled(['region_id', 'indicator_id'])) {
                    $childLists = $childCountryLists->where('parent_id', '=', $request->region_id)->get();
                    $level = 1;
                    $indicator_value = $request->indicator_id;
                    $parent = $parentQuery->where('cr.id', $request->region_id)->first();

                } else {
                    $mapsQuery->where('cr.level', '=', 1);
                }

                // Fetch child indicators if any
                if (!empty($childLists)) {
                    foreach ($childLists as $childList) {
                        $currentQuery = clone $mapsQuery;
                        $childIndicator = $currentQuery->where('cr.level', '=', $level)
                            ->where('icr.indicator_id', $indicator_value)
                            ->where('icr.country_region_id', $childList->id)
                            ->first();

                        if (!empty($childIndicator)) {
                            $childIndicators[] = $childIndicator;
                        }
                    }
                }
            } else {
                $parent = null;

                if ($request->filled(['indicator_id', 'sub_indicator_id'])) {
                    $mapsQuery->where('cr.level', '=', 1)
                              ->where('icr.indicator_id', $request->sub_indicator_id);
                } elseif ($request->filled('indicator_id')) {
                    $mapsQuery->where('cr.level', '=', 1)
                              ->where('icr.indicator_id', $request->indicator_id);
                } else {
                    $mapsQuery->where('cr.level', '=', 1);
                }
            }
        } else {
            if ($request->filled(['region_id', 'country_id', 'sub_country_id'])) {
                $mapsQuery->where('cr.level', '=', 2)
                          ->where('cr.parent_id', '=', $request->country_id)
                          ->where('cr.id', '=', $request->sub_country_id);
                $parent = $parentQuery->where('cr.id', $request->country_id)->first();

            } elseif ($request->filled(['region_id', 'country_id'])) {
                $mapsQuery->where('cr.level', '=', 2)
                          ->where('cr.parent_id', '=', $request->country_id);
                $parent = $parentQuery->where('cr.id', $request->country_id)->first();

            } elseif ($request->filled('region_id')) {
                $mapsQuery->where('cr.level', '=', 1)
                          ->where('cr.parent_id', '=', $request->region_id);
                $parent = $parentQuery->where('cr.id', $request->region_id)->first();

            } else {
                $mapsQuery->where('cr.level', '=', 1);
                $parent = null;

            }
        }

        // Order Lists
        $order = $request->order ?? "ASC";

        // Result For Maps
        if (isset($childIndicators)) {
            usort($childIndicators, function ($a, $b) use ($order) {
                return $order === "ASC" ? strcmp($a->country_score, $b->country_score) : strcmp($b->country_score, $a->country_score);
            });
            $results = $childIndicators;
        } else {
            $orderColumn = $request->filled('indicator_id') ? 'icr.value' : 'COALESCE(cu.country_score, 0)';

            $results = $mapsQuery->distinct()->groupBy('cr.id')
                ->get();
            if ($order === 'ASC') {
                $results = $results->sortBy($orderColumn); // Sort ascending

            } else {
                $results = $results->sortByDesc($orderColumn); // Sort descending

            }
            $results = $results->values()->all();
        }

        foreach ($results as $result) {
            // If geometry is JSON encoded, decode it
            if (isset($result->geometry)) {
                $result->geometry = json_decode($result->geometry);
            }
        }

        return response()->json([
            'status' => true,
            'parent' => $parent, // Adjust if parent data needs to be included
            'count' => count($results),
            'data' => $results
        ]);
    }

    //Indicator Score API
    public function indicatorScore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'region_id' => 'nullable|integer',
            'country_id' => 'nullable|integer',
            'sub_country_id' => 'nullable|integer',
            'indicator' => 'nullable|integer',
            'sub_indicator' => 'nullable|integer',
            'year' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        //Set Year if Choose
        $year = $request->year ?? 2023;

        $parentQuery = DB::table('country_region')->select('id', 'title', 'parent_id');

        $indicatorQuery = DB::table('country_region as cr')->join('indicator_country_region as icr', 'cr.id', '=', 'icr.country_region_id')->select('cr.id', 'cr.title', 'cr.geo_code', 'cr.parent_id', 'cr.latitude', 'cr.longitude', 'cr.geometry', 'icr.value as country_score', 'icr.indicator_id', 'icr.in_country_rank', 'icr.statements', 'icr.description', 'icr.year');

        if ($request->filled(['region_id','country_id','sub_country_id'])) {
            $indicatorQuery->where('cr.level', '=', 2)->where('cr.parent_id', '=', $request->country_id)->where('cr.id', '=', $request->sub_country_id);
            $parent = $parentQuery->where('id', $request->country_id)->get();
        } elseif ($request->filled(['region_id','country_id'])) {
            $indicatorQuery->where('cr.level', '=', 2)->where('cr.parent_id', '=', $request->country_id);
            $parent = $parentQuery->where('id', $request->country_id)->get();
        } elseif ($request->filled('region_id')) {
            $indicatorQuery->where('cr.level', '=', 1)->where('cr.parent_id', '=', $request->region_id);
            $parent = $parentQuery->where('id', $request->region_id)->get();
        } else {
            $indicatorQuery->where('icr.year', $year);
            $parent = $parentQuery->where('id', $request->region_id)->get();
        }

        //Indicator Lists
        if ($request->filled('indicator')) {
            $indicators = Indicator::where('level', 2)->where('parent_id', $request->indicator)->get();
        } else {
            $indicators = Indicator::where('level', 1)->get();
        }


        $indicatorsScore = [];

        foreach ($indicators as $indicator) {
            $currentQuery = clone $indicatorQuery;

            $indicatorSum = $currentQuery->where('icr.indicator_id', $indicator->id)->sum('value');

            $indicatorCount = $currentQuery->where('icr.indicator_id', $indicator->id)->count();

            if ($indicatorCount > 0) {
                $indicatorScore = $indicatorSum / $indicatorCount;

                // Normalize score to range from 0 to 5
                $normalizedScore = min(max($indicatorScore, 0), 5); // Ensures score is between 0 and 5
            } else {
                $normalizedScore = 0;
            }


            $indicatorsScore[] = [
                'id' => $indicator->id,
                'name' => $indicator->title,
                'score' => number_format($normalizedScore, 2)
            ];
        }

        return response()->json([
            'success' => true,
            'parent' => $parent,
            'data' => $indicatorsScore
        ]);
    }

    //Country List
    public function countryListNew(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'region_id' => 'nullable|integer',
            'country_id' => 'nullable|integer',
            'sub_country_id' => 'nullable|integer',
            'year' => 'nullable|integer',
            'indicator_id' => 'nullable|integer',
            'sub_indicator_id' => 'nullable|integer',
            'order' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        //Set Year if Choose
        $year = $request->year ?? 2023;

        //Parent Country if there is any
        $parentQuery = DB::table('country_region')->select('id', 'title');

        if ($request->filled('indicator_id')) {
            $mapsQuery = DB::table('country_region as cr')->leftjoin('indicator_country_region as icr', function ($join) use ($year) {
                $join->on('cr.id', '=', 'icr.country_region_id')->where('icr.year', '=', $year);
            });
            if ($request->filled(['region_id','country_id','indicator_id'])) {
                $mapsQuery->join('indicators as i', 'icr.indicator_id', '=', 'i.id');
            }

            $mapsQuery->select('cr.id', 'cr.title', 'icr.value as country_score');

            if ($request->filled('region_id')) {
                //Child Country List if there is Any
                $childCountryLists = CountryRegion::query();
                $childCountryLists->select('id', 'title');
                if ($request->filled(['region_id','country_id','sub_country_id','indicator_id','sub_indicator_id'])) {
                    $mapsQuery->where('icr.indicator_id', $request->sub_indicator_id)->where('icr.country_region_id', $request->sub_country_id);
                    $parent = $parentQuery->where('id', $request->country_id)->get();
                } elseif ($request->filled(['region_id','country_id','indicator_id','sub_indicator_id'])) {
                    $childIndicators = [];
                    $childLists = $childCountryLists->where('parent_id', '=', $request->country_id)->get();
                    $level = 2;
                    $indicator_value = $request->sub_indicator_id;
                    $parent = $parentQuery->where('id', $request->country_id)->get();
                } elseif ($request->filled(['region_id','indicator_id','sub_indicator_id'])) {
                    $childIndicators = [];
                    $childLists = $childCountryLists->where('parent_id', '=', $request->region_id)->get();
                    $level = 1;
                    $indicator_value = $request->sub_indicator_id;
                    $parent = $parentQuery->where('id', $request->region_id)->get();
                } elseif ($request->filled(['region_id','country_id','sub_country_id','indicator_id'])) {
                    $mapsQuery->where('icr.indicator_id', $request->indicator_id)->where('icr.country_region_id', $request->sub_country_id);
                    $parent = $parentQuery->where('id', $request->country_id)->get();
                } elseif ($request->filled(['region_id','country_id','indicator_id'])) {
                    $childIndicators = [];
                    $childLists = $childCountryLists->where('parent_id', '=', $request->country_id)->get();
                    $level = 2;
                    $indicator_value = $request->indicator_id;
                    $parent = $parentQuery->where('id', $request->country_id)->get();
                } elseif ($request->filled(['region_id','indicator_id'])) {
                    $childIndicators = [];
                    $childLists = $childCountryLists->where('parent_id', '=', $request->region_id)->get();
                    $level = 1;
                    $indicator_value = $request->indicator_id;
                    $parent = $parentQuery->where('id', $request->region_id)->get();
                } else {
                    $mapQuery->where('icr.year', '=', $year);
                }

                if (!empty($childLists)) {
                    foreach ($childLists as $childList) {
                        $currentQuery = clone $mapsQuery;
                        $childIndicator = $currentQuery->where('cr.level', '=', $level)->where('icr.indicator_id', $indicator_value)->where('icr.country_region_id', $childList->id)->where('icr.year', '=', $year)->first();

                        if (!empty($childIndicator)) {
                            $childIndicators[] = $childIndicator;
                        }
                    }
                }
            } else {
                $mapsQuery->where('cr.level', '=', 1)->where('icr.year', '=', $year);
                if ($request->filled(['indicator_id','sub_indicator_id'])) {
                    $mapsQuery->where('icr.indicator_id', $request->sub_indicator_id);
                    $parent = null;
                } elseif ($request->filled('indicator_id')) {

                    $mapsQuery->where('icr.indicator_id', $request->indicator_id);
                    $parent = null;
                }
            }
        } else {
            $mapsQuery = DB::table('country_region as cr')
            ->leftJoin('country_user as cu', function ($join) use ($year) {
                $join->on('cr.id', '=', 'cu.country_id')
                     ->where('cu.data_year', '=', $year); // Apply year filter here
            })
            ->select('cr.id', 'cr.title', 'cu.country_score');


            if ($request->filled(['region_id','country_id','sub_country_id'])) {
                $mapsQuery->where('cr.level', '=', 2)->where('cr.parent_id', '=', $request->country_id)->where('cr.id', '=', $request->sub_country_id);
                $parent = $parentQuery->where('id', $request->country_id)->get();
            } elseif ($request->filled(['region_id','country_id'])) {
                $mapsQuery->where('cr.level', '=', 2)->where('cr.parent_id', '=', $request->country_id);
                $parent = $parentQuery->where('id', $request->country_id)->get();
            } elseif ($request->filled('region_id')) {
                $mapsQuery->where('cr.level', '=', 1)->where('cr.parent_id', '=', $request->region_id);
                $parent = $parentQuery->where('id', $request->region_id)->get();
            } else {
                $mapsQuery->where('cr.level', '=', 1);
                $parent = null;
            }
        }

        //Order Lists
        $order = $request->order ?? "ASC";

        //Result For Country Score Lists
        if (isset($childIndicators)) {
            if ($order == "ASC") {
                usort($childIndicators, function ($a, $b) {
                    return strcmp($a->country_score, $b->country_score);
                });
                $results = $childIndicators;
            } else {
                usort($childIndicators, function ($a, $b) {
                    return strcmp($b->country_score, $a->country_score);
                });
                $results = $childIndicators;
            }
        } else {
            if ($request->filled('indicator_id')) {
                $results = $mapsQuery->orderBy('icr.value', $order)->distinct()->get();
            } else {
                $results = $mapsQuery->orderBy('cu.country_score', $order)->groupBy('cr.id')->get();
            }
        }

        /**foreach ($results as $result) {
            $result->geometry = json_decode($result->geometry);
        }**/

        return response()->json([
            'status' => true,
            'count' => count($results),
            'parent' => $parent,
            'data' => $results
        ]);
    }
    //All the used New API End

    //Train Graph API
    public function trainGraph(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'region_id' => 'nullable|integer',
            'country_id' => 'nullable|integer',
            'sub_country_id' => 'nullable|integer',
            'indicator_id' => 'nullable|integer',
            'sub_indicator_id' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $year = $request->year ?? 2023;
        $datewise_score = [];

        if ($request->filled('indicator_id')) {
            $parentQuery = DB::table('country_region as cr')
                ->leftJoin('indicator_country_region as icr', function ($join) use ($year) {
                    $join->on('cr.id', '=', 'icr.country_region_id');
                });
            $parentQuery->select('cr.id', 'cr.title', 'icr.year as data_year', 'icr.value');

            $includedDateQuery = DB::table('country_region as cr')
                ->leftJoin('indicator_country_region as icr', function ($join) use ($year) {
                    $join->on('cr.id', '=', 'icr.country_region_id');
                })->select('icr.year as data_year')->whereNotNull('icr.year');

            $childCountryQuery = CountryRegion::select('id', 'title');

            if ($request->filled('region_id')) {
                if ($request->filled(['region_id','country_id','sub_country_id','indicator_id','sub_indicator_id'])) {
                    $includedDates = $includedDateQuery->where('cr.level', '=', 2)->where('icr.indicator_id', '=', $request->sub_indicator_id)->where('icr.country_region_id', '=', $request->sub_country_id)->orderBy('icr.year', 'ASC')->distinct()->get();

                    foreach ($includedDates as $includedDate) {
                        $currentQuery = clone $parentQuery;
                        $currentQuery->where('cr.level', '=', 2)->where('icr.indicator_id', '=', $request->sub_indicator_id)->where('icr.country_region_id', '=', $request->sub_country_id)->where('icr.year', '=', $includedDate->data_year);

                        $resultSum = $currentQuery->sum('icr.value');
                        $resultCount = $currentQuery->count();

                        if ($resultCount > 0) {
                            $averageScore = $resultSum / $resultCount;
                        } else {
                            $averageScore = 0;
                        }

                        $datewise_score[] = ['data_year' => $includedDate->data_year,'country_score' => $averageScore];
                    }
                } elseif ($request->filled(['region_id','country_id','indicator_id','sub_indicator_id'])) {
                    $childCountries = $childCountryQuery->where('parent_id', $request->country_id)->get();

                    $dateScores = [];
                    $alreadyExistYear = [];
                    foreach ($childCountries as $childCountry) {
                        $currentIncludedDates = clone $includedDateQuery;
                        $includedDates = $currentIncludedDates->where('icr.indicator_id', '=', $request->sub_indicator_id)->where('icr.country_region_id', '=', $childCountry->id)->orderBy('icr.year', 'ASC')->distinct()->get();

                        if (!$includedDates->isEmpty()) {
                            foreach ($includedDates as $includedDate) {
                                if (!in_array($includedDate->data_year, $alreadyExistYear)) {
                                    $dateScores[] = $includedDate;
                                    $alreadyExistYear[] = $includedDate->data_year;
                                }
                            }
                        }
                    }

                    foreach ($dateScores as $dateScore) {
                        $totalSum = 0;
                        $totalCount = 0;
                        foreach ($childCountries as $childCountry) {
                            $currentQuerySecond = clone $parentQuery;
                            $currentQuerySecond->where('cr.level', '=', 2)->where('icr.country_region_id', '=', $childCountry->id)->where('icr.indicator_id', '=', $request->sub_indicator_id)->where('icr.year', '=', $dateScore->data_year);

                            // Calculate the sum and count for each country for this year
                            $resultSum = $currentQuerySecond->sum('icr.value');
                            $resultCount = $currentQuerySecond->count();

                            // Add the current country's sum and count to the totals
                            $totalSum += $resultSum;
                            $totalCount += $resultCount;
                        }

                        if ($totalCount > 0) {
                            $averageScore = $totalSum / $totalCount;
                        } else {
                            $averageScore = 0;
                        }

                        $datewise_score[] = ['data_year' => $dateScore->data_year,'country_score' => $averageScore];
                    }
                } elseif ($request->filled(['region_id','indicator_id','sub_indicator_id'])) {
                    $childCountries = $childCountryQuery->where('parent_id', $request->region_id)->get();

                    $dateScores = [];
                    $alreadyExistYear = [];
                    foreach ($childCountries as $childCountry) {
                        $currentIncludedDates = clone $includedDateQuery;
                        $includedDates = $currentIncludedDates->where('icr.indicator_id', '=', $request->sub_indicator_id)->where('icr.country_region_id', '=', $childCountry->id)->orderBy('icr.year', 'ASC')->distinct()->get();

                        if (!$includedDates->isEmpty()) {
                            foreach ($includedDates as $includedDate) {
                                if (!in_array($includedDate->data_year, $alreadyExistYear)) {
                                    $dateScores[] = $includedDate;
                                    $alreadyExistYear[] = $includedDate->data_year;
                                }
                            }
                        }
                    }

                    foreach ($dateScores as $dateScore) {
                        $totalSum = 0;
                        $totalCount = 0;
                        foreach ($childCountries as $childCountry) {
                            $currentQuerySecond = clone $parentQuery;
                            $currentQuerySecond->where('cr.level', '=', 1)->where('icr.country_region_id', '=', $childCountry->id)->where('icr.indicator_id', '=', $request->sub_indicator_id)->where('icr.year', '=', $dateScore->data_year);

                            // Calculate the sum and count for each country for this year
                            $resultSum = $currentQuerySecond->sum('icr.value');
                            $resultCount = $currentQuerySecond->count();

                            // Add the current country's sum and count to the totals
                            $totalSum += $resultSum;
                            $totalCount += $resultCount;
                        }

                        if ($totalCount > 0) {
                            $averageScore = $totalSum / $totalCount;
                        } else {
                            $averageScore = 0;
                        }

                        $datewise_score[] = ['data_year' => $dateScore->data_year,'country_score' => $averageScore];
                    }
                } elseif ($request->filled(['region_id','country_id','sub_country_id','indicator_id'])) {
                    $includedDates = $includedDateQuery->where('cr.level', '=', 2)->where('icr.indicator_id', '=', $request->indicator_id)->where('icr.country_region_id', '=', $request->sub_country_id)->orderBy('icr.year', 'ASC')->distinct()->get();

                    foreach ($includedDates as $includedDate) {
                        $currentQuery = clone $parentQuery;
                        $currentQuery->where('cr.level', '=', 2)->where('icr.indicator_id', '=', $request->indicator_id)->where('icr.country_region_id', '=', $request->sub_country_id)->where('icr.year', '=', $includedDate->data_year);

                        $resultSum = $currentQuery->sum('icr.value');
                        $resultCount = $currentQuery->count();

                        if ($resultCount > 0) {
                            $averageScore = $resultSum / $resultCount;
                        } else {
                            $averageScore = 0;
                        }

                        $datewise_score[] = ['data_year' => $includedDate->data_year,'country_score' => $averageScore];
                    }
                } elseif ($request->filled(['region_id','country_id','indicator_id'])) {
                    $childCountries = $childCountryQuery->where('parent_id', $request->country_id)->get();

                    $dateScores = [];
                    $alreadyExistYear = [];
                    foreach ($childCountries as $childCountry) {
                        $currentIncludedDates = clone $includedDateQuery;
                        $includedDates = $currentIncludedDates->where('icr.indicator_id', '=', $request->indicator_id)->where('icr.country_region_id', '=', $childCountry->id)->orderBy('icr.year', 'ASC')->distinct()->get();

                        if (!$includedDates->isEmpty()) {
                            foreach ($includedDates as $includedDate) {
                                if (!in_array($includedDate->data_year, $alreadyExistYear)) {
                                    $dateScores[] = $includedDate;
                                    $alreadyExistYear[] = $includedDate->data_year;
                                }
                            }
                        }
                    }

                    foreach ($dateScores as $dateScore) {
                        $totalSum = 0;
                        $totalCount = 0;
                        foreach ($childCountries as $childCountry) {
                            $currentQuerySecond = clone $parentQuery;
                            $currentQuerySecond->where('cr.level', '=', 2)->where('icr.country_region_id', '=', $childCountry->id)->where('icr.indicator_id', '=', $request->indicator_id)->where('icr.year', '=', $dateScore->data_year);

                            // Calculate the sum and count for each country for this year
                            $resultSum = $currentQuerySecond->sum('icr.value');
                            $resultCount = $currentQuerySecond->count();

                            // Add the current country's sum and count to the totals
                            $totalSum += $resultSum;
                            $totalCount += $resultCount;
                        }

                        if ($totalCount > 0) {
                            $averageScore = $totalSum / $totalCount;
                        } else {
                            $averageScore = 0;
                        }

                        $datewise_score[] = ['data_year' => $dateScore->data_year,'country_score' => $averageScore];
                    }
                } elseif ($request->filled(['region_id','indicator_id'])) {
                    $childCountries = $childCountryQuery->where('parent_id', $request->region_id)->get();

                    $dateScores = [];
                    $alreadyExistYear = [];
                    foreach ($childCountries as $childCountry) {
                        $currentIncludedDates = clone $includedDateQuery;
                        $includedDates = $currentIncludedDates->where('icr.indicator_id', '=', $request->indicator_id)->where('icr.country_region_id', '=', $childCountry->id)->orderBy('icr.year', 'ASC')->distinct()->get();

                        if (!$includedDates->isEmpty()) {
                            foreach ($includedDates as $includedDate) {
                                if (!in_array($includedDate->data_year, $alreadyExistYear)) {
                                    $dateScores[] = $includedDate;
                                    $alreadyExistYear[] = $includedDate->data_year;
                                }
                            }
                        }
                    }

                    foreach ($dateScores as $dateScore) {
                        $totalSum = 0;
                        $totalCount = 0;
                        foreach ($childCountries as $childCountry) {
                            $currentQuerySecond = clone $parentQuery;
                            $currentQuerySecond->where('cr.level', '=', 1)->where('icr.country_region_id', '=', $childCountry->id)->where('icr.indicator_id', '=', $request->indicator_id)->where('icr.year', '=', $dateScore->data_year);

                            // Calculate the sum and count for each country for this year
                            $resultSum = $currentQuerySecond->sum('icr.value');
                            $resultCount = $currentQuerySecond->count();

                            // Add the current country's sum and count to the totals
                            $totalSum += $resultSum;
                            $totalCount += $resultCount;
                        }

                        if ($totalCount > 0) {
                            $averageScore = $totalSum / $totalCount;
                        } else {
                            $averageScore = 0;
                        }

                        $datewise_score[] = ['data_year' => $dateScore->data_year,'country_score' => $averageScore];
                    }
                } else {
                    return 'g';
                    $mapQuery->where('icr.year', '=', $year);
                }

                if (!empty($childLists)) {
                    foreach ($childLists as $childList) {
                        $currentQuery = clone $mapsQuery;
                        $childIndicator = $currentQuery->where('cr.level', '=', $level)->where('icr.indicator_id', $indicator_value)->where('icr.country_region_id', $childList->id)->where('icr.year', '=', $year)->first();

                        if (!empty($childIndicator)) {
                            $childIndicators[] = $childIndicator;
                        }
                    }
                }
            } else {
                if ($request->filled(['indicator_id','sub_indicator_id'])) {
                    $includedDates = $includedDateQuery->where('cr.level', '=', 1)->where('icr.indicator_id', '=', $request->sub_indicator_id)->orderBy('icr.year', 'ASC')->distinct()->get();

                    foreach ($includedDates as $includedDate) {
                        $currentQuery = clone $parentQuery;
                        $currentQuery->where('cr.level', '=', 1)->where('icr.indicator_id', '=', $request->sub_indicator_id)->where('icr.year', '=', $includedDate->data_year);

                        $resultSum = $currentQuery->sum('icr.value');
                        $resultCount = $currentQuery->count();

                        if ($resultCount > 0) {
                            $averageScore = $resultSum / $resultCount;
                        } else {
                            $averageScore = 0;
                        }

                        $datewise_score[] = ['data_year' => $includedDate->data_year,'country_score' => $averageScore];
                    }
                } elseif ($request->filled('indicator_id')) {
                    $includedDates = $includedDateQuery->where('cr.level', '=', 1)->where('icr.indicator_id', '=', $request->indicator_id)->orderBy('icr.year', 'ASC')->distinct()->get();

                    foreach ($includedDates as $includedDate) {
                        $currentQuery = clone $parentQuery;
                        $currentQuery->where('cr.level', '=', 1)->where('icr.indicator_id', '=', $request->indicator_id)->where('icr.year', '=', $includedDate->data_year);

                        $resultSum = $currentQuery->sum('icr.value');
                        $resultCount = $currentQuery->count();

                        if ($resultCount > 0) {
                            $averageScore = $resultSum / $resultCount;
                        } else {
                            $averageScore = 0;
                        }

                        $datewise_score[] = ['data_year' => $includedDate->data_year,'country_score' => $averageScore];
                    }
                }
            }
        } else {
            $parentQuery = DB::table('country_region as cr')
                ->leftJoin('country_user as cu', function ($join) use ($year) {
                    $join->on('cr.id', '=', 'cu.country_id');
                });

            $parentQuery->select('cr.id', 'cr.title', 'cu.data_year', 'cu.country_score');

            $includedDateQuery = DB::table('country_region as cr')
                ->leftJoin('country_user as cu', function ($join) use ($year) {
                    $join->on('cr.id', '=', 'cu.country_id');
                })->select('data_year')->whereNotNull('cu.data_year')->orderBy('cu.data_year', 'ASC');

            if ($request->filled('region_id', 'country_id', 'sub_country_id')) {
                $includedDates = $includedDateQuery->where('cr.id', '=', $request->sub_country_id)->where('cr.parent_id', '=', $request->country_id)->distinct()->get();

                foreach ($includedDates as $includedDate) {
                    $currentQuery = clone $parentQuery;
                    $currentQuery->where('cr.level', '=', 2)->where('cr.parent_id', '=', $request->country_id)->where('cr.id', '=', $request->sub_country_id)->where('cu.data_year', '=', $includedDate->data_year);

                    $resultSum = $currentQuery->sum('cu.country_score');
                    $resultCount = $currentQuery->count();

                    if ($resultCount > 0) {
                        $averageScore = $resultSum / $resultCount;
                    } else {
                        $averageScore = 0;
                    }

                    $datewise_score[] = ['data_year' => $includedDate->data_year,'country_score' => $averageScore];
                }
            } elseif ($request->filled('region_id', 'country_id')) {
                $includedDates = $includedDateQuery->where('cr.parent_id', '=', $request->country_id)->distinct()->get();

                foreach ($includedDates as $includedDate) {
                    $currentQuery = clone $parentQuery;
                    $currentQuery->where('cr.level', '=', 2)->where('cr.parent_id', '=', $request->country_id)->where('cu.data_year', '=', $includedDate->data_year);
                    $resultSum = $currentQuery->sum('cu.country_score');
                    $resultCount = $currentQuery->count();

                    if ($resultCount > 0) {
                        $averageScore = $resultSum / $resultCount;
                    } else {
                        $averageScore = 0;
                    }

                    $datewise_score[] = ['data_year' => $includedDate->data_year,'country_score' => $averageScore];
                }
            } elseif ($request->filled('region_id')) {
                $includedDates = $includedDateQuery->where('cr.parent_id', '=', $request->region_id)->distinct()->get();

                foreach ($includedDates as $includedDate) {
                    $currentQuery = clone $parentQuery;
                    $currentQuery->where('cr.level', '=', 1)->where('cr.parent_id', '=', $request->region_id)->where('cu.data_year', '=', $includedDate->data_year);

                    $resultSum = $currentQuery->sum('cu.country_score');
                    $resultCount = $currentQuery->count();

                    if ($resultCount > 0) {
                        $averageScore = $resultSum / $resultCount;
                    } else {
                        $averageScore = 0;
                    }

                    $datewise_score[] = ['data_year' => $includedDate->data_year,'country_score' => $averageScore];
                }
            } else {
                $includedDates = $includedDateQuery->distinct()->get();

                foreach ($includedDates as $includedDate) {
                    $currentQuery = clone $parentQuery;
                    $currentQuery->where('cr.level', '=', 1);
                    $resultSum = $currentQuery->where('cu.data_year', '=', $includedDate->data_year)->sum('cu.country_score');
                    $resultCount = $currentQuery->where('cu.data_year', '=', $includedDate->data_year)->count();

                    if ($resultCount > 0) {
                        $averageScore = $resultSum / $resultCount;
                    } else {
                        $averageScore = 0;
                    }

                    $datewise_score[] = ['data_year' => $includedDate->data_year,'country_score' => $averageScore];
                }
            }
        }

        //Ascending Order
        usort($datewise_score, function ($a, $b) {
            return $a['data_year'] <=> $b['data_year'];
        });

        return response()->json([
            'success' => true,
            'data' => $datewise_score
        ]);
    }
    //Train Graph API
}
