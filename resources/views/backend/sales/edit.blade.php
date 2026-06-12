@extends('backend.layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Edit Order') }} - #{{ $order->code }}</h5>
            </div>
            
            <form action="{{ route('orders.update', $order->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="card-body">
                    @php
                        $shipping_address = json_decode($order->shipping_address);
                    @endphp
                    
                    <h6 class="mb-3 text-primary">{{ translate('Shipping Address Info') }}</h6>
                    
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="name">{{ translate('Customer Name') }}</label>
                        <div class="col-md-9">
                            <input type="text" id="name" name="name" class="form-control" 
                                   value="{{ $shipping_address->name ?? ($order->user->name ?? '') }}" required>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="phone">{{ translate('Phone Number') }}</label>
                        <div class="col-md-9">
                            <input type="text" id="phone" name="phone" class="form-control" 
                                   value="{{ $shipping_address->phone ?? ($order->user->phone ?? '') }}" required>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="address">{{ translate('Address') }}</label>
                        <div class="col-md-9">
                            <textarea id="address" name="address" class="form-control" rows="3" required>{{ $shipping_address->address ?? '' }}</textarea>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="city">{{ translate('City') }}</label>
                        <div class="col-md-9">
                            <input type="text" id="city" name="city" class="form-control" 
                                   value="{{ $shipping_address->city ?? '' }}" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="state">{{ translate('State') }}</label>
                        <div class="col-md-9">
                            <input type="text" id="state" name="state" class="form-control" 
                                   value="{{ $shipping_address->state ?? '' }}">
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="postal_code">{{ translate('Postal Code') }}</label>
                        <div class="col-md-9">
                            <input type="text" id="postal_code" name="postal_code" class="form-control" 
                                   value="{{ $shipping_address->postal_code ?? '' }}">
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6 class="mb-3 text-primary">{{ translate('Additional Info') }}</h6>
                    
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="tracking_code">{{ translate('Tracking Code') }}</label>
                        <div class="col-md-9">
                            <input type="text" id="tracking_code" name="tracking_code" class="form-control" 
                                   value="{{ $order->tracking_code }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="delivery_status">{{ translate('Delivery Status') }}</label>
                        <div class="col-md-9">
                            @if (auth()->user()->can('update_order_delivery_status') && $order->delivery_status != 'delivered' && $order->delivery_status != 'cancelled')
                                <select class="form-control aiz-selectpicker" name="delivery_status" id="delivery_status">
                                    <option value="pending" @if ($order->delivery_status == 'pending') selected @endif>{{ translate('Pending') }}</option>
                                    <option value="confirmed" @if ($order->delivery_status == 'confirmed') selected @endif>{{ translate('Confirmed') }}</option>
                                    <option value="picked_up" @if ($order->delivery_status == 'picked_up') selected @endif>{{ translate('Picked Up') }}</option>
                                    <option value="on_the_way" @if ($order->delivery_status == 'on_the_way') selected @endif>{{ translate('On The Way') }}</option>
                                    <option value="delivered" @if ($order->delivery_status == 'delivered') selected @endif>{{ translate('Delivered') }}</option>
                                    <option value="cancelled" @if ($order->delivery_status == 'cancelled') selected @endif>{{ translate('Cancel') }}</option>
                                </select>
                            @else
                                <input type="text" class="form-control" value="{{ translate(ucfirst(str_replace('_', ' ', $order->delivery_status))) }}" disabled>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="payment_status">{{ translate('Payment Status') }}</label>
                        <div class="col-md-9">
                            @if (auth()->user()->can('update_order_payment_status'))
                                <select class="form-control aiz-selectpicker" name="payment_status" id="payment_status">
                                    <option value="unpaid" @if ($order->payment_status == 'unpaid') selected @endif>{{ translate('Unpaid') }}</option>
                                    <option value="paid" @if ($order->payment_status == 'paid') selected @endif>{{ translate('Paid') }}</option>
                                </select>
                            @else
                                <input type="text" class="form-control" value="{{ translate(ucfirst(str_replace('_', ' ', $order->payment_status))) }}" disabled>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Total Amount') }}</label>
                        <div class="col-md-9 pt-2">
                            <span class="badge badge-inline badge-dark">{{ single_price($order->grand_total) }}</span>
                        </div>
                    </div>

                    <hr>
                    
                    <h6 class="mb-3 text-primary d-flex align-items-center">
                        <i class="las la-sticky-note mr-2"></i> {{ translate('Order Notes Log (Multiple)') }}
                    </h6>
                    
                    @if($order->notes->count() > 0)
                        <div class="border rounded p-3 mb-4 bg-light" style="max-height: 300px; overflow-y: auto;">
                            @foreach($order->notes as $note)
                                <div class="p-3 mb-2 rounded bg-white border">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="font-weight-bold text-dark">{{ optional($note->user)->name ?? translate('System / Unknown') }}</span>
                                        <small class="text-muted">{{ $note->created_at->format('d-m-Y h:i A') }}</small>
                                    </div>
                                    <div class="text-secondary" style="white-space: pre-wrap;">{{ $note->note }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 border rounded bg-light mb-4">
                            <p class="text-muted mb-0">{{ translate('No notes added to this order yet.') }}</p>
                        </div>
                    @endif
                    
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="note">{{ translate('Add New Note') }}</label>
                        <div class="col-md-9">
                            <textarea id="note" name="note" class="form-control" rows="3" 
                                      placeholder="{{ translate('Type a note to append to this order...') }}"></textarea>
                        </div>
                    </div>
                    
                    <div class="form-group mb-0 text-right">
                        <a href="{{ route('all_orders.index') }}" class="btn btn-outline-secondary mr-2">{{ translate('Back to List') }}</a>
                        <button type="submit" class="btn btn-primary">{{ translate('Update Order & Notes') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
