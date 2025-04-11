<x-app-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <h2 class="text-2xl font-bold text-center mb-6">Login</h2>

            <div x-data="loginForm()">
                <form @submit.prevent="submitForm" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" x-model="form.email" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p x-show="errors.email" x-text="errors.email" class="mt-1 text-sm text-red-600"></p>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" id="password" x-model="form.password" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p x-show="errors.password" x-text="errors.password" class="mt-1 text-sm text-red-600"></p>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="remember" x-model="form.remember"
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                    </div>

                    <div>
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Login
                        </button>
                    </div>

                    <div class="text-center text-sm text-gray-600">
                        <p>Don't have an account? <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-500">Register</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function loginForm() {
            return {
                form: {
                    email: '',
                    password: '',
                    remember: false
                },
                errors: {},
                isSubmitting: false,

                async submitForm() {
                    this.isSubmitting = true;
                    this.errors = {};

                    try {
                        const response = await fetch('/api/login', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            credentials: 'include',
                            body: JSON.stringify(this.form)
                        });

                        const data = await response.json();
                        console.log('Login response:', data);
                        // console.log('Login API Response:', data);
                        // console.log('Response status:', response.status, response.ok);

                        if (response.ok) {
                            // Store token in localStorage
                        if (data.token) {
                            // console.log('Token received:', data.token.substring(0, 10) + '...');
                            localStorage.setItem('auth_token', data.token);
                        }
                            // Redirect to dashboard on successful login
                            // window.location.href = '{{ route('dashboard') }}';
                            // Manually navigate to dashboard
                            // Redirect happens outside the conditional to ensure it works
                            window.location.href = '/dashboard';
                        } else {
                            // console.error('No token received in response');
                            if (data.errors) {
                                this.errors = data.errors;
                            } else if (data.message) {
                                this.errors.email = data.message;
                            } else {
                                alert('An error occurred during login');
                            }
                        }

                        // console.log('Attempting navigation to dashboard...');
                        // Manually navigate to dashboard
                        // window.location.href = '/dashboard';
                    } catch (error) {
                        console.error('Login error:', error);
                        alert('Network error occurred. Please try again.');
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>