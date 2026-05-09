@extends('backend.layouts.layout')

@section('content')
<section class="align-items-center d-flex h-100 bg-white">
	<div class="container">
		<div class="row">
			<div class="col-lg-6 mx-auto text-center py-4">
			    <h3>{{translate('The Website is Under Maintenance')}}</h3>
			    <div class="lead">{{translate('We will be back soon!')}}</div>
			</div>
		</div>
	</div>
</section>
@endsection
