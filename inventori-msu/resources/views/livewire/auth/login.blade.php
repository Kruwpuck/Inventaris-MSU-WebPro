<x-layouts.auth>

    @push('styles')
        <style>
            body {
                background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
                font-family: 'Poppins', sans-serif;
                min-height: 100vh;
            }

            .login-card {
                border-radius: 24px;
                overflow: hidden;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
                min-height: 600px;
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.5);
                animation: scaleIn 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
            }

            .form-section {
                transition: background-color 0.5s ease;
                padding: 3rem !important;
            }

            .form-section.pengelola {
                background-color: rgba(255, 255, 255, 0.6);
            }

            .form-section.pengurus {
                background-color: rgba(240, 248, 255, 0.6);
            }

            /* Animations */
            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes scaleIn {
                from {
                    opacity: 0;
                    transform: scale(0.95);
                }

                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }

            .anim-delay-1 {
                animation: slideUp 0.6s ease-out 0.1s both;
            }

            .anim-delay-2 {
                animation: slideUp 0.6s ease-out 0.2s both;
            }

            .anim-delay-3 {
                animation: slideUp 0.6s ease-out 0.3s both;
            }

            .anim-delay-4 {
                animation: slideUp 0.6s ease-out 0.4s both;
            }

            .anim-delay-5 {
                animation: slideUp 0.6s ease-out 0.5s both;
            }

            .slider-section {
                position: relative;
                overflow: hidden;
                background-color: #e9ecef;
            }

            .role-image {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                max-width: 100%;
                max-height: 90%;
                opacity: 0;
                transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
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
                width: 44px;
                height: 44px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.9);
                border: none;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s;
                cursor: pointer;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                z-index: 10;
            }

            .nav-btn:hover {
                background: #fff;
                transform: scale(1.1);
                box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
            }

            .switch-role-btn {
                cursor: pointer;
                transition: all 0.3s;
                padding: 0.6rem 1.2rem;
                border-radius: 50px;
                border: 1px solid #dee2e6;
                background: transparent;
            }

            .switch-role-btn:hover {
                background-color: #212529;
                color: #fff !important;
                border-color: #212529;
                transform: translateY(-2px);
            }

            .custom-input {
                background-color: rgba(255, 255, 255, 0.8) !important;
                border: 1px solid rgba(0, 0, 0, 0.05) !important;
                transition: all 0.3s;
            }

            .custom-input:focus {
                background-color: #fff !important;
                box-shadow: 0 0 0 4px rgba(11, 73, 44, 0.1) !important;
                border-color: #0b492c !important;
            }

            .btn-login-msu {
                background: #212529;
                border: none;
                transition: all 0.3s;
            }

            .btn-login-msu:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 16px rgba(33, 37, 41, 0.2);
                background: #000;
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

                    <div class="mb-5 anim-delay-1">
                        <h1 class="display-5 fw-bold mb-1" style="letter-spacing: -1px;">
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

                        <div class="mb-4 anim-delay-2">
                            @error('email')
                                <div class="alert alert-danger border-0 d-flex align-items-center mb-3 shadow-sm rounded-3" role="alert"
                                    style="background-color: #ffe5e9; color: #842029;">
                                    <i class="bi bi-exclamation-octagon-fill me-2"></i>
                                    <div>{{ $message }}</div>
                                </div>
                            @enderror
                            <label class="form-label small fw-bold text-secondary text-uppercase ls-1">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-lg custom-input rounded-3"
                                placeholder="name@example.com" required autofocus :readonly="isLoading">
                        </div>

                        <div class="mb-4 anim-delay-3">
                            <label class="form-label small fw-bold text-secondary text-uppercase ls-1">Password</label>
                            <input type="password" name="password"
                                class="form-control form-control-lg custom-input rounded-3" placeholder="Enter password"
                                required :readonly="isLoading">
                        </div>

                        <div class="mb-4 anim-delay-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" style="cursor:pointer">
                                <label class="form-check-label small text-secondary" for="remember" style="cursor:pointer">
                                    Remember me
                                </label>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-5 anim-delay-5">
                            <div class="d-flex align-items-center gap-2 switch-role-btn" @click="toggleRole()"
                                :class="{ 'opacity-50': isLoading }" :style="isLoading ? 'cursor: not-allowed;' : ''">
                                <i class="bi bi-arrow-repeat fs-5"></i>
                                <span class="small fw-semibold">Ganti Role</span>
                            </div>

                            <button type="submit" class="btn btn-login-msu btn-lg px-5 rounded-pill text-white"
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