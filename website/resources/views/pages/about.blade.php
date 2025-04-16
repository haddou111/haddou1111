@extends('layouts.app')

@section('title', 'À propos - ESTK')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <h1 class="text-center mb-5">À propos de l'ESTK</h1>
            
            <div class="about-section">
                <img src="{{ asset('images/back.jpg') }}" alt="ESTK" class="img-fluid mb-4">
                
                <h2>Présentation</h2>
                <p>
                    L'École Supérieure de Technologie de Kénitra (ESTK) est un établissement d'enseignement supérieur
                    qui forme des techniciens supérieurs dans différents domaines technologiques.
                </p>

                <h2>Mission</h2>
                <p>
                    Notre mission est de former des techniciens supérieurs compétents, capables de répondre
                    aux besoins du marché du travail et de contribuer au développement économique du pays.
                </p>

                <h2>Valeurs</h2>
                <ul>
                    <li>Excellence académique</li>
                    <li>Innovation technologique</li>
                    <li>Responsabilité sociale</li>
                    <li>Ouverture sur l'international</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection 