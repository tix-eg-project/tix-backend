@extends('Admin.layout.app')

@section('title', __('messages.subcategories'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>{{ __('messages.subcategories') }}</h4>

    <a href="{{ route('subcategories.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> {{ __('messages.add_subcategory') }}
    </a>
</div>

<table class="table table-striped text-black">
    <thead>
        <tr>
            <th>#</th>
            <th>{{ __('messages.name') }}</th>
            <th>{{ __('messages.description') }}</th>
            <th></th>{{ __('messages.category') }}</th>
            <th>{{ __('messages.image') }}</th>
            <th>{{ __('messages.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse($subcategories as $subcategory)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $subcategory->name }}</td>
            <td>{{ Str::limit($subcategory->description, 50) }}</td>
            <td>{{ $subcategory->category->name }}</td>


            <td>
                @if($subcategory->image)
                <img src="{{ asset( $subcategory->image) }}" width="60" alt="">
                @else
                -
                @endif
            </td>
            <td>
                <a href="{{ route('subcategories.edit', $subcategory->id) }}" class="btn btn-sm btn-warning">{{ __('messages.edit') }}</a>
                <form action="{{ route('subcategories.destroy', $subcategory->id) }}" method="POST" class="d-inline-block">
                    @csrf @method('DELETE')
                    <button onclick="return confirm('{{ __('messages.confirm_delete') }}')" class="btn btn-sm btn-danger">
                        {{ __('messages.delete') }}
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">{{ __('messages.no_data') }}</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="d-flex justify-content-center">
    {{ $subcategories->links('pagination::bootstrap-4') }}
</div>
@endsection