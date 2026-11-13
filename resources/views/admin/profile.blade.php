@extends('layouts.dashboard')
@section('title', 'Profil Admin')
@section('heading', 'Profil Pengguna')
@section('subheading', 'Kelola informasi identitas akun admin')
@section('sidebar') @include('partials.sidebar-admin') @endsection
@section('content')
@include('partials.profile-form', ['user' => $user])
@endsection

