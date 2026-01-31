@extends('Admin.layout.app')

@section('content')
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4 text-center"><span class="text-muted fw-light"></span> {{ __('messages.Vendor') }}</h4>

        <div class="card">
            <div class="card-body">

                <div class="table-responsive text-nowrap">
                    <table class="table table-striped text-black text-center">
                        <tbody class="text-center">

                            <tr>
                                <th>{{ __('messages.Company Name') }}</th>
                                <td>{{ $vendor->company_name }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('messages.Description') }}</th>
                                <td style="white-space: pre-wrap; max-width: 300px;">{{ $vendor->description }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('messages.Name') }}</th>
                                <td>{{ $vendor->name }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('messages.Email') }}</th>
                                <td>{{ $vendor->email }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('messages.Phone') }}</th>
                                <td>{{ $vendor->phone }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('messages.Address') }}</th>
                                <td>{{ $vendor->address }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('messages.Postal Code') }}</th>
                                <td>{{ $vendor->postal_code }}</td> {{-- ✅ تصحيح --}}
                            </tr>

                            <tr>
                                <th>{{ __('messages.Vodafone Cash') }}</th>
                                <td>{{ $vendor->vodafone_cash }}</td> {{-- ✅ تصحيح --}}
                            </tr>

                            <tr>
                                <th>{{ __('messages.InstaPay') }}</th>
                                <td>{{ $vendor->instapay }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('messages.Status') }}</th>
                                <td>
                                    <form action="{{ route('vendors.updateStatus', $vendor->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="{{ $vendor->status ? 0 : 1 }}">
                                        <button type="submit" class="btn btn-sm {{ $vendor->status ? 'btn-danger' : 'btn-success' }}">
                                            {{ $vendor->status ? 'إيقاف' : 'تفعيل' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <tr>
                                <th>{{ __('messages.Type of Business') }}</th>
                                <td>{{ $vendor->type_business }}</td> {{-- ✅ تصحيح --}}
                            </tr>

                            <tr>
                                <th>{{ __('messages.Category') }}</th>
                                <td>{{ $vendor->category->name ?? '-' }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('messages.Country') }}</th>
                                <td>{{ $vendor->country->name ?? '-' }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('messages.City') }}</th>
                                <td>{{ $vendor->city->name ?? '-' }}</td>
                            </tr>

                            <tr>
                                <th>{{ __('messages.Image') }}</th>
                                <td>
                                    @if ($vendor->image)
                                    <img src="{{ asset($vendor->image) }}" alt="Vendor Image" class="img-thumbnail" style="max-width: 100px;">
                                    @else
                                    <span class="text-muted">{{ __('messages.No image available') }}</span>
                                    @endif
                                </td>
                            </tr>

                            {{-- ✅ صور البطاقة: أمامية --}}
                            <tr>
                                <th>{{ __('messages.Front ID Image') }}</th>
                                <td>
                                    @if ($vendor->id_card_front_image)
                                    <img src="{{ asset( $vendor->id_card_front_image) }}" alt="Front ID" class="img-thumbnail" style="max-width: 140px;">
                                    @else
                                    <span class="text-muted">{{ __('messages.No image available') }}</span>
                                    @endif
                                </td>
                            </tr>

                            
                            <tr>
                                <th>{{ __('messages.Back ID Image') }}</th>
                                <td>
                                    @if ($vendor->id_card_back_image)
                                    <img src="{{ asset($vendor->id_card_back_image) }}" alt="Back ID" class="img-thumbnail" style="max-width: 140px;">
                                    @else
                                    <span class="text-muted">{{ __('messages.No image available') }}</span>
                                    @endif
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <a href="{{ route('vendore.index') }}" class="btn btn-secondary mt-3">{{ __('messages.Back') }}</a> {{-- ✅ تصحيح اسم الراوت إن لزم --}}
            </div>
        </div>
    </div>
</div>
@endsection