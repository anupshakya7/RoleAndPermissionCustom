public function trainGraph(Request $request){
		$validator = Validator::make($request->all(),[
			'region_id'=>'nullable|integer',
			'country_id'=>'nullable|integer',
			'sub_country_id'=>'nullable|integer',
			'indicator_id'=>'nullable|integer',
			'sub_indicator_id'=>'nullable|integer'
		]);
		
		if($validator->fails()){
			return response()->json(['errors'=>$validator->errors()]);
		}
		
		$year = $request->year ?? 2023;
		$datewise_score = [];
		
		if($request->filled('indicator_id')){
			$parentQuery = DB::table('country_region as cr')
				->leftJoin('indicator_country_region as icr', function ($join) use($year){
					$join->on('cr.id', '=', 'icr.country_region_id');
				});
			$parentQuery->select('cr.id','cr.title','icr.year as data_year','icr.value');
			
			$includedDateQuery = DB::table('country_region as cr')
				->leftJoin('indicator_country_region as icr', function ($join) use($year){
					$join->on('cr.id', '=', 'icr.country_region_id');
				})->select('icr.year as data_year')->whereNotNull('icr.year');
			
			if($request->filled('region_id')){
				//Child Country List if there is Any
				$childCountryLists = CountryRegion::query();
				$childCountryLists->select('id','title');
				if($request->filled(['region_id','country_id','sub_country_id','indicator_id','sub_indicator_id'])){
					$includedDates = $includedDateQuery->where('cr.level','=',2)->where('icr.indicator_id','=',$request->sub_indicator_id)->where('icr.country_region_id','=',$request->sub_country_id)->orderBy('icr.year','ASC')->distinct()->get();
					
					foreach($includedDates as $includedDate){
						$currentQuery = clone $parentQuery;
						$currentQuery->where('cr.level','=',2)->where('icr.indicator_id','=',$request->sub_indicator_id)->where('icr.country_region_id','=',$request->sub_country_id)->where('icr.year','=',$includedDate->data_year);
						
						$resultSum = $currentQuery->sum('icr.value');
						$resultCount = $currentQuery->count();
						
						if($resultCount > 0){
							$averageScore = $resultSum/$resultCount;
						}else{
							$averageScore = 0;
						}
					
						$datewise_score[] = ['data_year'=>$includedDate->data_year,'country_score'=>$averageScore];
					}
				}elseif($request->filled(['region_id','country_id','indicator_id','sub_indicator_id'])){
					$childCountries = CountryRegion::select('id','title')->where('parent_id',$request->country_id)->get();
					
					$dateScores = [];
					$alreadyExistYear = [];
					foreach($childCountries as $childCountry){
						$currentIncludedDates = clone $includedDateQuery;
						$includedDates = $currentIncludedDates->where('icr.indicator_id','=',$request->sub_indicator_id)->where('icr.country_region_id','=',$childCountry->id)->orderBy('icr.year','ASC')->distinct()->get();
						
						if(!$includedDates->isEmpty()){
							foreach($includedDates as $includedDate){
								if(!in_array($includedDate->data_year,$alreadyExistYear)){
									$dateScores[] = $includedDate;
									$alreadyExistYear[] = $includedDate->data_year;
								}
							}
						}
					}
				
				foreach($dateScores as $dateScore){
					$totalSum = 0;
					$totalCount = 0;
					foreach($childCountries as $childCountry){
						$currentQuerySecond = clone $parentQuery;
						$currentQuerySecond->where('cr.level','=',2)->where('icr.country_region_id','=',$childCountry->id)->where('icr.indicator_id','=',$request->sub_indicator_id)->where('icr.year','=',$dateScore->data_year);
						
						// Calculate the sum and count for each country for this year
						$resultSum = $currentQuerySecond->sum('icr.value');
						$resultCount = $currentQuerySecond->count();
						
						// Add the current country's sum and count to the totals
						$totalSum += $resultSum;
						$totalCount += $resultCount;
					}
					
					if($totalCount > 0){
						$averageScore = $totalSum/$totalCount;
					}else{
						$averageScore = 0;
					}
				
					$datewise_score[] = ['data_year'=>$dateScore->data_year,'country_score'=>$averageScore];
				}
				}elseif($request->filled(['region_id','indicator_id','sub_indicator_id'])){
					$childCountries = CountryRegion::select('id','title')->where('parent_id',$request->region_id)->get();
					
					$dateScores = [];
					$alreadyExistYear = [];
					foreach($childCountries as $childCountry){
						$currentIncludedDates = clone $includedDateQuery;
						$includedDates = $currentIncludedDates->where('icr.indicator_id','=',$request->sub_indicator_id)->where('icr.country_region_id','=',$childCountry->id)->orderBy('icr.year','ASC')->distinct()->get();
						
						if(!$includedDates->isEmpty()){
							foreach($includedDates as $includedDate){
								if(!in_array($includedDate->data_year,$alreadyExistYear)){
									$dateScores[] = $includedDate;
									$alreadyExistYear[] = $includedDate->data_year;
								}
							}
						}
					}
				
				foreach($dateScores as $dateScore){
					$totalSum = 0;
					$totalCount = 0;
					foreach($childCountries as $childCountry){
						$currentQuerySecond = clone $parentQuery;
						$currentQuerySecond->where('cr.level','=',1)->where('icr.country_region_id','=',$childCountry->id)->where('icr.indicator_id','=',$request->sub_indicator_id)->where('icr.year','=',$dateScore->data_year);
						
						// Calculate the sum and count for each country for this year
						$resultSum = $currentQuerySecond->sum('icr.value');
						$resultCount = $currentQuerySecond->count();
						
						// Add the current country's sum and count to the totals
						$totalSum += $resultSum;
						$totalCount += $resultCount;
					}
					
					if($totalCount > 0){
						$averageScore = $totalSum/$totalCount;
					}else{
						$averageScore = 0;
					}
				
					$datewise_score[] = ['data_year'=>$dateScore->data_year,'country_score'=>$averageScore];
				}
				}
				elseif($request->filled(['region_id','country_id','sub_country_id','indicator_id'])){
					$includedDates = $includedDateQuery->where('cr.level','=',2)->where('icr.indicator_id','=',$request->indicator_id)->where('icr.country_region_id','=',$request->sub_country_id)->orderBy('icr.year','ASC')->distinct()->get();
					
					foreach($includedDates as $includedDate){
						$currentQuery = clone $parentQuery;
						$currentQuery->where('cr.level','=',2)->where('icr.indicator_id','=',$request->indicator_id)->where('icr.country_region_id','=',$request->sub_country_id)->where('icr.year','=',$includedDate->data_year);
						
						$resultSum = $currentQuery->sum('icr.value');
						$resultCount = $currentQuery->count();
						
						if($resultCount > 0){
							$averageScore = $resultSum/$resultCount;
						}else{
							$averageScore = 0;
						}
					
						$datewise_score[] = ['data_year'=>$includedDate->data_year,'country_score'=>$averageScore];
					}
				}
				elseif($request->filled(['region_id','country_id','indicator_id'])){
					$childCountries = CountryRegion::select('id','title')->where('parent_id',$request->country_id)->get();
					
					$dateScores = [];
					$alreadyExistYear = [];
					foreach($childCountries as $childCountry){
						$currentIncludedDates = clone $includedDateQuery;
						$includedDates = $currentIncludedDates->where('icr.indicator_id','=',$request->indicator_id)->where('icr.country_region_id','=',$childCountry->id)->orderBy('icr.year','ASC')->distinct()->get();
						
						if(!$includedDates->isEmpty()){
							foreach($includedDates as $includedDate){
								if(!in_array($includedDate->data_year,$alreadyExistYear)){
									$dateScores[] = $includedDate;
									$alreadyExistYear[] = $includedDate->data_year;
								}
							}
						}
					}
				
				foreach($dateScores as $dateScore){
					$totalSum = 0;
					$totalCount = 0;
					foreach($childCountries as $childCountry){
						$currentQuerySecond = clone $parentQuery;
						$currentQuerySecond->where('cr.level','=',2)->where('icr.country_region_id','=',$childCountry->id)->where('icr.indicator_id','=',$request->indicator_id)->where('icr.year','=',$dateScore->data_year);
						
						// Calculate the sum and count for each country for this year
						$resultSum = $currentQuerySecond->sum('icr.value');
						$resultCount = $currentQuerySecond->count();
						
						// Add the current country's sum and count to the totals
						$totalSum += $resultSum;
						$totalCount += $resultCount;
					}
					
					if($totalCount > 0){
						$averageScore = $totalSum/$totalCount;
					}else{
						$averageScore = 0;
					}
				
					$datewise_score[] = ['data_year'=>$dateScore->data_year,'country_score'=>$averageScore];
				}
				}
				elseif($request->filled(['region_id','indicator_id'])){
					$childCountries = CountryRegion::select('id','title')->where('parent_id',$request->region_id)->get();
					
					$dateScores = [];
					$alreadyExistYear = [];
					foreach($childCountries as $childCountry){
						$currentIncludedDates = clone $includedDateQuery;
						$includedDates = $currentIncludedDates->where('icr.indicator_id','=',$request->indicator_id)->where('icr.country_region_id','=',$childCountry->id)->orderBy('icr.year','ASC')->distinct()->get();
						
						if(!$includedDates->isEmpty()){
							foreach($includedDates as $includedDate){
								if(!in_array($includedDate->data_year,$alreadyExistYear)){
									$dateScores[] = $includedDate;
									$alreadyExistYear[] = $includedDate->data_year;
								}
							}
						}
					}
				
				foreach($dateScores as $dateScore){
					$totalSum = 0;
					$totalCount = 0;
					foreach($childCountries as $childCountry){
						$currentQuerySecond = clone $parentQuery;
						$currentQuerySecond->where('cr.level','=',1)->where('icr.country_region_id','=',$childCountry->id)->where('icr.indicator_id','=',$request->indicator_id)->where('icr.year','=',$dateScore->data_year);
						
						// Calculate the sum and count for each country for this year
						$resultSum = $currentQuerySecond->sum('icr.value');
						$resultCount = $currentQuerySecond->count();
						
						// Add the current country's sum and count to the totals
						$totalSum += $resultSum;
						$totalCount += $resultCount;
					}
					
					if($totalCount > 0){
						$averageScore = $totalSum/$totalCount;
					}else{
						$averageScore = 0;
					}
				
					$datewise_score[] = ['data_year'=>$dateScore->data_year,'country_score'=>$averageScore];
				}
				}else{
					return 'g';
					$mapQuery->where('icr.year','=',$year);
				}
				
				if(!empty($childLists)){
					foreach($childLists as $childList){
						$currentQuery = clone $mapsQuery;
						$childIndicator = $currentQuery->where('cr.level','=',$level)->where('icr.indicator_id',$indicator_value)->where('icr.country_region_id',$childList->id)->where('icr.year','=',$year)->first();
						
						if(!empty($childIndicator)){
							$childIndicators[] = $childIndicator;
						}
					}
				}
			}else{
				if($request->filled(['indicator_id','sub_indicator_id'])){
					$includedDates = $includedDateQuery->where('cr.level','=',1)->where('icr.indicator_id','=',$request->sub_indicator_id)->orderBy('icr.year','ASC')->distinct()->get();
					
					foreach($includedDates as $includedDate){
						$currentQuery = clone $parentQuery;
						$currentQuery->where('cr.level','=',1)->where('icr.indicator_id','=',$request->sub_indicator_id)->where('icr.year','=',$includedDate->data_year);
						
						$resultSum = $currentQuery->sum('icr.value');
						$resultCount = $currentQuery->count();
						
						if($resultCount > 0){
							$averageScore = $resultSum/$resultCount;
						}else{
							$averageScore = 0;
						}
					
						$datewise_score[] = ['data_year'=>$includedDate->data_year,'country_score'=>$averageScore];
					}
				}
				elseif($request->filled('indicator_id')){
					$includedDates = $includedDateQuery->where('cr.level','=',1)->where('icr.indicator_id','=',$request->indicator_id)->orderBy('icr.year','ASC')->distinct()->get();
					
					foreach($includedDates as $includedDate){
						$currentQuery = clone $parentQuery;
						$currentQuery->where('cr.level','=',1)->where('icr.indicator_id','=',$request->indicator_id)->where('icr.year','=',$includedDate->data_year);
						
						$resultSum = $currentQuery->sum('icr.value');
						$resultCount = $currentQuery->count();
						
						if($resultCount > 0){
							$averageScore = $resultSum/$resultCount;
						}else{
							$averageScore = 0;
						}
					
						$datewise_score[] = ['data_year'=>$includedDate->data_year,'country_score'=>$averageScore];
					}
				}
			}
		}else{
			$parentQuery = DB::table('country_region as cr')
				->leftJoin('country_user as cu', function ($join) use($year){
					$join->on('cr.id', '=', 'cu.country_id');
				});
			
			$parentQuery->select('cr.id','cr.title','cu.data_year','cu.country_score');
			
			$includedDateQuery = DB::table('country_region as cr')
				->leftJoin('country_user as cu', function ($join) use($year){
					$join->on('cr.id', '=', 'cu.country_id');
				})->select('data_year')->whereNotNull('cu.data_year')->orderBy('cu.data_year','ASC');
			
			if($request->filled('region_id','country_id','sub_country_id')){
				$includedDates = $includedDateQuery->where('cr.id','=',$request->sub_country_id)->where('cr.parent_id','=',$request->country_id)->distinct()->get();
				
				foreach($includedDates as $includedDate){
					$currentQuery = clone $parentQuery;
					$currentQuery->where('cr.level','=',2)->where('cr.parent_id','=',$request->country_id)->where('cr.id','=',$request->sub_country_id)->where('cu.data_year','=',$includedDate->data_year);
					
					$resultSum = $currentQuery->sum('cu.country_score');
					$resultCount = $currentQuery->count();
					
					if($resultCount > 0){
						$averageScore = $resultSum/$resultCount;
					}else{
						$averageScore = 0;
					}
				
					$datewise_score[] = ['data_year'=>$includedDate->data_year,'country_score'=>$averageScore];
				}
			}elseif($request->filled('region_id','country_id')){
				$includedDates = $includedDateQuery->where('cr.parent_id','=',$request->country_id)->distinct()->get();
				
				foreach($includedDates as $includedDate){
					$currentQuery = clone $parentQuery;
					$currentQuery->where('cr.level','=',2)->where('cr.parent_id','=',$request->country_id)->where('cu.data_year','=',$includedDate->data_year);
					$resultSum = $currentQuery->sum('cu.country_score');
					$resultCount = $currentQuery->count();
					
					if($resultCount > 0){
						$averageScore = $resultSum/$resultCount;
					}else{
						$averageScore = 0;
					}
				
					$datewise_score[] = ['data_year'=>$includedDate->data_year,'country_score'=>$averageScore];
				}
			}elseif($request->filled('region_id')){
				$includedDates = $includedDateQuery->where('cr.parent_id','=',$request->region_id)->distinct()->get();
				
				foreach($includedDates as $includedDate){
					$currentQuery = clone $parentQuery;
					$currentQuery->where('cr.level','=',1)->where('cr.parent_id','=',$request->region_id)->where('cu.data_year','=',$includedDate->data_year);
			
					$resultSum = $currentQuery->sum('cu.country_score');
					$resultCount = $currentQuery->count();
					
					if($resultCount > 0){
						$averageScore = $resultSum/$resultCount;
					}else{
						$averageScore = 0;
					}
				
					$datewise_score[] = ['data_year'=>$includedDate->data_year,'country_score'=>$averageScore];
				}
			}else{
				$includedDates = $includedDateQuery->distinct()->get();

				foreach($includedDates as $includedDate){
					$currentQuery = clone $parentQuery;
					$currentQuery->where('cr.level','=',1);
					$resultSum = $currentQuery->where('cu.data_year','=',$includedDate->data_year)->sum('cu.country_score');
					$resultCount = $currentQuery->where('cu.data_year','=',$includedDate->data_year)->count();
					
					if($resultCount > 0){
						$averageScore = $resultSum/$resultCount;
					}else{
						$averageScore = 0;
					}
				
					$datewise_score[] = ['data_year'=>$includedDate->data_year,'country_score'=>$averageScore];
				}
			}
		}
		
		//Ascending Order
		usort($datewise_score, function($a, $b) {
			return $a['data_year'] <=> $b['data_year'];
		});
		
		return response()->json([
			'success'=>true,
			'data'=>$datewise_score
		]);
	}