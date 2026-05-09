@extends('backend.layouts.layout')

@section('content')
<section class="text-center py-6">
	<div class="container">
		<div class="row">
			<div class="col-lg-6 mx-auto">
				<h1 class="not_found">500</h1>
				<h3>{{ translate("Server Error!") }}</h3>
		    	<p class="fs-16 opacity-60">{{ translate("The server failed to load your request.") }}</p>
		    	<a class="btn btn-primary btn-md " href="{{url('/')}}">Go To Home Page</a>
			</div>
		</div>
	</div>
</section>
@endsection
