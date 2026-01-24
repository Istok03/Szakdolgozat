@extends('layouts.admin')

@section('content')
<div class="container py-4">

    <h1 class="fw-bold text-white mb-4">Termékek kezelése</h1>


<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('admin.products.create') }}" class="btn btn-success">+ Új termék</a>
</div>


    
    <div class="admin-table-card">

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Kép</th>
                    <th>Név</th>
                    <th>Ár</th>
                    <th>Kedvezmény</th>
                    <th class="text-end">Művelet</th>
                </tr>
            </thead>

            <tbody>
                @foreach($products as $product)
                    <tr>
                        <td>
                            <img src="{{ asset($product->image) }}" width="60" height="60">
                        </td>

                        <td>{{ $product->name }}</td>

                        <td>{{ number_format($product->price, 0, ',', ' ') }} Ft</td>

                        <td>
                            @if($product->discount > 0)
                                <span class="badge-discount">-{{ $product->discount }}%</span>
                            @else
                                <span class="badge-discount">0%</span>
                            @endif
                        </td>

                        <td class="text-end">
                            <a href="{{ route('admin.products.edit', $product->id) }}" 
                               class="btn-admin-light">
                                Szerkesztés
                            </a>

                            <form action="{{ route('admin.products.destroy', $product->id) }}" 
                                  method="POST" 
                                  style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn-admin-danger">
                                    Törlés
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>

    </div>

</div>
@endsection
