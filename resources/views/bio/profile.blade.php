<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>{{ $person->full_name }} - Perfil | PROXICARD</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @if($designType == 'rescue')
        <link rel="stylesheet" href="{{ asset('css/bio-rescue.css') }}">
    @elseif($designType == 'government')
        <link rel="stylesheet" href="{{ asset('css/bio-government.css') }}">
    @elseif($designType == 'company')
        <link rel="stylesheet" href="{{ asset('css/bio-company.css') }}">
    @elseif($designType == 'student')
        <link rel="stylesheet" href="{{ asset('css/bio-student.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('css/bio-default.css') }}">
    @endif
</head>
<body class="bio-{{ $designType }}">
    <div class="bio-container">
        @if($designType == 'rescue')
            @include('bio.partials.rescue-card', ['person' => $person])
        @elseif($designType == 'government')
            @include('bio.partials.government-card', ['person' => $person])
        @elseif($designType == 'company')
            @include('bio.partials.company-card', ['person' => $person])
        @elseif($designType == 'student')
            @include('bio.partials.student-card', ['person' => $person])
        @else
            @include('bio.partials.default-card', ['person' => $person])
        @endif
    </div>
</body>
</html>