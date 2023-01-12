<option value="">Select City</option>
@if(!empty($cityData))
	@foreach($cityData as $sArray)
		<option value="{{ $sArray->city_code }}">{{ $sArray->city_name }}</option>
	@endforeach	
@endif