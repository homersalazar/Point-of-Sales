@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-primary/20 via-base-200 to-secondary/20 flex items-center justify-center p-6 w-full">

        <div class="w-full max-w-5xl grid md:grid-cols-2 bg-base-100 shadow-2xl rounded-3xl overflow-hidden">

            <!-- LEFT SIDE (POS Branding) -->
            <div class="hidden md:flex flex-col justify-center items-center bg-primary text-primary-content p-10 relative">

                <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_center,_white,_transparent_60%)]"></div>

                <div class="relative z-10 text-center space-y-4">
                    <div class="text-5xl">
                        <i class="fa-solid fa-cash-register"></i>
                    </div>

                    <h1 class="text-3xl font-bold">
                        POS System
                    </h1>

                    <p class="opacity-80 text-sm">
                        Fast. Reliable. Simple.
                    </p>
                </div>

            </div>

            <!-- RIGHT SIDE (LOGIN FORM) -->
            <div class="p-10 flex flex-col justify-center">

                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-base-content">
                        Login
                    </h2>
                    <p class="text-sm text-base-content/60">
                        Enter your credentials to continue
                    </p>
                </div>

                <form method="POST" action="" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Email</span>
                        </label>

                        <input
                            type="email"
                            name="email"
                            placeholder="Enter your email"
                            class="input input-bordered w-full focus:input-primary"
                            required
                        >
                    </div>

                    <!-- Password -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Password</span>
                        </label>

                        <input
                            type="password"
                            name="password"
                            placeholder="Enter your password"
                            class="input input-bordered w-full focus:input-primary"
                            required
                        >
                    </div>

                    <!-- Remember + Forgot -->
                    <div class="flex items-center justify-between text-sm">

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" class="checkbox checkbox-sm checkbox-primary">
                            <span>Remember me</span>
                        </label>

                        <a href="#" class="link link-hover text-primary">
                            Forgot password?
                        </a>

                    </div>

                    <!-- Login Button -->
                    <button class="btn btn-primary w-full mt-2">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        Login
                    </button>

                </form>

            </div>

        </div>

    </div>
@endsection
