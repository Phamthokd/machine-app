@extends('layouts.app')

@section('content')
<div style="max-width:520px;margin:24px auto;padding:16px;">
    @if(session('success'))
        <div style="background:#d1fae5;padding:12px;border-radius:8px;margin-bottom:12px;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background:#fee2e2;padding:12px;border-radius:8px;margin-bottom:12px;">
            <ul style="margin:0;padding-left:18px;">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h2>Import danh sách máy (CSV)</h2>

    <form method="POST" action="{{ url('/machines/import-csv') }}" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" accept=".csv,text/csv" required />
        <button type="submit" style="margin-top:12px;padding:10px 16px;">Import</button>
    </form>

    <p style="margin-top:12px;color:#666;font-size:13px;">
        CSV UTF-8. Header có thể là: <br>
        <b>ma_thiet_bi</b> (hoặc <b>MA_MAY</b>) , ten_thiet_bi, to_hien_tai, brand, model, serial, invoice/cd, year, country of origin, stock-in date, department, ngày vào kho, ngày ra kho
    </p>
</div>
@endsection
