@extends('layouts.admin')

@section('title', isset($company) ? 'Editar Institución' : 'Nueva Institución')
@section('header', isset($company) ? 'Editar Institución' : 'Registrar Institución')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/companies-form.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
<div class="company-create-modern">
    <!-- Hero Section -->
    <div class="form-hero">
        <div class="hero-content">
            <div class="hero-icon">
                <i class="fas {{ isset($company) ? 'fa-edit' : 'fa-building' }}"></i>
            </div>
            <div class="hero-text">
                <h1>{{ isset($company) ? 'Editar Institución' : 'Registrar Nueva Institución' }}</h1>
                <p>Complete la información según el tipo de institución</p>
            </div>
        </div>
    </div>

    <div class="form-container-glass">
        <!-- Mensajes de Error -->
        @if ($errors->any())
            <div class="alert-glass error">
                <i class="fas fa-exclamation-circle"></i>
                <div class="alert-content">
                    <strong>Por favor, corrija los siguientes errores:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button class="alert-close" onclick="this.parentElement.remove()">×</button>
            </div>
        @endif

        <form method="POST" 
              action="{{ isset($company) ? route('admin.companies.update', $company->id) : route('admin.companies.store') }}"
              enctype="multipart/form-data">
            @csrf
            @if(isset($company))
                @method('PUT')
            @endif

            <!-- Selector de Tipo -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-tag"></i>
                    </div>
                    <div class="section-title">
                        <h3>Tipo de Institución</h3>
                        <p>Selecciona el tipo de institución que deseas registrar</p>
                    </div>
                </div>

                <div class="type-cards">
                    <!-- Empresa -->
                    <div class="type-card {{ old('type', $company->type ?? '') == 'company' ? 'active' : '' }}" data-type="company">
                        <div class="type-card-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="type-card-info">
                            <h4>Empresa</h4>
                            <p>Para organizaciones, corporaciones y negocios</p>
                        </div>
                        <div class="type-card-radio">
                            <input type="radio" name="type" value="company" id="typeCompany"
                                   {{ old('type', $company->type ?? '') == 'company' ? 'checked' : '' }} required>
                            <label for="typeCompany">
                                <span class="radio-custom"></span>
                            </label>
                        </div>
                    </div>

                    <!-- Colegio -->
                    <div class="type-card {{ old('type', $company->type ?? '') == 'school' ? 'active' : '' }}" data-type="school">
                        <div class="type-card-icon">
                            <i class="fas fa-school"></i>
                        </div>
                        <div class="type-card-info">
                            <h4>Colegio</h4>
                            <p>Para instituciones educativas y academias</p>
                        </div>
                        <div class="type-card-radio">
                            <input type="radio" name="type" value="school" id="typeSchool"
                                   {{ old('type', $company->type ?? '') == 'school' ? 'checked' : '' }} required>
                            <label for="typeSchool">
                                <span class="radio-custom"></span>
                            </label>
                        </div>
                    </div>

                    <!-- ONG de Seguridad y Rescate -->
                    <div class="type-card {{ old('type', $company->type ?? '') == 'ngo_rescue' ? 'active' : '' }}" data-type="ngo_rescue">
                        <div class="type-card-icon">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <div class="type-card-info">
                            <h4>ONG de Seguridad y Rescate</h4>
                            <p>Bomberos, Protección Civil, Defensa Civil, Cruz Roja</p>
                        </div>
                        <div class="type-card-radio">
                            <input type="radio" name="type" value="ngo_rescue" id="typeNgoRescue"
                                   {{ old('type', $company->type ?? '') == 'ngo_rescue' ? 'checked' : '' }} required>
                            <label for="typeNgoRescue">
                                <span class="radio-custom"></span>
                            </label>
                        </div>
                    </div>

                    <!-- Organización Gubernamental -->
                    <div class="type-card {{ old('type', $company->type ?? '') == 'government' ? 'active' : '' }}" data-type="government">
                        <div class="type-card-icon">
                            <i class="fas fa-landmark"></i>
                        </div>
                        <div class="type-card-info">
                            <h4>Organización Gubernamental</h4>
                            <p>Ministerios, Gobernaciones, Alcaldías, Asamblea Nacional</p>
                        </div>
                        <div class="type-card-radio">
                            <input type="radio" name="type" value="government" id="typeGovernment"
                                   {{ old('type', $company->type ?? '') == 'government' ? 'checked' : '' }} required>
                            <label for="typeGovernment">
                                <span class="radio-custom"></span>
                            </label>
                        </div>
                    </div>
                </div>
                @error('type')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Logo de la Institución -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-image"></i>
                    </div>
                    <div class="section-title">
                        <h3>Logo de la Institución</h3>
                        <p>Sube el logo que identificará a la institución</p>
                    </div>
                </div>

                <div class="logo-area">
                    <div class="logo-preview" id="logoPreview">
                        @if(isset($company) && $company->logo)
                            <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo">
                            <button type="button" class="remove-logo" onclick="removeLogo()">
                                <i class="fas fa-times"></i>
                            </button>
                        @else
                            <div class="logo-placeholder">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Sin logo</span>
                            </div>
                        @endif
                    </div>
                    <div class="logo-actions">
                        <input type="file" name="logo" id="logoInput" accept="image/*" style="display: none;">
                        <button type="button" class="btn-upload" onclick="document.getElementById('logoInput').click()">
                            <i class="fas fa-upload"></i> Seleccionar Logo
                        </button>
                        <small class="help-text">
                            <i class="fas fa-info-circle"></i> Formatos: JPG, PNG. Tamaño recomendado: 200x200px. Máx: 2MB
                        </small>
                    </div>
                </div>
                @error('logo')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Información Básica (Común para todos) -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="section-title">
                        <h3>Información Básica</h3>
                        <p>Datos principales de la institución</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="input-group full-width">
                        <label>
                            <i class="fas fa-building"></i>
                            <span>Nombre de la Institución *</span>
                        </label>
                        <input type="text" name="name" class="input-field @error('name') error @enderror"
                               value="{{ old('name', $company->name ?? '') }}" 
                               placeholder="Ej: Corporación ABC, Colegio San José, Bomberos Metropolitanos, Ministerio de Educación" required>
                        @error('name')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group">
                        <label>
                            <i class="fas fa-hashtag"></i>
                            <span>RIF / NIT</span>
                        </label>
                        <input type="text" name="tax_id" class="input-field @error('tax_id') error @enderror"
                               value="{{ old('tax_id', $company->tax_id ?? '') }}" 
                               placeholder="J-12345678-9">
                        @error('tax_id')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group">
                        <label>
                            <i class="fas fa-envelope"></i>
                            <span>Correo Electrónico</span>
                        </label>
                        <input type="email" name="email" class="input-field @error('email') error @enderror"
                               value="{{ old('email', $company->email ?? '') }}" 
                               placeholder="contacto@ejemplo.com">
                        @error('email')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group">
                        <label>
                            <i class="fas fa-phone"></i>
                            <span>Teléfono</span>
                        </label>
                        <input type="text" name="phone" class="input-field @error('phone') error @enderror"
                               value="{{ old('phone', $company->phone ?? '') }}" 
                               placeholder="(0212) 555-1234">
                        @error('phone')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group full-width">
                        <label>
                            <i class="fas fa-globe"></i>
                            <span>Sitio Web</span>
                        </label>
                        <input type="url" name="website" class="input-field @error('website') error @enderror"
                               value="{{ old('website', $company->website ?? '') }}" 
                               placeholder="https://www.ejemplo.com">
                        @error('website')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- SECCIÓN ESPECÍFICA PARA EMPRESA -->
            <!-- ========================================== -->
            <div class="form-section company-specific" style="display: {{ old('type', $company->type ?? '') == 'company' ? 'block' : 'none' }}">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="section-title">
                        <h3>Información de la Empresa</h3>
                        <p>Datos específicos del sector empresarial</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="input-group">
                        <label>
                            <i class="fas fa-industry"></i>
                            <span>Sector / Industria</span>
                        </label>
                        <select name="industry" class="input-field @error('industry') error @enderror">
                            <option value="">Seleccionar sector</option>
                            <option value="tecnologia" {{ old('industry', $company->industry ?? '') == 'tecnologia' ? 'selected' : '' }}>💻 Tecnología</option>
                            <option value="comercio" {{ old('industry', $company->industry ?? '') == 'comercio' ? 'selected' : '' }}>🛒 Comercio</option>
                            <option value="manufactura" {{ old('industry', $company->industry ?? '') == 'manufactura' ? 'selected' : '' }}>🏭 Manufactura</option>
                            <option value="servicios" {{ old('industry', $company->industry ?? '') == 'servicios' ? 'selected' : '' }}>🤝 Servicios</option>
                            <option value="construccion" {{ old('industry', $company->industry ?? '') == 'construccion' ? 'selected' : '' }}>🏗️ Construcción</option>
                            <option value="salud" {{ old('industry', $company->industry ?? '') == 'salud' ? 'selected' : '' }}>🏥 Salud</option>
                            <option value="finanzas" {{ old('industry', $company->industry ?? '') == 'finanzas' ? 'selected' : '' }}>💰 Finanzas</option>
                            <option value="otros" {{ old('industry', $company->industry ?? '') == 'otros' ? 'selected' : '' }}>📌 Otros</option>
                        </select>
                        @error('industry')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group">
                        <label>
                            <i class="fas fa-users"></i>
                            <span>Tamaño de la Empresa</span>
                        </label>
                        <select name="size" class="input-field @error('size') error @enderror">
                            <option value="">Seleccionar tamaño</option>
                            <option value="1-10" {{ old('size', $company->size ?? '') == '1-10' ? 'selected' : '' }}>1-10 empleados</option>
                            <option value="11-50" {{ old('size', $company->size ?? '') == '11-50' ? 'selected' : '' }}>11-50 empleados</option>
                            <option value="51-200" {{ old('size', $company->size ?? '') == '51-200' ? 'selected' : '' }}>51-200 empleados</option>
                            <option value="201-500" {{ old('size', $company->size ?? '') == '201-500' ? 'selected' : '' }}>201-500 empleados</option>
                            <option value="501+" {{ old('size', $company->size ?? '') == '501+' ? 'selected' : '' }}>Más de 500 empleados</option>
                        </select>
                        @error('size')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- SECCIÓN ESPECÍFICA PARA COLEGIO -->
            <!-- ========================================== -->
            <div class="form-section school-specific" style="display: {{ old('type', $company->type ?? '') == 'school' ? 'block' : 'none' }}">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="section-title">
                        <h3>Información del Colegio</h3>
                        <p>Datos específicos de la institución educativa</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="input-group full-width">
                        <label>
                            <i class="fas fa-chalkboard"></i>
                            <span>Niveles Educativos</span>
                        </label>
                        <div class="checkbox-group">
                            <label class="checkbox-card">
                                <input type="checkbox" name="levels[]" value="preschool" 
                                       {{ in_array('preschool', old('levels', $company->levels ?? [])) ? 'checked' : '' }}>
                                <span class="checkbox-card-content">
                                    <i class="fas fa-child"></i>
                                    <strong>Preescolar</strong>
                                </span>
                            </label>
                            <label class="checkbox-card">
                                <input type="checkbox" name="levels[]" value="primary" 
                                       {{ in_array('primary', old('levels', $company->levels ?? [])) ? 'checked' : '' }}>
                                <span class="checkbox-card-content">
                                    <i class="fas fa-book"></i>
                                    <strong>Primaria</strong>
                                    <small>1° a 6° grado</small>
                                </span>
                            </label>
                            <label class="checkbox-card">
                                <input type="checkbox" name="levels[]" value="middle" 
                                       {{ in_array('middle', old('levels', $company->levels ?? [])) ? 'checked' : '' }}>
                                <span class="checkbox-card-content">
                                    <i class="fas fa-flask"></i>
                                    <strong>Media General</strong>
                                    <small>7° a 9° grado</small>
                                </span>
                            </label>
                            <label class="checkbox-card">
                                <input type="checkbox" name="levels[]" value="high" 
                                       {{ in_array('high', old('levels', $company->levels ?? [])) ? 'checked' : '' }}>
                                <span class="checkbox-card-content">
                                    <i class="fas fa-university"></i>
                                    <strong>Diversificado</strong>
                                    <small>4° a 5° año</small>
                                </span>
                            </label>
                        </div>
                        @error('levels')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group">
                        <label>
                            <i class="fas fa-clock"></i>
                            <span>Jornadas</span>
                        </label>
                        <div class="checkbox-group inline">
                            <label class="checkbox-label">
                                <input type="checkbox" name="shifts[]" value="morning" 
                                       {{ in_array('morning', old('shifts', $company->shifts ?? [])) ? 'checked' : '' }}>
                                <span>🌅 Mañana</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="shifts[]" value="afternoon" 
                                       {{ in_array('afternoon', old('shifts', $company->shifts ?? [])) ? 'checked' : '' }}>
                                <span>🌇 Tarde</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="shifts[]" value="evening" 
                                       {{ in_array('evening', old('shifts', $company->shifts ?? [])) ? 'checked' : '' }}>
                                <span>🌙 Noche</span>
                            </label>
                        </div>
                        @error('shifts')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group full-width">
                        <label>
                            <i class="fas fa-user-tie"></i>
                            <span>Director / Directora</span>
                        </label>
                        <input type="text" name="principal" class="input-field @error('principal') error @enderror"
                               value="{{ old('principal', $company->principal ?? '') }}" 
                               placeholder="Nombre del director o directora">
                        @error('principal')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- SECCIÓN ESPECÍFICA PARA ONG DE RESCATE -->
            <!-- ========================================== -->
            <div class="form-section ngo-rescue-specific" style="display: {{ old('type', $company->type ?? '') == 'ngo_rescue' ? 'block' : 'none' }}">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-ambulance"></i>
                    </div>
                    <div class="section-title">
                        <h3>Información de la ONG de Rescate</h3>
                        <p>Datos específicos de la organización de seguridad y rescate</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="input-group">
                        <label>
                            <i class="fas fa-fire-extinguisher"></i>
                            <span>Tipo de Organización</span>
                        </label>
                        <select name="rescue_type" class="input-field @error('rescue_type') error @enderror">
                            <option value="">Seleccionar tipo</option>
                            <option value="firefighters" {{ old('rescue_type', $company->rescue_type ?? '') == 'firefighters' ? 'selected' : '' }}>🚒 Cuerpo de Bomberos</option>
                            <option value="civil_protection" {{ old('rescue_type', $company->rescue_type ?? '') == 'civil_protection' ? 'selected' : '' }}>🛡️ Protección Civil</option>
                            <option value="civil_defense" {{ old('rescue_type', $company->rescue_type ?? '') == 'civil_defense' ? 'selected' : '' }}>⚠️ Defensa Civil</option>
                            <option value="red_cross" {{ old('rescue_type', $company->rescue_type ?? '') == 'red_cross' ? 'selected' : '' }}>🔴 Cruz Roja</option>
                            <option value="first_aid" {{ old('rescue_type', $company->rescue_type ?? '') == 'first_aid' ? 'selected' : '' }}>🩺 Primeros Auxilios</option>
                            <option value="search_rescue" {{ old('rescue_type', $company->rescue_type ?? '') == 'search_rescue' ? 'selected' : '' }}>🔍 Búsqueda y Rescate</option>
                            <option value="emergency_medical" {{ old('rescue_type', $company->rescue_type ?? '') == 'emergency_medical' ? 'selected' : '' }}>🚑 Servicio Médico de Emergencia</option>
                        </select>
                        @error('rescue_type')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group">
                        <label>
                            <i class="fas fa-phone-alt"></i>
                            <span>Línea de Emergencia</span>
                        </label>
                        <input type="text" name="emergency_line" class="input-field @error('emergency_line') error @enderror"
                               value="{{ old('emergency_line', $company->emergency_line ?? '') }}" 
                               placeholder="Ej: 911, 171, 0800-EMERGENCIA">
                        @error('emergency_line')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group full-width">
                        <label>
                            <i class="fas fa-map-marked-alt"></i>
                            <span>Área de Cobertura</span>
                        </label>
                        <textarea name="coverage_area" class="input-field @error('coverage_area') error @enderror" 
                                  rows="2" placeholder="Municipios, parroquias o zonas que atiende...">{{ old('coverage_area', $company->coverage_area ?? '') }}</textarea>
                        @error('coverage_area')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- SECCIÓN ESPECÍFICA PARA GOBIERNO -->
            <!-- ========================================== -->
            <div class="form-section government-specific" style="display: {{ old('type', $company->type ?? '') == 'government' ? 'block' : 'none' }}">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-landmark"></i>
                    </div>
                    <div class="section-title">
                        <h3>Información Gubernamental</h3>
                        <p>Datos específicos de la organización gubernamental</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="input-group">
                        <label>
                            <i class="fas fa-layer-group"></i>
                            <span>Nivel del Gobierno</span>
                        </label>
                        <select name="government_level" class="input-field @error('government_level') error @enderror">
                            <option value="">Seleccionar nivel</option>
                            <option value="national" {{ old('government_level', $company->government_level ?? '') == 'national' ? 'selected' : '' }}>🏛️ Nacional</option>
                            <option value="regional" {{ old('government_level', $company->government_level ?? '') == 'regional' ? 'selected' : '' }}>🏢 Regional</option>
                            <option value="municipal" {{ old('government_level', $company->government_level ?? '') == 'municipal' ? 'selected' : '' }}>🏘️ Municipal</option>
                            <option value="parish" {{ old('government_level', $company->government_level ?? '') == 'parish' ? 'selected' : '' }}>📌 Parroquial</option>
                        </select>
                        @error('government_level')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group">
                        <label>
                            <i class="fas fa-balance-scale"></i>
                            <span>Rama del Poder Público</span>
                        </label>
                        <select name="government_branch" class="input-field @error('government_branch') error @enderror">
                            <option value="">Seleccionar rama</option>
                            <option value="executive" {{ old('government_branch', $company->government_branch ?? '') == 'executive' ? 'selected' : '' }}>⚡ Poder Ejecutivo</option>
                            <option value="legislative" {{ old('government_branch', $company->government_branch ?? '') == 'legislative' ? 'selected' : '' }}>📜 Poder Legislativo</option>
                            <option value="judicial" {{ old('government_branch', $company->government_branch ?? '') == 'judicial' ? 'selected' : '' }}>⚖️ Poder Judicial</option>
                            <option value="citizen" {{ old('government_branch', $company->government_branch ?? '') == 'citizen' ? 'selected' : '' }}>👁️ Poder Ciudadano</option>
                            <option value="electoral" {{ old('government_branch', $company->government_branch ?? '') == 'electoral' ? 'selected' : '' }}>🗳️ Poder Electoral</option>
                        </select>
                        @error('government_branch')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group full-width">
                        <label>
                            <i class="fas fa-building"></i>
                            <span>Tipo de Ente</span>
                        </label>
                        <select name="government_entity_type" class="input-field @error('government_entity_type') error @enderror">
                            <option value="">Seleccionar tipo de ente</option>
                            <option value="ministry" {{ old('government_entity_type', $company->government_entity_type ?? '') == 'ministry' ? 'selected' : '' }}>📋 Ministerio</option>
                            <option value="governorship" {{ old('government_entity_type', $company->government_entity_type ?? '') == 'governorship' ? 'selected' : '' }}>🏢 Gobernación</option>
                            <option value="mayoralty" {{ old('government_entity_type', $company->government_entity_type ?? '') == 'mayoralty' ? 'selected' : '' }}>🏘️ Alcaldía</option>
                            <option value="institute" {{ old('government_entity_type', $company->government_entity_type ?? '') == 'institute' ? 'selected' : '' }}>🏛️ Instituto Autónomo</option>
                            <option value="foundation" {{ old('government_entity_type', $company->government_entity_type ?? '') == 'foundation' ? 'selected' : '' }}>🎯 Fundación del Estado</option>
                            <option value="parliament" {{ old('government_entity_type', $company->government_entity_type ?? '') == 'parliament' ? 'selected' : '' }}>📜 Asamblea Nacional / Consejo Legislativo</option>
                            <option value="judicial_organ" {{ old('government_entity_type', $company->government_entity_type ?? '') == 'judicial_organ' ? 'selected' : '' }}>⚖️ Órgano Judicial</option>
                            <option value="military" {{ old('government_entity_type', $company->government_entity_type ?? '') == 'military' ? 'selected' : '' }}>⭐ Fuerza Armada</option>
                            <option value="police" {{ old('government_entity_type', $company->government_entity_type ?? '') == 'police' ? 'selected' : '' }}>👮 Cuerpo Policial</option>
                        </select>
                        @error('government_entity_type')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Ubicación (Común para todos) -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="section-title">
                        <h3>Ubicación</h3>
                        <p>Dirección física de la institución</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="input-group full-width">
                        <label>
                            <i class="fas fa-location-dot"></i>
                            <span>Dirección</span>
                        </label>
                        <textarea name="address" class="input-field @error('address') error @enderror" 
                                  rows="3" placeholder="Calle, número, urbanización...">{{ old('address', $company->address ?? '') }}</textarea>
                        @error('address')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group">
                        <label>
                            <i class="fas fa-city"></i>
                            <span>Ciudad</span>
                        </label>
                        <input type="text" name="city" class="input-field @error('city') error @enderror"
                               value="{{ old('city', $company->city ?? '') }}" 
                               placeholder="Caracas, Maracaibo, Valencia...">
                        @error('city')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group">
                        <label>
                            <i class="fas fa-map"></i>
                            <span>Estado / Provincia</span>
                        </label>
                        <input type="text" name="state" class="input-field @error('state') error @enderror"
                               value="{{ old('state', $company->state ?? '') }}" 
                               placeholder="Distrito Capital, Miranda, Zulia...">
                        @error('state')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group">
                        <label>
                            <i class="fas fa-mail-bulk"></i>
                            <span>Código Postal</span>
                        </label>
                        <input type="text" name="postal_code" class="input-field @error('postal_code') error @enderror"
                               value="{{ old('postal_code', $company->postal_code ?? '') }}" 
                               placeholder="1010, 4001...">
                        @error('postal_code')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group">
                        <label>
                            <i class="fas fa-flag"></i>
                            <span>País</span>
                        </label>
                        <input type="text" name="country" class="input-field @error('country') error @enderror"
                               value="{{ old('country', $company->country ?? 'Venezuela') }}" 
                               placeholder="Venezuela">
                        @error('country')
                            <span class="input-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Información Adicional (Común para todos) -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="section-title">
                        <h3>Información Adicional</h3>
                        <p>Detalles complementarios de la institución</p>
                    </div>
                </div>

                <div class="input-group full-width">
                    <label>
                        <i class="fas fa-align-left"></i>
                        <span>Descripción</span>
                    </label>
                    <textarea name="description" class="input-field @error('description') error @enderror" 
                              rows="4" placeholder="Breve descripción de la institución, su misión, visión, etc.">{{ old('description', $company->description ?? '') }}</textarea>
                    @error('description')
                        <span class="input-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="status-toggle">
                    <div class="toggle-info">
                        <i class="fas fa-power-off"></i>
                        <div>
                            <strong>Estado de la Institución</strong>
                            <small>Si está inactiva, no aparecerá en los selectores</small>
                        </div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_active" value="1" 
                               {{ old('is_active', $company->is_active ?? true) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                        <span class="toggle-label">Activo</span>
                    </label>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="form-actions">
                <a href="{{ route('admin.companies.index') }}" class="btn-cancel">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> {{ isset($company) ? 'Actualizar' : 'Registrar' }} Institución
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Manejar cambio de tipo
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const companySpecific = document.querySelector('.company-specific');
    const schoolSpecific = document.querySelector('.school-specific');
    const ngoRescueSpecific = document.querySelector('.ngo-rescue-specific');
    const governmentSpecific = document.querySelector('.government-specific');
    const heroTitle = document.querySelector('.hero-text h1');
    const heroSubtitle = document.querySelector('.hero-text p');
    
    function updateTypeFields() {
        const selectedType = document.querySelector('input[name="type"]:checked')?.value;
        
        // Ocultar todas las secciones específicas
        if (companySpecific) companySpecific.style.display = 'none';
        if (schoolSpecific) schoolSpecific.style.display = 'none';
        if (ngoRescueSpecific) ngoRescueSpecific.style.display = 'none';
        if (governmentSpecific) governmentSpecific.style.display = 'none';
        
        // Mostrar según selección
        if (selectedType === 'company') {
            if (companySpecific) companySpecific.style.display = 'block';
            if (heroTitle) heroTitle.textContent = 'Registrar Empresa';
            if (heroSubtitle) heroSubtitle.textContent = 'Complete la información de la empresa';
        } else if (selectedType === 'school') {
            if (schoolSpecific) schoolSpecific.style.display = 'block';
            if (heroTitle) heroTitle.textContent = 'Registrar Colegio';
            if (heroSubtitle) heroSubtitle.textContent = 'Complete la información del colegio';
        } else if (selectedType === 'ngo_rescue') {
            if (ngoRescueSpecific) ngoRescueSpecific.style.display = 'block';
            if (heroTitle) heroTitle.textContent = 'Registrar ONG de Rescate';
            if (heroSubtitle) heroSubtitle.textContent = 'Complete la información de la organización de rescate';
        } else if (selectedType === 'government') {
            if (governmentSpecific) governmentSpecific.style.display = 'block';
            if (heroTitle) heroTitle.textContent = 'Registrar Organización Gubernamental';
            if (heroSubtitle) heroSubtitle.textContent = 'Complete la información del ente gubernamental';
        }
        
        // Actualizar clases activas en las tarjetas
        document.querySelectorAll('.type-card').forEach(card => {
            const cardType = card.dataset.type;
            if (selectedType === cardType) {
                card.classList.add('active');
            } else {
                card.classList.remove('active');
            }
        });
    }
    
    typeRadios.forEach(radio => {
        radio.addEventListener('change', updateTypeFields);
    });
    
    // Click en tarjetas de tipo
    document.querySelectorAll('.type-card').forEach(card => {
        card.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                radio.dispatchEvent(new Event('change'));
            }
        });
    });
    
    // Previsualización de logo
    const logoInput = document.getElementById('logoInput');
    const logoPreview = document.getElementById('logoPreview');
    
    if (logoInput) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    logoPreview.innerHTML = `
                        <img src="${event.target.result}" alt="Vista previa">
                        <button type="button" class="remove-logo" onclick="removeLogo()">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    function removeLogo() {
        if (confirm('¿Eliminar el logo actual?')) {
            logoPreview.innerHTML = `
                <div class="logo-placeholder">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span>Sin logo</span>
                </div>
            `;
            
            // Limpiar el input file
            if (logoInput) logoInput.value = '';
            
            // Agregar campo oculto para eliminar logo
            let hiddenInput = document.querySelector('input[name="remove_logo"]');
            if (!hiddenInput) {
                hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'remove_logo';
                hiddenInput.value = '1';
                document.querySelector('form').appendChild(hiddenInput);
            }
        }
    }
    
    // Auto-cerrar alertas
    setTimeout(() => {
        document.querySelectorAll('.alert-glass').forEach(alert => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        });
    }, 5000);
    
    // Inicializar
    updateTypeFields();
</script>
@endpush