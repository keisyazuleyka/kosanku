@extends('layouts.dashboard')
@section('title', 'Profil Pemilik Kost')
@section('heading', 'Profil Pemilik')
@section('subheading', 'Kelola informasi identitas pemilik kost')
@section('sidebar') @include('partials.sidebar-owner') @endsection
@section('content')
@include('partials.profile-form', ['user' => $user])
@endsection

