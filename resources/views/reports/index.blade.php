@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@endsection

@section('content')
    @include('reports.partials.adsense')
    @include('reports.partials.charts')
    @include('reports.partials._project-tables')
@endsection
