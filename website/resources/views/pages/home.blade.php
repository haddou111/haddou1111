@extends('layouts.app')

@section('title', 'Accueil - ESTK')

@section('content')
<div class="container">
    <!-- Section Hero -->
    <div class="hero-section">
        <img src="{{ asset('images/EST_KHENIFRA.jpg') }}" alt="EST Khenifra" class="img-fluid">
    </div>

    <!-- Section Actualités -->
    <div class="news-section mt-5">
        <h2 class="text-center mb-4">Actualités</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <img src="{{ asset('images/amphi.jpeg') }}" class="card-img-top" alt="Amphithéâtre">
                    <div class="card-body">
                        <h5 class="card-title">Nouvel Amphithéâtre</h5>
                        <p class="card-text">Inauguration du nouvel amphithéâtre de l'ESTK.</p>
                    </div>
                </div>
            </div>
            <!-- Ajoutez d'autres actualités ici -->
        </div>
    </div>

    <!-- Section Services -->
    <div class="services-section mt-5">
        <h2 class="text-center mb-4">Services</h2>
        <div class="row">
            <div class="col-md-3">
                <div class="service-card text-center">
                    <img src="{{ asset('images/ENT.png') }}" alt="ENT" class="service-icon">
                    <h4>ENT</h4>
                    <p>Espace Numérique de Travail</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="service-card text-center">
                    <img src="{{ asset('images/pmb.png') }}" alt="PMB" class="service-icon">
                    <h4>Bibliothèque</h4>
                    <p>Système de gestion de bibliothèque</p>
                </div>
            </div>
            <!-- Ajoutez d'autres services ici -->
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/animations.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/transitions.js') }}"></script>
@endpush 