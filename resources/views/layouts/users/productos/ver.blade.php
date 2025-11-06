@extends('layouts.layout')
@section('contenido')
    <!-- Content ============================================= -->
    <section id="content">
        <div class="content-wrap">
            <div class="container">

                <div class="row gx-5 col-mb-80">
                    <!-- Gallery ============================================= -->
                    <main class="postcontent col-lg-8">

                        <div class="fslider" data-pagi="false" data-animation="fade">
                            <div class="flexslider">
                                <div class="slider-wrap">
                                    @if ($articulo_seleccionado->articulo && $articulo_seleccionado->articulo->posiciones->isNotEmpty())
                                        @foreach ($articulo_seleccionado->articulo->posiciones as $posicion)
                                            <div class="slide">
                                                <img src="{{ asset($posicion->imagen) }}"
                                                    alt="Imagen de {{ $articulo_seleccionado->articulo->nombre }}">
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="slide">
                                            <img src="{{ asset($producto->imagen_principal) }}"
                                                alt="Imagen de {{ $producto->nombre }}">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </main>

                    <!-- Product Info ============================================= -->
                    <aside class="sidebar col-lg-4">
                        <div class="bg-light rounded p-4 shadow-sm">

                            <h2 id="article-name" class="mb-2 fw-bold text-uppercase text-baby-sky">
                                {{ $articulo_seleccionado->articulo->nombre ?? $producto->nombre }}</h2>

                            <p class="mb-3 fs-5 text-dark">{{ $producto->descripcion }}</p>

                            <div id="price-container" class="mb-3">
                                @if ($descuento_aplicado > 0)
                                    <span class="fs-4 fw-bold text-danger">Bs. {{ number_format($precio_final, 2) }}</span>
                                    <del class="text-muted ms-2">Bs. {{ number_format($precio_base, 2) }}</del>
                                    @if ($es_porcentaje)
                                        <span
                                            class="badge bg-danger text-white ms-2">-{{ $articulo_seleccionado->descuento }}%</span>
                                    @endif
                                @else
                                    <span class="fs-4 fw-bold text-danger">Bs. {{ number_format($precio_final, 2) }}</span>
                                @endif
                                <p class="small text-muted mb-0">Estado: {{ $articulo_seleccionado->estado }}</p>
                            </div>

                            <div id="stock-container" class="mb-3"></div>

                            <hr>

                            {{-- Colores --}}
                            <h5 class="fw-medium mb-3">Seleccionar Color:</h5>
                            <div class="d-flex flex-wrap">
                                @foreach ($todos_los_articulos as $articulo_variante)
                                    @php
                                        $imagen_selector =
                                            $articulo_variante->articulo->posiciones->first()->imagen ??
                                            $producto->imagen_principal;
                                    @endphp
                                    <button
                                        class="btn border-2 p-1 me-2 mb-2 variant-selector @if ($articulo_variante->id == $articulo_seleccionado->id) border-primary @endif"
                                        data-sa-id="{{ $articulo_variante->id }}">
                                        <img src="{{ asset($imagen_selector) }}" alt="Selector de color"
                                            style="width: 40px; height: 40px; object-fit: cover;">
                                    </button>
                                @endforeach
                            </div>
                            <hr>

                            {{-- Tallas --}}
                            <div id="tallas-container">
                                @if (isset($tallas) && $tallas->isNotEmpty())
                                    <h5 class="fw-medium mb-3">Seleccionar Talla:</h5>
                                    <div class="d-flex flex-wrap">
                                        @foreach ($tallas as $talla)
                                            <input type="radio" class="btn-check" name="talla"
                                                id="talla-{{ $talla->id }}" value="{{ $talla->id }}"
                                                autocomplete="off" @if ($loop->first) checked @endif>
                                            <label class="btn btn-outline-secondary me-2 mb-2"
                                                for="talla-{{ $talla->id }}">{{ $talla->descripcion }}</label>
                                        @endforeach
                                    </div>
                                    <hr>
                                @endif
                            </div>

                            {{-- Capacidades --}}
                            <div id="capacidades-container">
                                @if (isset($capacidades) && $capacidades->isNotEmpty())
                                    <h5 class="fw-medium mb-3">Seleccionar Capacidad:</h5>
                                    <div class="d-flex flex-wrap">
                                        @foreach ($capacidades as $capacidad)
                                            <input type="radio" class="btn-check" name="capacidad"
                                                id="capacidad-{{ $capacidad->id }}" value="{{ $capacidad->id }}"
                                                autocomplete="off" @if ($loop->first) checked @endif>
                                            <label class="btn btn-outline-secondary me-2 mb-2"
                                                for="capacidad-{{ $capacidad->id }}">{{ $capacidad->descripcion }}</label>
                                        @endforeach
                                    </div>
                                    <hr>
                                @endif
                            </div>

                            <button
                                class="bg-baby-gold w-100 bg-button-baby-gold text-white fw-semibold hover-baby-gold px-4 py-2 border-0">
                                AÑADIR A LA BOLSA
                            </button>

                            <hr>

                            <!-- caracteristicas -->
                            <div class="row col-mb-50 mb-0 gx-5">
                                @if ($producto->caracteristicas->isNotEmpty())
                                    @foreach ($producto->caracteristicas as $caracteristica)
                                        <div class="col-sm-6 col-lg-4">
                                            <div
                                                class="feature-box fbox-center fbox-effect p-4 bg-baby-light rounded shadow-sm h-100 text-center">
                                                <div class="mb-3">
                                                    <img src="{{ asset($caracteristica->icono) }}" alt="icono"
                                                        class="img-fluid" style="width: 60px; height: 60px;">
                                                </div>
                                                <div class="fbox-content">
                                                    <h6 class="mb-0 fw-semibold text-dark">
                                                        {{ $caracteristica->descripcion }}
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="alert alert-info text-center fw-semibold my-4">
                                        Aún no se han registrado características para este producto.
                                    </div>
                                @endif
                            </div>

                            <hr>

                            <!-- detalles -->
                            <div class="accordion accordion-bg accordion-border mb-0" id="accordionFlushExample">
                                @if ($producto->detalles->isNotEmpty())
                                    @foreach ($producto->detalles as $detalle)
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="flush-heading{{ $detalle }}">
                                                <button
                                                    class="accordion-button collapsed bg-light text-dark fw-semibold px-4 py-3 rounded"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#flush-collapse{{ $detalle }}"
                                                    aria-expanded="false"
                                                    aria-controls="flush-collapse{{ $detalle }}">
                                                    {{ $detalle->titulo }}
                                                </button>
                                            </h2>
                                            <div id="flush-collapse{{ $detalle }}" class="accordion-collapse collapse"
                                                aria-labelledby="flush-heading{{ $detalle }}"
                                                data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body px-4 py-3 bg-light border-top">
                                                    <p class="mb-2">{{ $detalle->descripcion }}</p>

                                                    @if ($detalle->imagen)
                                                        <img src="{{ asset($detalle->imagen) }}" alt="imagen detalle"
                                                            class="img-thumbnail mt-3 rounded" style="max-height: 220px;">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="alert alert-info text-center fw-semibold my-4">
                                        Aún no se han registrado detalles para este producto.
                                    </div>
                                @endif
                            </div>

                        </div>
                    </aside>
                </div>

            </div>
        </div>
    </section><!-- #content end -->
@endsection

@push('scripts')
    <script>
        window.ASSET_URL = "{{ rtrim(asset(''), '/') }}";
        window.productVariantsData = @json($todos_los_articulos->keyBy('id'));

        document.addEventListener('DOMContentLoaded', function() {
            const variantsData = window.productVariantsData;
            const sliderParent = jQuery('main.postcontent');
            const articleNameEl = document.getElementById('article-name');
            const priceContainer = document.getElementById('price-container');
            const stockContainer = document.getElementById('stock-container');
            const tallasContainer = document.getElementById('tallas-container');
            const capacidadesContainer = document.getElementById('capacidades-container');
            const variantSelectors = document.querySelectorAll('.variant-selector');

            const sliderOptions = {
                animation: "fade",
                pagi: false,
                selector: ".slider-wrap > .slide",
                prevText: "<",
                nextText: ">",
            };

            function updateView(variantId) {
                const variant = variantsData[variantId];
                if (!variant) return;

                // actualizar nombre articulo
                articleNameEl.textContent = variant.articulo.nombre;

                // actualizar galeria
                let newSliderHtml =
                    '<div class="fslider" data-pagi="false" data-animation="fade"><div class="flexslider"><div class="slider-wrap">';
                if (variant.articulo.posiciones && variant.articulo.posiciones.length > 0) {
                    variant.articulo.posiciones.forEach(posicion => {
                        const imageUrl = `${window.ASSET_URL}/${posicion.imagen}`;
                        newSliderHtml +=
                            `<div class="slide"><img src="${imageUrl}" alt="${variant.articulo.nombre}"></div>`;
                    });
                } else {
                    const fallbackUrl = "{{ asset($producto->imagen_principal) }}";
                    newSliderHtml +=
                        `<div class="slide"><img src="${fallbackUrl}" alt="{{ $producto->nombre }}"></div>`;
                }
                newSliderHtml += '</div></div></div>';
                sliderParent.html(newSliderHtml);
                sliderParent.find('.fslider').flexslider(sliderOptions);

                // actualizar precio
                let priceHtml = '';
                const esPorcentaje = variant.descuento_porcentaje == 1;
                const descuentoHabilitado = variant.descuento_habilitado == 1;
                const precioBase = parseFloat(variant.articulo.precio);
                let precioFinal = precioBase;
                let descuentoAplicado = 0;

                if (descuentoHabilitado) {
                    if (esPorcentaje) {
                        descuentoAplicado = (precioBase * parseFloat(variant.descuento)) / 100;
                        precioFinal = precioBase - descuentoAplicado;
                    } else {
                        descuentoAplicado = parseFloat(variant.descuento);
                        precioFinal = precioBase - descuentoAplicado;
                    }
                }

                if (descuentoAplicado > 0) {
                    priceHtml += `<span class="fs-4 fw-bold text-danger">Bs. ${precioFinal.toFixed(2)}</span>`;
                    priceHtml += `<del class="text-muted ms-2">Bs. ${precioBase.toFixed(2)}</del>`;
                    if (esPorcentaje) {
                        priceHtml += `<span class="badge bg-danger text-white ms-2">-${variant.descuento}%</span>`;
                    }
                } else {
                    priceHtml += `<span class="fs-4 fw-bold text-danger">Bs. ${precioFinal.toFixed(2)}</span>`;
                }
                priceHtml += `<p class="small text-muted mb-0">Estado: ${variant.estado}</p>`;
                priceContainer.innerHTML = priceHtml;

                // actualizar stock
                let stockHtml = '';
                const stock = parseInt(variant.stock, 10);
                if (stock > 10) {
                    stockHtml = '<span class="badge bg-success">En Stock</span>';
                } else if (stock > 0 && stock <= 10) {
                    stockHtml = `<span class="badge bg-warning text-dark">¡Solo quedan ${stock}!</span>`;
                } else {
                    stockHtml = '<span class="badge bg-danger">Agotado</span>';
                }
                stockContainer.innerHTML = stockHtml;

                const findSpecs = (specName) => variant.articulo.catalogos.filter(c => c.especificacion && c
                    .especificacion.tipo && c.especificacion.tipo.nombre === specName);

                // actualizar tallas
                let tallasHtml = '';
                const tallas = findSpecs('Tallas');
                if (tallas.length > 0) {
                    tallasHtml += `<h5 class="fw-medium mb-3">Seleccionar Talla:</h5>`;
                    tallasHtml += `<div class="d-flex flex-wrap">`;
                    tallas.forEach((t, index) => {
                        const talla = t.especificacion;
                        tallasHtml +=
                            `<input type="radio" class="btn-check" name="talla" id="talla-${variant.id}-${talla.id}" value="${talla.id}" autocomplete="off" ${index === 0 ? 'checked' : ''}>`;
                        tallasHtml +=
                            `<label class="btn btn-outline-secondary me-2 mb-2" for="talla-${variant.id}-${talla.id}">${talla.descripcion}</label>`;
                    });
                    tallasHtml += `</div><hr>`;
                }
                tallasContainer.innerHTML = tallasHtml;

                // actualizar capacidades
                let capacidadesHtml = '';
                const capacidades = findSpecs('Capacidades');
                if (capacidades.length > 0) {
                    capacidadesHtml += `<h5 class="fw-medium mb-3">Seleccionar Capacidad:</h5>`;
                    capacidadesHtml += `<div class="d-flex flex-wrap">`;
                    capacidades.forEach((c, index) => {
                        const capacidad = c.especificacion;
                        capacidadesHtml +=
                            `<input type="radio" class="btn-check" name="capacidad" id="capacidad-${variant.id}-${capacidad.id}" value="${capacidad.id}" autocomplete="off" ${index === 0 ? 'checked' : ''}>`;
                        capacidadesHtml +=
                            `<label class="btn btn-outline-secondary me-2 mb-2" for="capacidad-${variant.id}-${capacidad.id}">${capacidad.descripcion}</label>`;
                    });
                    capacidadesHtml += `</div><hr>`;
                }
                capacidadesContainer.innerHTML = capacidadesHtml;
            }

            variantSelectors.forEach(selector => {
                selector.addEventListener('click', function() {
                    variantSelectors.forEach(s => s.classList.remove('border-primary'));
                    this.classList.add('border-primary');

                    const selectedVariantId = this.dataset.saId;
                    updateView(selectedVariantId);
                });
            });

            const initialVariantId = document.querySelector('.variant-selector.border-primary').dataset.saId;
            if (initialVariantId) {
                const initialVariant = variantsData[initialVariantId];
                if (initialVariant) {
                    let stockHtml = '';
                    const stock = parseInt(initialVariant.stock, 10);
                    if (stock > 10) {
                        stockHtml = '<span class="badge bg-success">En Stock</span>';
                    } else if (stock > 0 && stock <= 10) {
                        stockHtml = `<span class="badge bg-warning text-dark">¡Solo quedan ${stock}!</span>`;
                    } else {
                        stockHtml = '<span class="badge bg-danger">Agotado</span>';
                    }
                    stockContainer.innerHTML = stockHtml;
                }
            }
        });
    </script>
@endpush
