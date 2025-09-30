@extends('layouts.app')

@section('title', 'Debug Layout')
@section('page-title', 'Debug Layout')

@section('content')
<div class="p-6 bg-white rounded-lg border">Layout OK. User: {{ $userName ?? 'User' }}</div>
@endsection
