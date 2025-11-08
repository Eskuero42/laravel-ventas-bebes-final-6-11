@extends('layouts.admin-layout')
@section('contenido')
    <div class="row">
        <div class="col-xl-12">
            <div class="card crm-widget">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        Resumen del producto
                    </h5>
                    @if ($sucursal_articulo)
                        <button type="button" class="btn btn-success" data-bs-toggle="modal"
                            data-bs-target=".bs-example-modal-xl">
                            <i class="ri-add-circle-line align-middle me-1"></i> Registrar Articulos
                        </button>
                    @else
                        <button type="button" class="btn btn-success" disabled>
                            <i class="ri-add-circle-line align-middle me-1"></i> Registrar Articulos
                        </button>
                    @endif
                </div>

                <div class="card-body p-4">
                    <div class="row gy-4">
                        <div class="col-xl-4">
                            <div>
                                <h4 class="fw-semibold">{{ $sucursal_articulo->producto->nombre }}</h4>
                                <div class="bg-light rounded p-2 mt-3 text-center">
                                    <img src="{{ asset($sucursal_articulo->producto->imagen_principal) }}" alt="Producto"
                                        class="img-fluid rounded object-fit-contain" style="max-height: 180px;">
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-2">
                            <div>
                                <h6 class="text-muted text-uppercase fs-13 mb-2">Precio promedio</h6>
                                <p class="fs-5 fw-medium mb-4">Bs. {{ $sucursal_articulo->producto->precio }}</p>

                                <h6 class="text-muted text-uppercase fs-13 mb-2">Código</h6>
                                <p class="fs-5 fw-medium mb-4">{{ $sucursal_articulo->producto->codigo }}</p>
                            </div>
                        </div>

                        <div class="col-xl-6">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="card border shadow-sm">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0">Detalles del producto</h6>
                                                <div class="d-flex gap-1">
                                                    <button class="btn btn-sm btn-light btn-icon editDetalleBtn"
                                                        data-bs-toggle="modal" data-bs-target="#editarDetallesModal"
                                                        data-id="{{ $sucursal_articulo->producto->id }}"
                                                        data-titulo="{{ $sucursal_articulo->producto->nombre }}">
                                                        <i class="ri-pencil-fill"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-light" data-bs-toggle="modal"
                                                        data-bs-target="#nuevoDetalleModal" id="nuevoDetalleBtn">
                                                        <i class="ri-add-line"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            @if ($sucursal_articulo->producto->detalles->count())
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-hover table-nowrap mb-0">
                                                        <tbody>
                                                            @foreach ($sucursal_articulo->producto->detalles as $detalle)
                                                                <tr>
                                                                    <td class="fw-medium">{{ $detalle->titulo }}</td>
                                                                    <td>{{ $detalle->descripcion }}</td>
                                                                    <td class="text-end">
                                                                        <div class="d-flex gap-1 justify-content-end">
                                                                            <button
                                                                                class="btn btn-sm btn-light btn-icon editBtn-uno"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#editarUnDetalleModal"
                                                                                data-detalle='@json($detalle)'>
                                                                                <i class="ri-pencil-fill"></i>
                                                                            </button>
                                                                            <button
                                                                                class="btn btn-sm btn-light btn-icon text-danger delete-detalle-btn"
                                                                                data-id="{{ $detalle->id }}"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target=".confirmarEliminarDetalleModal">
                                                                                <i class="ri-delete-bin-5-line"></i>
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <p class="text-muted mb-0">Sin detalles registrados.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-12">
                                    <div class="card border shadow-sm">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0">Características</h6>
                                                <div class="d-flex gap-1">
                                                    <button class="btn btn-sm btn-light btn-icon editDetalleBtn"
                                                        data-bs-toggle="modal" data-bs-target="#editarCaracteristicasModal"
                                                        data-id="{{ $sucursal_articulo->producto->id }}"
                                                        data-titulo="{{ $sucursal_articulo->producto->nombre }}">
                                                        <i class="ri-pencil-fill"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-light" data-bs-toggle="modal"
                                                        data-bs-target="#nuevoCaracteristicaModal" id="nuevoDetalleBtn">
                                                        <i class="ri-add-line"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <!--CARACTERISTICA-->
                                            @if ($sucursal_articulo->producto->caracteristicas->count())
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-hover table-nowrap mb-0">
                                                        <tbody>
                                                            @foreach ($sucursal_articulo->producto->caracteristicas as $caracteristica)
                                                                <tr>
                                                                    <td style="width: 30px;">
                                                                        <img src="{{ asset('storage/' . $caracteristica->icono) }}"
                                                                            alt="Icono"
                                                                            class="img-fluid rounded object-fit-contain"
                                                                            style="max-height: 20px;">
                                                                    </td>
                                                                    <td>{{ $caracteristica->descripcion }}</td>
                                                                    <td class="text-end">
                                                                        <div class="d-flex gap-1 justify-content-end">
                                                                            <button
                                                                                class="btn btn-sm btn-light btn-icon edit-caracteristica-btn"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#editarCaracteristicaModal"
                                                                                data-caracteristica='@json($caracteristica)'>
                                                                                <i class="ri-pencil-fill"></i>
                                                                            </button>
                                                                            <button
                                                                                class="btn btn-sm btn-light btn-icon text-danger delete-caracteristica-btn"
                                                                                data-id="{{ $caracteristica->id }}"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#confirmarEliminarCaracteristicaModal">
                                                                                <i class="ri-delete-bin-5-line"></i>
                                                                            </button>

                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <p class="text-muted mb-0">Sin detalles registrados.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row mt-4">
                        <div class="col-xl-12">
                            <div class="bg-light p-4 rounded">
                                <h6 class="text-muted text-uppercase fs-13 mb-2">Descripción:</h6>
                                <p class="mb-0 fs-14">{{ $sucursal_articulo->producto->descripcion }}</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row gx-lg-10">
                        @foreach ($sucursal_articulos_agrupados as $grupo)
                            <div class="col-xl-6 mb-4 mt-2">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <h4>{{ $grupo['articulos']->first()['articulo']->nombre }}</h4>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div>
                                            <!--duplicar articulo-->
                                            <a class="btn btn-success duplicarArticuloBtn" data-bs-toggle="modal"
                                                data-bs-target="#compraArticuloModal"
                                                data-bs-grupo='@json($grupo)'
                                                data-bs-codigo="{{ $grupo['codigo'] }}" title="Derivado del articulo">
                                                <i class="ri-file-copy-2-fill align-middle me-1 fs-5"></i>
                                            </a>
                                            <!--añadir stock articulo-->
                                            <a class="btn btn-secondary ajustarStockBtn" data-bs-toggle="modal"
                                                data-bs-target="#ajustarStockModal"
                                                data-bs-obj='@json($grupo['articulos']->first())' title="Ajustar Stock">
                                                <i class="ri-stack-line align-middle me-1 fs-5"></i>
                                            </a>
                                            <a class="btn btn-warning editArticuloBtn" data-bs-toggle="modal"
                                                data-bs-target="#editArticuloModal"
                                                data-bs-obj='@json($grupo['articulos']->first())'
                                                data-bs-catalogos='@json($grupo['articulos']->first()['catalogos'])'
                                                data-product-category-type="{{ $grupo['articulos']->first()['categoria_tipo'] }}"
                                                title="Editar Articulo">
                                                <i class="ri-ball-pen-fill align-middle me-1 fs-5"></i>
                                            </a>
                                            <!--eliminar articulo-->
                                            <a class="btn btn-danger deleteArticuloBtn" href="#"
                                                data-bs-toggle="modal" data-bs-target="#deleteArticuloModal"
                                                data-ids="{{ json_encode($grupo['articulos']->pluck('articulo.id')->all()) }}"
                                                title="Eliminar artículo y variaciones">
                                                <i class="ri-delete-bin-5-fill align-middle me-1 fs-5"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-lg-4 col-sm-4">
                                        <div class="p-2 border border-dashed rounded">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <div class="avatar-title rounded bg-transparent text-info fs-24">
                                                        <i class="ri-money-dollar-circle-fill"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="text-muted mb-1">Precio:</p>
                                                    <h5 class="mb-0">Bs. {{ $grupo['articulos']->first()['precio'] }}
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end col -->
                                    <div class="col-lg-4 col-sm-4">
                                        <div class="p-2 border border-dashed rounded">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <div class="avatar-title rounded bg-transparent text-info fs-24">
                                                        <i class="ri-file-copy-2-fill"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="text-muted mb-1">Precio de descuento:</p>
                                                    <h5 class="mb-0">Bs.
                                                        {{ $grupo['articulos']->first()['descuento_habilitado'] ? $grupo['articulos']->first()['precio'] * (1 - $grupo['articulos']->first()['descuento_porcentaje'] / 100) : 'N/A' }}
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end col -->
                                    <div class="col-lg-4 col-sm-4">
                                        <div class="p-2 border border-dashed rounded">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <div class="avatar-title rounded bg-transparent text-info fs-24">
                                                        <i class="ri-stack-fill"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="text-muted mb-1">Stock:</p>
                                                    <h5 class="mb-0">{{ $grupo['articulos']->sum('stock') }}</h5>
                                                    <!-- Suma stock de todos los artículos -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-sm-7">
                                        @php
                                            $carouselId = 'carouselGrupo' . $loop->index;
                                            // El grupo ahora representa un único artículo con sus variaciones,
                                            // pero para las imágenes, todas pertenecen al mismo artículo base.
                                            // Tomamos el primer artículo del grupo para obtener las imágenes.
                                            $articuloParaImagenes = $grupo['articulos']->first();
                                            $imagenes = $articuloParaImagenes['imagenes'];
                                        @endphp
                                        <div id="{{ $carouselId }}" class="carousel slide" data-bs-ride="carousel">
                                            <div class="carousel-inner">
                                                @forelse ($imagenes as $index => $posicion)
                                                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}"
                                                        data-articulo-id="{{ $articuloParaImagenes['articulo']->id }}">
                                                        <img src="{{ asset($posicion->imagen) }}" class="d-block w-100"
                                                            alt="Imagen de artículo">
                                                    </div>
                                                @empty
                                                    <div class="carousel-item active">
                                                        <div class="d-flex justify-content-center align-items-center bg-light"
                                                            style="height: 200px;">
                                                            <span class="text-muted">Sin imagen</span>
                                                        </div>
                                                    </div>
                                                @endforelse
                                            </div>
                                            @if ($imagenes->count() > 1)
                                                <a class="carousel-control-prev" href="#{{ $carouselId }}"
                                                    role="button" data-bs-slide="prev">
                                                    <span class="carousel-control-prev-icon"></span>
                                                </a>
                                                <a class="carousel-control-next" href="#{{ $carouselId }}"
                                                    role="button" data-bs-slide="next">
                                                    <span class="carousel-control-next-icon"></span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-sm-5">
                                        <div class="row">
                                            @foreach ($grupo['articulos'] as $articuloEnGrupo)
                                                @php
                                                    $catalogosPorTipo = $articuloEnGrupo['catalogos']->groupBy(
                                                        'tipo.nombre',
                                                    );
                                                @endphp

                                                <div class="especificaciones-articulo"
                                                    data-articulo-id="{{ $articuloEnGrupo['articulo']->id }}"
                                                    style="display: {{ $loop->first ? 'block' : 'none' }};">
                                                    @foreach ($catalogosPorTipo as $nombreTipo => $catalogos)
                                                        <div class="mb-4">
                                                            <span
                                                                class="badge bg-light text-dark fw-semibold fs-13 px-3 py-2 rounded-2 mb-2 d-inline-block">
                                                                {{ $nombreTipo }}
                                                            </span>
                                                            <div class="mt-1">
                                                                @foreach ($catalogos as $catalogo)
                                                                    @if ($nombreTipo === 'Colores')
                                                                        <div class="d-flex align-items-center gap-2 mb-1">
                                                                            <span
                                                                                class="d-inline-block rounded-circle border"
                                                                                style="width: 20px; height: 20px; background-color: {{ preg_match('/^#[0-9A-Fa-f]{6}$/', $catalogo->especificacion->descripcion) ? $catalogo->especificacion->descripcion : '#ccc' }};"
                                                                                title="Color: {{ $catalogo->especificacion->descripcion }}"></span>
                                                                            <span
                                                                                class="text-muted fs-14">{{ $catalogo->especificacion->descripcion }}</span>
                                                                        </div>
                                                                    @else
                                                                        <div class="fs-14 text-muted mb-1">
                                                                            {{ $catalogo->especificacion->descripcion }}
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                    @if ($articuloEnGrupo['fecha_vencimiento'])
                                                        <div class="mb-4">
                                                            <span
                                                                class="badge bg-light text-dark fw-semibold fs-13 px-3 py-2 rounded-2 mb-2 d-inline-block">
                                                                Fecha de vencimiento
                                                            </span>
                                                            <div class="fs-14 text-muted mt-1">
                                                                {{ \Carbon\Carbon::parse($articuloEnGrupo['fecha_vencimiento'])->translatedFormat('d \d\e F \d\e Y') }}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- modal de registrar articulo -->
    <div class="modal fade bs-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">

                <div class="modal-header justify-content-center position-relative">
                    <h3 class="modal-title text-uppercase fw-bold text-success-emphasis text-center w-100"
                        id="myExtraLargeModalLabel">
                        <i class="ri-add-circle-fill align-middle me-1"></i> Registrar Articulo
                    </h3>
                    <button type="button" class="btn-close position-absolute end-0 top-50 translate-middle-y me-3"
                        data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form action="" id="addFormArticulo" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="sucursales_categorias_id"
                        value="{{ $sucursal_articulo->sucursales_categorias_id }}">
                    <input type="hidden" name="producto_id" value="{{ $sucursal_articulo->producto->id }}">
                    <div class="modal-body">

                        <div class="row g-3">
                            <div class="row mb-3">
                                <div class="col-xl-9">
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label for="codigo" class="form-label">Código</label>
                                            <input type="hidden" class="form-control " id="codigo" name="codigo"
                                                value="{{ old('codigo') }}" placeholder="Se generará automáticamente"
                                                readonly>
                                            <input type="text" class="form-control border-success-subtle"
                                                id="codigo_vista" value="Atomático" readonly>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="descuento-fijo" class="form-label">Descuento
                                                fijo</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text"><i
                                                        class="ri-money-dollar-circle-line"></i></span>
                                                <input type="number" name="descuento"
                                                    class="form-control border-success-subtle" id="descuento-fijo"
                                                    placeholder="Ej: 50.00" min="0" step="0.01"
                                                    value="0">
                                                <div class="invalid-feedback">Ingrese un valor válido</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="descuento-porcentaje" class="form-label">Descuento
                                                porcentual</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="ri-percent-line"></i></span>
                                                <input type="number" name="descuento_porcentaje"
                                                    class="form-control border-success-subtle" id="descuento-porcentaje"
                                                    placeholder="0%" min="0" max="100" value="0">
                                                <button class="btn btn-light" type="button" data-bs-toggle="dropdown">
                                                    <i class="ri-arrow-down-s-line"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    @foreach ([10, 20, 30, 40, 50, 60, 70, 80, 90] as $percent)
                                                        <li>
                                                            <a class="dropdown-item" href="#"
                                                                onclick="event.preventDefault(); document.getElementById('descuento-porcentaje').value = '{{ $percent }}'">
                                                                {{ $percent }}%
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="stock" class="form-label">Stock inicial</label>
                                            <input type="number" name="stock"
                                                class="form-control border-success-subtle" id="stock"
                                                placeholder="Ej: 10" min="0" value="0" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Nombre del artículo</label>

                                            <div class="form-check mb-2">
                                                <input class="form-check-input border-success-subtle" type="radio"
                                                    name="nombre_opcion" id="usar_nombre_producto" value="producto"
                                                    checked>
                                                <label class="form-check-label border-success-subtle"
                                                    for="usar_nombre_producto">
                                                    Usar nombre del producto:
                                                    <strong>{{ $sucursal_articulo->producto->nombre }}</strong>
                                                </label>
                                            </div>

                                            <div class="form-check mb-2">
                                                <input class="form-check-input border-success-subtle" type="radio"
                                                    name="nombre_opcion" id="usar_nombre_personalizado"
                                                    value="personalizado">
                                                <label class="form-check-label border-success-subtle"
                                                    for="usar_nombre_personalizado">
                                                    Escribir un nombre diferente
                                                </label>
                                            </div>

                                            <input type="hidden" name="nombre" id="nombre-final"
                                                value="{{ $sucursal_articulo->producto->nombre }}">

                                            <input type="text" class="form-control mt-2 d-none border-success-subtle"
                                                id="nombre-personalizado" placeholder="Nombre personalizado">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Precio del artículo</label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input border-success-subtle"
                                                            type="radio" name="precio_radio" id="precio_actual"
                                                            value="actual" checked>
                                                        <label class="form-check-label mb-2" for="precio_actual">
                                                            Precio actual
                                                        </label>
                                                        <input type="number" name="precio_actual"
                                                            class="form-control border-success-subtle"
                                                            value="{{ $sucursal_articulo->producto->precio }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input border-success-subtle"
                                                            type="radio" name="precio_radio" id="precio_nuevo"
                                                            value="nuevo">
                                                        <label class="form-check-label mb-2" for="precio_nuevo">Otro
                                                            precio</label>
                                                        <input type="number" name="precio_nuevo" value="0"
                                                            class="form-control border-success-subtle"
                                                            placeholder="introducir precio" step="0.01" min="0"
                                                            disabled>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            @if ($sucursal_articulo->producto->categoria && $sucursal_articulo->producto->categoria->tipo === 'no asignado')
                                                <div class="form-group">
                                                    <label for="fecha_vencimiento" class="form-label">Fecha de
                                                        vencimiento</label>
                                                    <input type="date" name="fecha_vencimiento" id="fecha_vencimiento"
                                                        class="form-control border-success-subtle" required>
                                                </div>
                                            @else
                                                <input type="hidden" name="fecha_vencimiento" value="">
                                            @endif
                                        </div>
                                    </div>


                                    <div class="row mb-3" id="imagenes-container">
                                        <!-- Aquí se agregarán los inputs dinámicamente -->
                                    </div>

                                    <div class="mb-3">
                                        <button type="button" class="btn btn-outline-success btn-sm"
                                            id="agregar-imagen">
                                            <i class="ri-add-line"></i> Agregar imagen
                                        </button>
                                    </div>
                                </div>

                                <div class="col-xl-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Elija las especificaciones del producto</label>
                                        @if ($sucursal_articulo->producto->producto_tipos->isNotEmpty())
                                            <div class="d-flex flex-wrap">
                                                @foreach ($sucursal_articulo->producto->producto_tipos as $productoTipo)
                                                    <div class="me-4 mb-4">
                                                        <div class="fw-bold mb-2">
                                                            {{ $productoTipo->tipo?->nombre ?? '—' }}
                                                        </div>
                                                        @foreach ($productoTipo->tipo->especificaciones as $especificacion)
                                                            <div class="form-check ms-3">
                                                                @php
                                                                    $esColor =
                                                                        $productoTipo->tipo->nombre === 'Colores';
                                                                    $inputType = $esColor ? 'radio' : 'checkbox';
                                                                    // Para radios: name único por tipo (sin [])
                                                                    // Para checkboxes: name con array ([])
                                                                    $inputName = $esColor
                                                                        ? "especificaciones[{$productoTipo->tipo->id}]"
                                                                        : "especificaciones[{$productoTipo->tipo->id}][]";
                                                                @endphp

                                                                <input class="form-check-input border-success-subtle"
                                                                    type="{{ $inputType }}"
                                                                    name="{{ $inputName }}"
                                                                    value="{{ $especificacion->id }}"
                                                                    id="spec_{{ $especificacion->id }}">

                                                                <label
                                                                    class="form-check-label d-flex align-items-center gap-2 border-success-subtle"
                                                                    for="spec_{{ $especificacion->id }}">
                                                                    @if ($esColor)
                                                                        <span class="d-inline-block rounded-circle border"
                                                                            style="width: 18px; height: 18px; background-color: {{ preg_match('/^#[0-9A-Fa-f]{6}$/', $especificacion->descripcion) ? $especificacion->descripcion : '#ccc' }};"
                                                                            title="Color: {{ $especificacion->descripcion }}"></span>
                                                                        <span
                                                                            class="text-muted">{{ $especificacion->descripcion }}</span>
                                                                    @else
                                                                        {{ $especificacion->descripcion }}
                                                                    @endif
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-muted">Este producto todavía no tiene tipos asociados.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>



                    </div>
                    <div class="modal-footer d-flex justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ri-close-line me-1"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-success addBtn">
                            <i class="ri-check-fill align-middle me-1"></i>
                            Registrar
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- modal de duplicar articulo -->

    <div class="modal fade" id="compraArticuloModal" tabindex="-1" role="dialog"
        aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header justify-content-center position-relative">
                    <h3 class="modal-title text-uppercase fw-bold text-success-emphasis text-center w-100"
                        id="myExtraLargeModalLabel">
                        <i class="ri-add-circle-fill align-middle me-1"></i> Duplicar Articulo
                    </h3>
                    <button type="button" class="btn-close position-absolute end-0 top-50 translate-middle-y me-3"
                        data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form action="" id="addFormDuplicarArticulo" enctype="multipart/form-data">
                    @csrf
                    {{-- Campos ocultos con datos del sucursal_articulo original --}}
                    <input type="hidden" name="sucursales_categorias_id"
                        value="{{ $sucursal_articulo->sucursales_categorias_id }}">
                    <input type="hidden" name="producto_id" value="{{ $sucursal_articulo->producto_id }}">
                    <input type="hidden" name="articulo_id" id="articulo_id_duplicar">
                    <input type="hidden" name="precio_actual" id="precio_actual-duplicar" value="">
                    <input type="hidden" name="codigo" id="codigo-duplicar" value="">
                    <input type="hidden" name="precio_radio" value="actual">
                    <input type="hidden" name="nombre" id="nombre-duplicar" value="">
                    <input type="hidden" name="stock" value="0"> {{-- Stock inicial en 0, se define en imágenes --}}
                    <input type="hidden" name="descuento" id="descuento-duplicar" value="">
                    <input type="hidden" name="descuento_porcentaje" id="descuento_porcentaje-duplicar" value="">
                    <input type="hidden" name="fecha_vencimiento" id="fecha_vencimiento-duplicar" value="">

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-xl-9">
                                {{-- Solo mostramos el contenedor de imágenes --}}
                                <div class="row mb-3 border-success-subtle" id="imagenes-container-duplicar">
                                    <!-- Inputs de imágenes se agregarán aquí dinámicamente -->
                                </div>
                                <div class="mb-3">
                                    <button type="button" class="btn btn-outline-success btn-sm"
                                        id="agregar-imagen-duplicar">
                                        <i class="ri-add-line"></i> Agregar imagen
                                    </button>
                                </div>
                            </div>

                            <div class="col-xl-3">
                                {{-- Especificaciones: se mantienen visibles --}}
                                <div class="col-md-12">
                                    <label class="form-label">Elija las especificaciones del producto</label>
                                    @if ($productoTipos->isNotEmpty())
                                        <div class="d-flex flex-wrap">
                                            @foreach ($productoTipos as $productoTipo)
                                                <div class="me-4 mb-4">
                                                    <div class="fw-bold mb-2">
                                                        {{ $productoTipo->tipo->nombre }}
                                                    </div>
                                                    @foreach ($productoTipo->tipo->especificaciones as $especificacion)
                                                        <div class="form-check ms-3 border-success-subtle">
                                                            @php
                                                                $esColor = $productoTipo->tipo->nombre === 'Colores';
                                                                $inputType = $esColor ? 'radio' : 'checkbox';
                                                                $inputName = $esColor
                                                                    ? "especificaciones[{$productoTipo->tipo->id}]"
                                                                    : "especificaciones[{$productoTipo->tipo->id}][]";
                                                            @endphp

                                                            <input class="form-check-input border-success-subtle"
                                                                type="{{ $inputType }}" name="{{ $inputName }}"
                                                                value="{{ $especificacion->id }}"
                                                                id="spec_{{ $especificacion->id }}">

                                                            <label class="form-check-label d-flex align-items-center gap-2"
                                                                for="spec_{{ $especificacion->id }}">
                                                                @if ($esColor)
                                                                    <span class="d-inline-block rounded-circle border"
                                                                        style="width: 18px; height: 18px; background-color: {{ preg_match('/^#[0-9A-Fa-f]{6}$/', $especificacion->descripcion) ? $especificacion->descripcion : '#ccc' }};"
                                                                        title="Color: {{ $especificacion->descripcion }}"></span>
                                                                    <span
                                                                        class="text-muted">{{ $especificacion->descripcion }}</span>
                                                                @else
                                                                    {{ $especificacion->descripcion }}
                                                                @endif
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted">Este producto no tiene tipos asociados.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer d-flex justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ri-close-line me-1"></i> Cancelar
                        </button>
                        <button type="submit" class="btn bg-success addaBtn">
                            <i class="ri-check-fill align-middle me-1"></i>
                            Registrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de registrar Detalles -->
    <div class="modal fade" id="nuevoDetalleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        <i class="ri-add-line me-1 align-middle text-success"></i>
                        Registrar compra
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <form id="formNuevoDetalle" action="#" method="POST">
                        @csrf
                        <input type="hidden" name="producto_id" value="{{ $sucursal_articulo->producto->id }}">

                        <div class="mb-3">
                            <label for="nuevoTitulo" class="form-label">Título</label>
                            <input type="text" class="form-control" name="titulo" id="nuevoTitulo"
                                placeholder="Título de detalle" required>
                        </div>
                        <div class="mb-3">
                            <label for="nuevaDescripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" id="nuevaDescripcion" rows="3"
                                placeholder="Descripción del detalle" required></textarea>
                        </div>
                    </form>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-link link-secondary fw-medium" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1 align-middle"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success" form="formNuevoDetalle">
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Editar Detalles -->
    <div class="modal fade bs-example-modal-lg" id="editarDetallesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        <i class="ri-pencil-fill me-1 align-middle text-warning"></i>
                        Editar Detalles del Producto
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <form id="formEditarDetalles" action="#" method="POST">
                        @csrf
                        <input type="hidden" name="producto_id" value="{{ $sucursal_articulo->producto->id }}">

                        <div class="row">
                            @foreach ($sucursal_articulo->producto->detalles as $i => $detalle)
                                <input type="hidden" name="detalles[{{ $i }}][id]"
                                    value="{{ $detalle->id }}">

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Título #{{ $i + 1 }}</label>
                                    <input type="text" name="detalles[{{ $i }}][titulo]"
                                        class="form-control" value="{{ $detalle->titulo }}" placeholder="Ej: Material">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea name="detalles[{{ $i }}][descripcion]" class="form-control" rows="2"
                                        placeholder="Ej: 100% algodón">{{ $detalle->descripcion }}</textarea>
                                </div>
                            @endforeach
                        </div>
                    </form>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-link link-secondary fw-medium" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1 align-middle"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" form="formEditarDetalles">
                        Guardar cambios
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- editar un detalle -->
    <div class="modal fade" id="editarUnDetalleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        <i class="ri-pencil-fill me-1 align-middle text-warning"></i>
                        Editar Detalle
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarUnDetalle" action="#" method="POST">
                        @csrf
                        <input type="hidden" name="id" id="editDetalleId">
                        <div class="mb-3">
                            <label for="editTitulo" class="form-label">Título</label>
                            <input type="text" class="form-control" name="titulo" id="editTitulo" required>
                        </div>
                        <div class="mb-3">
                            <label for="editDescripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" id="editDescripcion" rows="3" required></textarea>
                        </div>

                        <div class="modal-footer d-flex justify-content-end">
                            <button type="button" class="btn btn-link link-secondary fw-medium" data-bs-dismiss="modal">
                                <i class="ri-close-line me-1 align-middle"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-warning updateBtn-uno">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <!-- eliminar un Detalle -->
    <div class="modal fade confirmarEliminarDetalleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" id="deleteDetalleForm">
                    @csrf
                    <input type="hidden" name="id" id="deleteUnDetalleId">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="ri-alert-line align-middle me-2"></i>
                            Eliminar Detalle
                        </h5>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body text-center">
                        <i class="ri-error-warning-line text-danger display-4"></i>
                        <p class="mt-3">
                            ¿Está seguro de que desea eliminar la categoría?
                            <br>
                            <strong>Esta acción no se puede deshacer.</strong>
                        </p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ri-close-line align-middle me-1"></i>
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-danger btnDelete">
                            <i class="ri-delete-bin-line align-middle me-1"></i>
                            Eliminar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de registrar caracteristica -->
    <div class="modal fade" id="nuevoCaracteristicaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        <i class="ri-add-line me-1 align-middle text-success"></i>
                        Registrar nueva característica
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <form id="formNuevoCaracteristica" action="#" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="producto_id" value="{{ $sucursal_articulo->producto->id }}">

                        <div class="mb-3">
                            <label for="nuevoIcono" class="form-label">Ícono</label>
                            <input class="form-control" type="file" name="icono" id="nuevoIcono" required>
                        </div>

                        <div class="mb-3">
                            <label for="nuevaDescripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" id="nuevaDescripcion" rows="3"
                                placeholder="Descripción de la característica" required></textarea>
                        </div>
                    </form>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-link link-secondary fw-medium" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1 align-middle"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success" form="formNuevoCaracteristica">
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Editar caracteristicas -->
    <div class="modal fade bs-example-modal-lg" id="editarCaracteristicasModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        <i class="ri-pencil-fill me-1 align-middle text-warning"></i>
                        Editar Características del Producto
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <form id="formEditarCaracteristicas" action="#" method="POST">
                        @csrf
                        <input type="hidden" name="producto_id" value="{{ $sucursal_articulo->producto->id }}">

                        <div class="row">
                            @foreach ($sucursal_articulo->producto->caracteristicas as $i => $caracteristica)
                                <input type="hidden" name="caracteristicas[{{ $i }}][id]"
                                    value="{{ $caracteristica->id }}">

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Ícono #{{ $i + 1 }}</label>
                                    <input type="file" name="caracteristicas[{{ $i }}][icono]"
                                        class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea name="caracteristicas[{{ $i }}][descripcion]" class="form-control" rows="2">{{ $caracteristica->descripcion }}</textarea>
                                </div>
                            @endforeach
                        </div>
                    </form>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-link link-secondary fw-medium" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1 align-middle"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" form="formEditarCaracteristicas">
                        Guardar cambios
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- editar una caracteristica -->
    <div class="modal fade" id="editarCaracteristicaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        <i class="ri-pencil-fill me-1 align-middle text-warning"></i>
                        Editar Característica
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarCaracteristica" action="#" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" id="editCaracteristicaId">
                        <div class="mb-3">
                            <label for="editIcono" class="form-label">Ícono (Opcional)</label>
                            <input class="form-control" type="file" name="icono" id="editIcono">
                            <div class="mt-2">
                                <img id="iconoActual" src="" alt="Ícono actual" style="height: 40px;">
                            </div>

                        </div>

                        <div class="mb-3">
                            <label for="editCaracteristicaDescripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" id="editCaracteristicaDescripcion" rows="3" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-link link-secondary fw-medium" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1 align-middle"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning" form="formEditarCaracteristica">
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- eliminar una caracteristica -->
    <div class="modal fade" id="confirmarEliminarCaracteristicaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" id="deleteCaracteristicaForm">
                    @csrf
                    <input type="hidden" name="id" id="deleteCaracteristicaId">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="ri-alert-line align-middle me-2"></i>
                            Eliminar Característica
                        </h5>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body text-center">
                        <i class="ri-error-warning-line text-danger display-4"></i>
                        <p class="mt-3">
                            ¿Estás seguro de que quieres eliminar esta característica?
                            <br>
                            <strong>Esta acción no se puede deshacer.</strong>
                        </p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ri-close-line align-middle me-1"></i>
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-danger btnDeleteCaracteristica">
                            <i class="ri-delete-bin-line align-middle me-1"></i>
                            Eliminar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal eliminar articulo -->
    <div class="modal fade" id="deleteArticuloModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deleteArticuloForm">
                    @csrf
                    <input type="hidden" name="ids" id="deleteArticuloIds">

                    <div class="modal-header bg-soft-danger justify-content-center position-relative">
                        <h3 class="modal-title text-uppercase fw-bold text-danger-emphasis text-center w-100"
                            id="deleteRecordModalLabel">
                            <i class="ri-delete-bin-5-fill align-middle me-1"></i>
                            Confirmar Eliminación
                        </h3>
                        <button type="button" class="btn-close position-absolute end-0 top-50 translate-middle-y me-3"
                            data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="my-3">
                            <i class="ri-delete-bin-line display-4 text-danger"></i>
                            <p class="mt-3 mb-0 fs-5 text-muted">
                                ¿Estás seguro de que deseas eliminar este registro?
                            </p>
                            <p class="text-muted small">Esta acción no se puede deshacer.</p>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ri-close-line align-middle me-1"></i>
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-danger btnDeleteArticulo">
                            <i class="ri-delete-bin-line align-middle me-1"></i>
                            Eliminar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- modal de editar articulo -->
    <div class="modal fade" id="editArticuloModal" tabindex="-1" role="dialog"
        aria-labelledby="editArticuloModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header justify-content-center position-relative">
                    <h3 class="modal-title text-uppercase fw-bold text-warning-emphasis text-center w-100"
                        id="editArticuloModalLabel">
                        <i class="ri-ball-pen-fill align-middle me-1"></i> Editar Articulo
                    </h3>
                    <button type="button" class="btn-close position-absolute end-0 top-50 translate-middle-y me-3"
                        data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="" id="editFormArticulo" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="articulo_id" id="edit_articulo_id">
                    <input type="hidden" name="sucursal_articulo_id" id="edit_sucursal_articulo_id">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-xl-9">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="edit_nombre" class="form-label">Nombre del Artículo</label>
                                        <input type="text" name="nombre" class="form-control border-warning-subtle" id="edit_nombre"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_precio" class="form-label border-warning-subtle">Precio</label>
                                        <div class="input-group">
                                            <span class="input-group-text border-warning-subtle">Bs.</span>
                                            <input type="number" name="precio" class="form-control border-warning-subtle" id="edit_precio"
                                                step="0.01" min="0" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_stock" class="form-label border-warning-subtle">Stock</label>
                                        <input type="number" name="stock" class="form-control border-warning-subtle" id="edit_stock"
                                            min="0" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="edit_descuento" class="form-label">Descuento fijo</label>
                                        <div class="input-group">
                                            <span class="input-group-text border-warning-subtle">Bs.</span>
                                            <input type="number" name="descuento" class="form-control border-warning-subtle"
                                                id="edit_descuento" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_descuento_porcentaje" class="form-label">Descuento
                                            porcentual</label>
                                        <div class="input-group">
                                            <span class="input-group-text border-warning-subtle">%</span>
                                            <input type="number" name="descuento_porcentaje" class="form-control border-warning-subtle"
                                                id="edit_descuento_porcentaje" min="0" max="100">
                                        </div>
                                    </div>
                                    <div class="col-md-4" id="edit_fecha_vencimiento_container">
                                        <label for="edit_fecha_vencimiento" class="form-label border-warning-subtle">Fecha de
                                            vencimiento</label>
                                        <input type="date" name="fecha_vencimiento" id="edit_fecha_vencimiento"
                                            class="form-control border-warning-subtle">
                                    </div>
                                </div>

                                <h6 class="mb-3">Imágenes</h6>
                                <div class="row mb-3" id="edit_imagenes_container">
                                    <!-- las imagenes serán cargadas aqui por JavaScript -->
                                </div>
                                <div class="mb-3">
                                    <label for="edit_nuevas_imagenes" class="form-label ">Añadir nuevas imágenes</label>
                                    <input type="file" name="nuevas_imagenes[]" id="edit_nuevas_imagenes"
                                        class="form-control border-warning-subtle" multiple>
                                </div>
                            </div>

                            <div class="col-xl-3">
                                <label class="form-label">Especificaciones</label>
                                @if ($productoTipos->isNotEmpty())
                                    <div class="d-flex flex-wrap">
                                        @foreach ($productoTipos as $productoTipo)
                                            <div class="me-4 mb-4">
                                                <div class="fw-bold mb-2">
                                                    {{ $productoTipo->tipo?->nombre ?? '—' }}
                                                </div>
                                                @foreach ($productoTipo->tipo->especificaciones as $especificacion)
                                                    <div class="form-check ms-3">
                                                        @php
                                                            $esColor = $productoTipo->tipo->nombre === 'Colores';
                                                            $inputType = $esColor ? 'radio' : 'checkbox';
                                                            $inputName =
                                                                "especificaciones[{$productoTipo->tipo->id}]" .
                                                                ($esColor ? '' : '[]');
                                                        @endphp
                                                        <input class="form-check-input border-warning-subtle" type="{{ $inputType }}"
                                                            name="{{ $inputName }}" value="{{ $especificacion->id }}"
                                                            id="edit_spec_{{ $especificacion->id }}">
                                                        <label class="form-check-label d-flex align-items-center gap-2"
                                                            for="edit_spec_{{ $especificacion->id }}">
                                                            @if ($esColor)
                                                                <span class="d-inline-block rounded-circle border"
                                                                    style="width: 18px; height: 18px; background-color: {{ preg_match('/^#[0-9A-Fa-f]{6}$/', $especificacion->descripcion) ? $especificacion->descripcion : '#ccc' }};"></span>
                                                                <span
                                                                    class="text-muted">{{ $especificacion->descripcion }}</span>
                                                            @else
                                                                {{ $especificacion->descripcion }}
                                                            @endif
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted">Este producto no tiene tipos asociados.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ri-close-line me-1"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="ri-check-fill align-middle me-1"></i>Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Ajuste de Stock -->
    <div class="modal fade" id="ajustarStockModal" tabindex="-1" aria-labelledby="ajustarStockModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header justify-content-center position-relative">
                    <h3 class="modal-title text-uppercase fw-bold text-secondary-emphasis text-center w-100"
                        id="ajustarStockModalLabel">
                        <i class="ri-stack-line align-middle me-1"></i> Ajustar Stock
                    </h3>
                    <button type="button" class="btn-close position-absolute end-0 top-50 translate-middle-y me-3"
                        data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form id="ajustarStockForm">
                    @csrf
                    <input type="hidden" name="sucursal_articulo_id" id="ajuste_sucursal_articulo_id">
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <h4 class="mb-1" id="ajuste_articulo_nombre"></h4>
                            <p class="text-muted mb-0">Stock actual: <span class="fw-bold fs-4"
                                    id="ajuste_stock_actual"></span></p>
                        </div>
                        <div class="mb-3">
                            <label for="ajuste_cantidad" class="form-label">Cantidad a Mover</label>
                            <input type="number" class="form-control border-secondary-subtle" id="ajuste_cantidad" name="cantidad"
                                min="1" required>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ri-close-line me-1"></i>
                            Cancelar
                        </button>
                        <button type="submit" name="action" value="remove" class="btn btn-danger">
                            <i class="ri-indeterminate-circle-fill align-middle me-1 fs-5"></i>
                            Restar del Stock
                        </button>
                        <button type="submit" name="action" value="add" class="btn btn-success">
                            <i class="ri-add-box-fill align-middle me-1 fs-5"></i>
                            Añadir al Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // nombres personalizados
            const nombreOpcionProducto = document.getElementById('usar_nombre_producto');
            const nombreOpcionPersonalizado = document.getElementById('usar_nombre_personalizado');
            const nombrePersonalizadoInput = document.getElementById('nombre-personalizado');
            const nombreFinalInput = document.getElementById('nombre-final');
            const productoNombre = "{{ $sucursal_articulo->producto->nombre }}";

            function handleNombreChange() {
                if (nombreOpcionPersonalizado && nombreOpcionPersonalizado.checked) {
                    nombrePersonalizadoInput.classList.remove('d-none');
                    nombreFinalInput.value = nombrePersonalizadoInput.value;
                } else {
                    nombrePersonalizadoInput.classList.add('d-none');
                    nombreFinalInput.value = productoNombre;
                }
            }

            if (nombreOpcionProducto && nombreOpcionPersonalizado) {
                nombreOpcionProducto.addEventListener('change', handleNombreChange);
                nombreOpcionPersonalizado.addEventListener('change', handleNombreChange);
                nombrePersonalizadoInput.addEventListener('input', () => {
                    if (nombreOpcionPersonalizado.checked) {
                        nombreFinalInput.value = nombrePersonalizadoInput.value;
                    }
                });
                handleNombreChange();
            }

            // precio
            const precioActualRadio = document.getElementById('precio_actual');
            const precioNuevoRadio = document.getElementById('precio_nuevo');
            const precioNuevoInput = document.querySelector('input[name="precio_nuevo"]');

            function handlePrecioChange() {
                if (precioNuevoInput) {
                    precioNuevoInput.disabled = !precioNuevoRadio.checked;
                }
            }

            if (precioActualRadio && precioNuevoRadio) {
                precioActualRadio.addEventListener('change', handlePrecioChange);
                precioNuevoRadio.addEventListener('change', handlePrecioChange);
                handlePrecioChange();
            }

            // imagenes dinamicas
            const container = document.getElementById('imagenes-container');
            const botonAgregar = document.getElementById('agregar-imagen');
            let contador = 0;

            function crearCampoImagen() {
                contador++;
                const colDiv = document.createElement('div');
                colDiv.className = 'col-md-4';
                colDiv.innerHTML = `
                    <div class="mb-3 imagen-item">
                        <label for="imagen_${contador}" class="form-label">Imagen ${contador}
                            <button type="button" class="btn btn-sm btn-outline-danger ms-2 quitar-imagen">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </label>
                        <input type="file" name="imagen[]" id="imagen_${contador}" class="form-control" accept="image/*">
                    </div>
                `;
                container.appendChild(colDiv);

                colDiv.querySelector('.quitar-imagen').addEventListener('click', function() {
                    container.removeChild(colDiv);
                });
            }

            if (botonAgregar) {
                botonAgregar.addEventListener('click', crearCampoImagen);
            }

            // carrusel
            document.querySelectorAll('.carousel').forEach(function(carouselEl) {
                carouselEl.addEventListener('slid.bs.carousel', function() {
                    const activeSlide = carouselEl.querySelector('.carousel-item.active');
                    const articuloId = activeSlide.getAttribute('data-articulo-id');

                    const especificacionesContainer = carouselEl.closest('.row.mt-3').querySelector(
                        '.col-sm-5 .row');

                    especificacionesContainer.querySelectorAll('.especificaciones-articulo')
                        .forEach(function(el) {
                            el.style.display = 'none';
                        });

                    const especificacionesActivo = especificacionesContainer.querySelector(
                        `[data-articulo-id="${articuloId}"]`);
                    if (especificacionesActivo) {
                        especificacionesActivo.style.display = 'block';
                    }
                });
            });

            // modal
            const compraModal = document.getElementById('compraArticuloModal');
            if (compraModal) {
                compraModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const grupo = JSON.parse(button.getAttribute('data-bs-grupo'));

                    // obtener IDs de especificaciones
                    const usadas = new Set();
                    grupo.articulos.forEach(art => {
                        art.catalogos.forEach(cat => {
                            usadas.add(cat.especificacion_id);
                        });
                    });

                    // deshabilitar especificaciones ya usadas
                    document.querySelectorAll('#compraArticuloModal input[name^="especificaciones"]')
                        .forEach(input => {
                            input.disabled = false;
                            input.parentElement.classList.remove('text-muted');
                            input.parentElement.removeAttribute('title');
                        });

                    // carga los dato del articulo para duplicar
                    if (grupo) {
                        const articuloOriginal = grupo.articulos[0];
                        const articuloIdInput = document.getElementById('articulo_id_duplicar');
                        const precioInput = document.getElementById('precio_actual-duplicar');
                        const nombreInput = document.getElementById('nombre-duplicar');
                        const descuentoInput = document.getElementById('descuento-duplicar');
                        const descuentoPorcentajeInput = document.getElementById(
                            'descuento_porcentaje-duplicar');
                        const fechaVencimientoInput = document.getElementById('fecha_vencimiento-duplicar');

                        if (articuloIdInput) articuloIdInput.value = articuloOriginal.articulo.id;
                        if (precioInput) precioInput.value = articuloOriginal.precio;
                        if (nombreInput) nombreInput.value = articuloOriginal.articulo.nombre;
                        if (descuentoInput) descuentoInput.value = articuloOriginal.descuento || 0;
                        if (descuentoPorcentajeInput) descuentoPorcentajeInput.value = articuloOriginal
                            .descuento_porcentaje || 0;
                        if (fechaVencimientoInput) fechaVencimientoInput.value = articuloOriginal
                            .fecha_vencimiento || '';
                    }

                    const codigo = button.getAttribute('data-bs-codigo');
                    const codigoInput = document.getElementById('codigo-duplicar');
                    if (codigoInput) codigoInput.value = codigo || '';
                });

                // duplicar imagenes
                compraModal.addEventListener('shown.bs.modal', function() {
                    const containerDuplicar = document.getElementById('imagenes-container-duplicar');
                    const btnDuplicar = document.getElementById('agregar-imagen-duplicar');

                    if (!containerDuplicar || !btnDuplicar) return;

                    containerDuplicar.innerHTML = '';
                    agregarImagenDuplicar(containerDuplicar);

                    btnDuplicar.onclick = () => agregarImagenDuplicar(containerDuplicar);
                });
            }

            function agregarImagenDuplicar(container) {
                const index = container.children.length + 1;
                const div = document.createElement('div');
                div.className = 'col-md-4 mb-2';
                div.innerHTML = `
                    <label class="form-label">Imagen ${index}</label>
                    <input type="file" name="imagenes[]" class="form-control" accept="image/*" required>
                    <button type="button" class="btn btn-sm btn-outline-danger mt-1 remove-img">Eliminar</button>
                `;
                container.appendChild(div);

                div.querySelector('.remove-img').onclick = () => {
                    container.removeChild(div);
                    // Reordenar números
                    Array.from(container.children).forEach((el, i) => {
                        el.querySelector('label').textContent = `Imagen ${i + 1}`;
                    });
                };
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            // registrar articulo
            $('#addFormArticulo').submit(function(e) {
                e.preventDefault();
                $('.addBtn').prop('disabled', true);

                let formData = new FormData(this);
                formData.append('producto_id', "{{ $sucursal_articulo->producto->id }}");
                formData.set('nombre', document.getElementById('nombre-final').value);

                $.ajax({
                    url: "{{ route('admin.articulos.sucursales.registrar') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        alert(res.message);
                        if (res.success) {
                            $('#addFormArticulo')[0].reset();
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.message || 'Error al guardar el artículo.');
                    },
                    complete: function() {
                        $('.addBtn').prop('disabled', false);
                    }
                });
            });

            // duplucar artituclo
            $('#addFormDuplicarArticulo').submit(function(e) {
                e.preventDefault();
                $('.addaBtn').prop('disabled', true);

                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('admin.articulos.sucursales.duplicar') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        alert(res.message);
                        if (res.success) {
                            $('#addFormArticulo')[0].reset();
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.message || 'Error al guardar el artículo.');
                    },
                    complete: function() {
                        $('.addaBtn').prop('disabled', false);
                    }
                });
            });

            // registrar un detalle
            $('#formNuevoDetalle').submit(function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                $.ajax({
                    url: "{{ route('admin.registrar.detalles') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        alert(res.message);
                        if (res.success) {
                            $('#nuevoDetalleModal').modal('hide');
                            $('#formNuevoDetalle')[0].reset();
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.message || 'Error al registrar el detalle');
                    }
                });
            });

            // editar varios detalles
            $('#formEditarDetalles').submit(function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                $.ajax({
                    url: "{{ route('admin.editar.detalles') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        alert(res.message);
                        if (res.success) {
                            $('#editarDetallesModal').modal('hide');
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.message || 'Error al guardar detalles');
                    }
                });
            });

            // editar un detalle 
            $('.editBtn-uno').click(function() {
                var data = $(this).data('detalle');
                $('#editDetalleId').val(data.id);
                $('#editTitulo').val(data.titulo);
                $('#editDescripcion').val(data.descripcion);
            });

            $('#formEditarUnDetalle').submit(function(e) {
                e.preventDefault();
                $('.updateBtn-uno').prop('disabled', true);
                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('admin.editar.detalle') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        alert(res.message);
                        $('.updateBtn-uno').prop('disabled', false);
                        if (res.success) {
                            location.reload();
                        }
                    }
                });
            });

            // eliminar un detalle
            $('.delete-detalle-btn').click(function() {
                var id = $(this).data('id');
                $('#deleteUnDetalleId').val(id);
            });

            $('#deleteDetalleForm').submit(function(e) {
                e.preventDefault();
                $('.btnDelete').prop('disabled', true);

                $.ajax({
                    url: "{{ route('admin.eliminar.detalle') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        alert(res.message);
                        $('.btnDelete').prop('disabled', false);
                        if (res.success) {
                            location.reload();
                        }
                    }
                });
            });

            // registrar caracteristicas
            $('#formNuevoCaracteristica').submit(function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                $.ajax({
                    url: "{{ route('admin.registrar.caracteristicas') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        alert(res.message);
                        if (res.success) {
                            $('#nuevoCaracteristicaModal').modal('hide');
                            $('#formNuevoCaracteristica')[0].reset();
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.message || 'Error al registrar característica');
                    }
                });
            });

            // editar varias caracteristicas
            $('#formEditarCaracteristicas').submit(function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                $.ajax({
                    url: "{{ route('admin.editar.caracteristicas') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        alert(res.message);
                        if (res.success) {
                            $('#editarCaracteristicasModal').modal('hide');
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.message || 'Error al guardar características');
                    }
                });
            });

            // editar una caracteristica
            $('.edit-caracteristica-btn').click(function() {
                var data = $(this).data('caracteristica');
                $('#editCaracteristicaId').val(data.id);
                $('#editCaracteristicaDescripcion').val(data.descripcion);
                $('#iconoActual').attr('src', data.icono ?? '').toggle(!!data.icono);
            });

            $('#formEditarCaracteristica').submit(function(e) {
                e.preventDefault();
                $('.btn-warning').prop('disabled', true);
                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('admin.editar.caracteristica') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        alert(res.message);
                        $('.btn-warning').prop('disabled', false);
                        if (res.success) {
                            location.reload();
                        }
                    }
                });
            });

            // eliminar una caracteristica
            $('.delete-caracteristica-btn').click(function() {
                $('#deleteCaracteristicaId').val($(this).data('id'));
            });

            $('#deleteCaracteristicaForm').submit(function(e) {
                e.preventDefault();
                $('.btnDeleteCaracteristica').prop('disabled', true);

                $.ajax({
                    url: "{{ route('admin.eliminar.caracteristica') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        alert(res.message);
                        $('.btnDeleteCaracteristica').prop('disabled', false);
                        if (res.success) {
                            location.reload();
                        }
                    }
                });
            });

            // eliminar arituculo
            $('.deleteArticuloBtn').click(function() {
                $('#deleteArticuloIds').val(JSON.stringify($(this).data('ids')));
            });

            $('#deleteArticuloForm').submit(function(e) {
                e.preventDefault();
                $('.btnDeleteArticulo').prop('disabled', true);

                $.ajax({
                    url: "{{ route('admin.articulos.eliminar') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        alert(res.message);
                        if (res.success) {
                            location.reload();
                        } else {
                            $('.btnDeleteArticulo').prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.message || 'Error al eliminar');
                        $('.btnDeleteArticulo').prop('disabled', false);
                    }
                });
            });

            // editar articulo - llena datos modal
            $('.editArticuloBtn').on('click', function() {
                const articulo = $(this).data('bs-obj');
                const catalogos = $(this).data('bs-catalogos');
                const productCategoryType = $(this).data('product-category-type');

                $('#edit_articulo_id').val(articulo.articulo.id);
                $('#edit_sucursal_articulo_id').val(articulo.id);
                $('#edit_nombre').val(articulo.articulo.nombre);
                $('#edit_precio').val(articulo.precio);
                $('#edit_stock').val(articulo.stock);
                $('#edit_descuento').val(articulo.descuento);
                $('#edit_descuento_porcentaje').val(articulo.descuento_porcentaje);
                $('#edit_fecha_vencimiento').val(articulo.fecha_vencimiento);

                if (productCategoryType === 'Otros') {
                    $('#edit_fecha_vencimiento_container').show();
                } else {
                    $('#edit_fecha_vencimiento_container').hide();
                    $('#edit_fecha_vencimiento').val('');
                }

                const editImagenesContainer = $('#edit_imagenes_container');
                editImagenesContainer.empty();

                if (articulo.imagenes && articulo.imagenes.length > 0) {
                    articulo.imagenes.forEach(function(imagen, index) {
                        const imageUrl = `{{ asset('') }}${imagen.imagen}`;
                        editImagenesContainer.append(`
                            <div class="col-md-4 mb-3 image-item-edit" data-image-id="${imagen.id}">
                                <div class="card mb-0">
                                    <div class="card-body p-2">
                                        <img src="${imageUrl}" class="img-fluid mb-2" alt="Imagen ${index + 1}" style="max-height: 150px; width: 100%; object-fit: contain;">
                                        <button type="button" class="btn btn-danger btn-sm w-100 delete-existing-image" data-image-id="${imagen.id}">Eliminar</button>
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                } else {
                    editImagenesContainer.append('<p class="text-muted">No hay imágenes existentes.</p>');
                }

                editImagenesContainer.off('click', '.delete-existing-image').on('click',
                    '.delete-existing-image',
                    function() {
                        const imageId = $(this).data('image-id');
                        if (confirm('¿Estás seguro de que quieres eliminar esta imagen?')) {
                            $(this).closest('.image-item-edit').remove();

                            let deletedImagesInput = $(
                                '#editFormArticulo input[name="deleted_images"]');
                            if (deletedImagesInput.length === 0) {
                                deletedImagesInput = $('<input type="hidden" name="deleted_images">');
                                $('#editFormArticulo').append(deletedImagesInput);
                            }
                            let deletedImages = deletedImagesInput.val() ? JSON.parse(deletedImagesInput
                                .val()) : [];
                            deletedImages.push(imageId);
                            deletedImagesInput.val(JSON.stringify(deletedImages));
                        }
                    });

                $('#editArticuloModal input[name^="especificaciones"]').prop('checked', false);

                if (catalogos && catalogos.length > 0) {
                    catalogos.forEach(function(catalogo) {
                        const specId = catalogo.especificacion_id;
                        const inputElement = $(`#edit_spec_${specId}`);
                        if (inputElement.length) {
                            inputElement.prop('checked', true);
                        }
                    });
                }
            });

            // editar articulo
            $('#editFormArticulo').submit(function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                const deletedImagesInput = $('#editFormArticulo input[name="deleted_images"]');
                if (deletedImagesInput.length && deletedImagesInput.val()) {
                    formData.append('deleted_images', deletedImagesInput.val());
                }

                $.ajax({
                    url: "{{ route('admin.articulos.actualizar') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        alert(res.message);
                        if (res.success) {
                            $('#editArticuloModal').modal('hide');
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.message || 'Error al actualizar el artículo.');
                    }
                });
            });

            // ajustar stock
            $('.ajustarStockBtn').on('click', function() {
                const articuloData = $(this).data('bs-obj');
                $('#ajuste_sucursal_articulo_id').val(articuloData.id);
                $('#ajuste_articulo_nombre').text(articuloData.articulo.nombre);
                $('#ajuste_stock_actual').text(articuloData.stock);
                $('#ajustarStockForm')[0].reset();
            });

            $('#ajustarStockForm').submit(function(e) {
                e.preventDefault();

                const action = $(document.activeElement).val();
                if (action !== 'add' && action !== 'remove') return;

                const form = $(this);
                const submitButton = $(document.activeElement);
                const originalButtonText = submitButton.html();

                form.find('button[type="submit"]').prop('disabled', true);
                submitButton.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
                );

                const formData = new FormData(this);
                formData.set('action', action);

                $.ajax({
                    url: "{{ route('admin.articulos.ajustar-stock') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        alert(res.message);
                        if (res.success) {
                            $('#ajustarStockModal').modal('hide');
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.message || 'Ocurrió un error.');
                    },
                    complete: function() {
                        form.find('button[type="submit"]').prop('disabled', false);
                        submitButton.html(originalButtonText);
                    }
                });
            });
        });
    </script>
@endpush
