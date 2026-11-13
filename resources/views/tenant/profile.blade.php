@extends('layouts.dashboard')
@section('title', 'Profil Penyewa')
@section('heading', 'Profil Saya')
@section('subheading', 'Kelola informasi identitas penyewa')
@section('sidebar') @include('partials.sidebar-tenant') @endsection
@section('content')
@include('partials.profile-form', ['user' => $user])
@endsection

