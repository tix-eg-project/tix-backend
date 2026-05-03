@extends('Admin.layout.app')

@section('stay_in_touch_active', 'active')
@section('stay_in_touch_open', 'open')
@section('title', __('messages.stay_in_touch'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ __('messages.stay_in_touch') }}</h4>
    <a href="{{ route('stay-in-touch.edit') }}" class="btn btn-primary">
        <i class="bi bi-pencil-square me-1"></i> {{ __('messages.edit') }}
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('messages.close') }}"></button>
    </div>
@endif

@if($data)
    <div class="card rounded-4 custom-card bg-light text-dark">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('messages.address') }}</th>
                            <th>{{ __('messages.phone_numbers') }}</th>
                            <th>{{ __('messages.working_hours') }}</th>
                            <th>{{ __('messages.map_link') }}</th>
                            <th>{{ __('messages.whatsapp_link') }}</th>
                            <th>{{ __('messages.web_link') }}</th>
                            <th>{{ __('messages.email') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <ul class="mb-0">
                                    @foreach((array)($data->address ?? []) as $address)
                                        <li>{{ $address }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <ul class="mb-0">
                                    @foreach((array)($data->phones ?? []) as $phone)
                                        <li>{{ $phone }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>{{ $data->work_hours ?? '' }}</td>
                            <td>
                                @if(!empty($data->map_link))
                                    <a href="{{ $data->map_link }}" target="_blank">{{ __('messages.view_map') }}</a>
                                @endif
                            </td>
                            <td>
                                @if(!empty($data->whatsapp_link))
                                    <a href="{{ $data->whatsapp_link }}" target="_blank">{{ __('messages.open_whatsapp') }}</a>
                                @endif
                            </td>
                            <td>
                                <ul class="mb-0">
                                    @foreach((array)($data->web_link ?? []) as $link)
                                        @if($link)
                                            <li><a href="{{ $link }}" target="_blank">{{ __('messages.visit_website') }}</a></li>
                                        @endif
                                    @endforeach
                                </ul>
                            </td>
                            <td>{{ $data->email ?? '' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <p class="text-muted mb-0">{{ __('messages.no_data') }}</p>
@endif
@endsection
