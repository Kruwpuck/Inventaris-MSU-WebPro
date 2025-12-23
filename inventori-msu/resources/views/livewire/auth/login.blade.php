<x-layouts.auth>

    @push('styles')
        <style>
            body {
                background-color: #f8f9fa;
                /* Bootstrap bg-light */
            }

            .login-card {
                border-radius: 2rem;
                overflow: hidden;
                box-shadow: 0 1rem 3rem rgba(0, 0, 0, .175);
                min-height: 600px;
            }

            .form-section {
                transition: background-color 0.5s ease;
            }

            .form-section.pengelola {
                background-color: #ffffff;
            }

            .form-section.pengurus {
                background-color: #f0f8ff;
                /* AliceBlue for slight tint */
            }

            .slider-section {
                position: relative;
                overflow: hidden;
                background-color: #e9ecef;
            }

            .slider-track {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                transition: transform 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            }

            /* Simple fade/slide effect for images */
            .role-image {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                max-width: 100%;
                max-height: 90%;
                opacity: 0;
                transition: all 0.6s ease;
            }

            .role-image.active {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }

            .role-image.inactive-left {
                opacity: 0;
                transform: translate(-70%, -50%) scale(0.9);
            }

            .role-image.inactive-right {
                opacity: 0;
                transform: translate(-30%, -50%) scale(0.9);
            }

            .nav-btn {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.8);
                border: none;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s;
                cursor: pointer;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }

            .nav-btn:hover {
                background: #fff;
                transform: scale(1.1);
            }

            .switch-role-btn {
                cursor: pointer;
                transition: all 0.3s;
                padding: 0.5rem 1rem;
                border-radius: 2rem;
                border: 1px solid #dee2e6;
            }

            .switch-role-btn:hover {
                background-color: #212529;
                color: #fff !important;
                border-color: #212529;
            }
        </style>
    @endpush

    <div class="min-vh-100 d-flex align-items-center justify-content-center py-5" x-data="{ 
        role: 'pengelola', 
        isLoading: false,
        toggleRole() { 
            if (this.isLoading) return;
            this.role = (this.role === 'pengelola' ? 'pengurus' : 'pengelola'); 
        } 
    }">

        <div class="card login-card border-0 w-100" style="max-width: 1000px;">
            <div class="row g-0 h-100">

                <!-- LEFT SIDE: Form -->
                <div class="col-lg-6 p-5 d-flex flex-column justify-content-center form-section"
                    :class="role === 'pengelola' ? 'pengelola' : 'pengurus'">

                    <div class="mb-5">
                        <h1 class="display-5 fw-bold mb-1">
                            <span x-text="role === 'pengelola' ? 'PENGELOLA' : 'PENGURUS'"></span>
                            <span class="text-muted fw-light d-block fs-3">SIDE</span>
                        </h1>
                        <p class="text-muted mt-3" x-text="role === 'pengelola' 
                            ? 'Login khusus untuk pengelola aset dan inventaris utama.' 
                            : 'Login untuk pengurus operasional harian dan peminjaman.'">
                        </p>
                    </div>

                    <form method="POST" action="{{ route('login.store') }}" @submit="isLoading = true">
                        @csrf
                        <input type="hidden" name="role_context" x-model="role">

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary text-uppercase ls-1">Email
                                Address</label>
                            <input type="email" name="email" class="form-control form-control-lg bg-light border-0"
                                placeholder="name@example.com" required autofocus :readonly="isLoading">
                            @error('email')
                                <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary text-uppercase ls-1">Password</label>
                            <input type="password" name="password"
                                class="form-control form-control-lg bg-light border-0" placeholder="Enter password"
                                required :readonly="isLoading">
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label small text-secondary" for="remember">
                                    Remember me
                                </label>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-5">
                            <div class="d-flex align-items-center gap-2 switch-role-btn" @click="toggleRole()"
                                :class="{ 'opacity-50': isLoading }" :style="isLoading ? 'cursor: not-allowed;' : ''">
                                <i class="bi bi-arrow-repeat fs-5"></i>
                                <span class="small fw-semibold">Ganti Role</span>
                            </div>

                            <button type="submit" class="btn btn-dark btn-lg px-5 rounded-pill shadow-sm"
                                :disabled="isLoading">
                                <span x-show="!isLoading">Masuk</span>
                                <span x-show="isLoading" class="spinner-border spinner-border-sm" role="status"
                                    aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- RIGHT SIDE: Slider Image -->
                <div class="col-lg-6 d-none d-lg-block position-relative slider-section">
                    <!-- Decorative BG -->
                    <div class="position-absolute top-0 end-0 w-100 h-100"
                        style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);"></div>

                    <!-- Images Container -->
                    <div class="w-100 h-100 position-relative">
                        <!-- Pengelola Image -->
                        <img src="{{ asset('aset/pengelola_v2.jpg') }}" class="role-image"
                            :class="role === 'pengelola' ? 'active' : 'inactive-left'" alt="Pengelola">

                        <!-- Pengurus Image -->
                        <img src="{{ asset('aset/pengurus_v2.jpg') }}" class="role-image"
                            :class="role === 'pengurus' ? 'active' : 'inactive-right'" alt="Pengurus">
                    </div>

                    <!-- Navigation Arrows -->
                    <button class="nav-btn position-absolute top-50 start-0 translate-middle-y ms-4"
                        @click="if(!isLoading) role = 'pengelola'" x-show="role === 'pengurus'" :disabled="isLoading"
                        :style="isLoading ? 'opacity: 0.5; cursor: not-allowed;' : ''">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="nav-btn position-absolute top-50 end-0 translate-middle-y me-4"
                        @click="if(!isLoading) role = 'pengurus'" x-show="role === 'pengelola'" :disabled="isLoading"
                        :style="isLoading ? 'opacity: 0.5; cursor: not-allowed;' : ''">
                        <i class="bi bi-chevron-right"></i>
                    </button>

                    <!-- Dots Indicator -->
                    <div class="position-absolute bottom-0 start-50 translate-middle-x mb-4 d-flex gap-2">
                        <div class="rounded-pill transition-all"
                            :class="role === 'pengelola' ? 'bg-dark' : 'bg-secondary opacity-25'"
                            style="width: 24px; height: 6px; transition: all 0.3s;"></div>
                        <div class="rounded-pill transition-all"
                            :class="role === 'pengurus' ? 'bg-dark' : 'bg-secondary opacity-25'"
                            style="width: 24px; height: 6px; transition: all 0.3s;"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.auth>