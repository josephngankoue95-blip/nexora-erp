<header class="ecommerce-header">

    <div class="ecommerce-header-left">

        <button
            type="button"
            class="ecommerce-mobile-toggle"
            id="ecommerceMobileToggle"
            aria-label="Ouvrir le menu"
        >
            ☰
        </button>


        <div class="ecommerce-header-title">

            <h1>
                @yield('page-title', 'E-commerce')
            </h1>

            <p>
                Gérez votre boutique en ligne depuis votre ERP.
            </p>

        </div>

    </div>


    <div class="ecommerce-header-actions">

        {{-- ========================================================
             COMPANY
        ========================================================= --}}

        @php
            $currentCompany = auth()->user()?->company;
        @endphp

        @if($currentCompany)

            <div class="ecommerce-header-company">

                <div class="ecommerce-company-avatar">

                    @if($currentCompany->logo)
                        <img
                            src="{{ Storage::url($currentCompany->logo) }}"
                            alt="{{ $currentCompany->name }}"
                            style="
                                width: 100%;
                                height: 100%;
                                object-fit: cover;
                                border-radius: inherit;
                            "
                        >
                    @else
                        {{ strtoupper(substr($currentCompany->name, 0, 1)) }}
                    @endif

                </div>


                <div class="ecommerce-company-info">

                    <div class="ecommerce-company-name">
                        {{ $currentCompany->name }}
                    </div>

                    <div class="ecommerce-company-role">
                        Boutique en ligne
                    </div>

                </div>

            </div>

        @endif

    </div>

</header>