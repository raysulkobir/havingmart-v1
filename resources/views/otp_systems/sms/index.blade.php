@extends('backend.layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h3 class="fs-18 mb-0">{{translate('Send Bulk SMS')}}</h3>
            </div>
            <form class="form-horizontal" action="{{ route('sms.send') }}" method="POST" enctype="multipart/form-data">
            	@csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-2 control-label">{{translate('Send To')}}</label>
                        <div class="col-sm-10">
                            <div class="aiz-radio-inline">
                                <label class="aiz-radio">
                                    <input type="radio" name="send_to" value="selected" checked>
                                    <span>{{ translate('Selected Customers') }}</span>
                                </label>
                                <label class="aiz-radio">
                                    <input type="radio" name="send_to" value="range">
                                    <span>{{ translate('Customer Range') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 control-label" for="name">{{translate('Mobile Users')}}</label>
                        <div class="col-sm-10">
                            <select class="form-control aiz-selectpicker" data-live-search="true" name="user_phones[]" multiple>
                                @foreach($users as $user)
                                    @if ($user->phone != null)
                                        <option value="{{$user->phone}}">{{$user->name}} - {{$user->phone}} - {{$user->id}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="customer-range-wrapper" style="display: none;">
                        <label class="col-sm-2 control-label" for="customer_range">{{translate('Customer Range')}}</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="customer_range" name="customer_range" placeholder="1-2000">
                            <small class="form-text text-muted">{{ translate('Customers are selected by ID order. Example: 1-2000 sends to customers 1 through 2000 that have phone numbers.') }}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 control-label" for="name">{{translate('SMS content')}}</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="content" required>🎁 ঈদ Giveaway! জিতুন পুরস্কার 
👉 https://go.havingmart.com/t/Ng3N</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label">{{translate('Template ID')}}</label>
                        <div class="col-md-10">
                            <input type="text" name="template_id" value="1"  class="form-control" placeholder="{{translate('Template Id')}}">
                            <small class="form-text text-danger">{{ ('**N.B : Template ID is Required Only for Fast2SMS DLT Manual **') }}</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary" type="submit">{{translate('Send')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
    <script type="text/javascript">
        (function () {
            function toggleSmsTargetFields() {
                var sendTo = document.querySelector('input[name="send_to"]:checked').value;
                var userSelect = document.querySelector('select[name="user_phones[]"]');
                var rangeWrapper = document.getElementById('customer-range-wrapper');
                var rangeInput = document.getElementById('customer_range');

                if (sendTo === 'range') {
                    userSelect.closest('.form-group').style.display = 'none';
                    userSelect.removeAttribute('required');
                    rangeWrapper.style.display = '';
                    rangeInput.setAttribute('required', 'required');
                } else {
                    userSelect.closest('.form-group').style.display = '';
                    userSelect.setAttribute('required', 'required');
                    rangeWrapper.style.display = 'none';
                    rangeInput.removeAttribute('required');
                }
            }

            document.querySelectorAll('input[name="send_to"]').forEach(function (input) {
                input.addEventListener('change', toggleSmsTargetFields);
            });

            toggleSmsTargetFields();
        })();
    </script>
@endsection
