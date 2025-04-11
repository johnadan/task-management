<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tasks') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Task Filters -->
                    <div x-data="taskFilters()" class="mb-6">
                        <div class="flex flex-col md:flex-row gap-4 mb-4">
                            <div class="flex-1">
                                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                                <input type="text" id="search" x-model="search" @input="applyFilters"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select id="status" x-model="status" @change="applyFilters"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">All</option>
                                    <option value="to do">To Do</option>
                                    <option value="in progress">In Progress</option>
                                    <option value="done">Done</option>
                                </select>
                            </div>
                            <div>
                                <label for="sort" class="block text-sm font-medium text-gray-700">Sort By</label>
                                <select id="sort" x-model="sortBy" @change="applyFilters"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="created_at">Date Created</option>
                                    <option value="title">Title</option>
                                </select>
                            </div>
                            <div>
                                <label for="direction" class="block text-sm font-medium text-gray-700">Direction</label>
                                <select id="direction" x-model="sortDirection" @change="applyFilters"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="desc">Descending</option>
                                    <option value="asc">Ascending</option>
                                </select>
                            </div>
                            <div>
                                <label for="perPage" class="block text-sm font-medium text-gray-700">Per Page</label>
                                <select id="perPage" x-model="perPage" @change="applyFilters"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Tasks List -->
                    <div x-data="tasks()" x-init="loadTasks()">
                        <!-- Success Messages -->
                        <div x-data="{ showSuccess: false, message: '' }" x-init="
                            const successMsg = localStorage.getItem('task_success_message');
                            if (successMsg) {
                                message = successMsg;
                                showSuccess = true;
                                localStorage.removeItem('task_success_message');
                                setTimeout(() => showSuccess = false, 5000);
                            }
                        ">
                            <div x-show="showSuccess" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                                <span class="block sm:inline" x-text="message"></span>
                                <span class="absolute top-0 bottom-0 right-0 px-4 py-3" @click="showSuccess = false">
                                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <title>Close</title>
                                        <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div class="mb-4 flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900">Your Tasks</h3>
                            <a href="{{ route('tasks.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                New Task
                            </a>
                        </div>

                        <div class="space-y-4">
                            <template x-if="tasks.length === 0">
                                <div class="text-center py-8">
                                    <p class="text-gray-500">No tasks found. Click "New Task" to create one.</p>
                                </div>
                            </template>

                            <template x-for="task in tasks" :key="task.id">
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-medium text-lg" x-text="task.title"></h4>
                                            <p class="mt-1 text-gray-600" x-text="task.content.substring(0, 100) + (task.content.length > 100 ? '...' : '')"></p>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span x-show="task.is_published" class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Published</span>
                                            <span x-show="!task.is_published" class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Draft</span>
                                            <span
                                                :class="{
                                                    'bg-red-100 text-red-800': task.status === 'to do',
                                                    'bg-yellow-100 text-yellow-800': task.status === 'in progress',
                                                    'bg-green-100 text-green-800': task.status === 'done'
                                                }"
                                                class="px-2 py-1 text-xs rounded-full"
                                                x-text="task.status">
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex justify-between items-center">
                                        <div class="text-sm text-gray-500" x-text="formatDate(task.created_at)"></div>
                                        <div class="flex space-x-2">
                                        <a :href="`/tasks/${task.id}/edit`" class="inline-flex items-center px-3 py-1 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                                            Edit
                                            </a>
                                            <button @click="deleteTask(task.id)" class="inline-flex items-center px-3 py-1 bg-red-200 border border-transparent rounded-md font-semibold text-xs text-red-700 uppercase tracking-widest hover:bg-red-300">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            <nav class="flex items-center justify-between">
                                <div class="flex-1 flex justify-between">
                                    <button @click="prevPage" :disabled="currentPage === 1" :class="{'opacity-50': currentPage === 1}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Previous
                                    </button>
                                    <span class="text-sm text-gray-700">
                                        Page <span x-text="currentPage"></span> of <span x-text="lastPage"></span>
                                    </span>
                                    <button @click="nextPage" :disabled="currentPage === lastPage" :class="{'opacity-50': currentPage === lastPage}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Next
                                    </button>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Check if token exists and redirect to login if not
        document.addEventListener('DOMContentLoaded', function() {
            const token = localStorage.getItem('auth_token');
            if (!token) {
                window.location.href = "{{ route('login') }}";
            }
        });

        function taskFilters() {
            return {
                search: '',
                status: '',
                sortBy: 'created_at',
                sortDirection: 'desc',
                perPage: 10,

                applyFilters() {
                    window.dispatchEvent(new CustomEvent('apply-filters', {
                        detail: {
                            search: this.search,
                            status: this.status,
                            sortBy: this.sortBy,
                            sortDirection: this.sortDirection,
                            perPage: this.perPage
                        }
                    }));
                }
            }
        }

        function tasks() {
            return {
                tasks: [],
                currentPage: 1,
                lastPage: 1,
                filters: {
                    search: '',
                    status: '',
                    sort_by: 'created_at',
                    sort_direction: 'desc',
                    per_page: 10
                },

                async loadTasks(page = 1) {
                    this.currentPage = page;
                    console.log('Loading tasks for page:', page);
                    console.log('Using filters:', this.filters);

                    const params = new URLSearchParams({
                        page: this.currentPage,
                        search: this.filters.search,
                        status: this.filters.status,
                        sort_by: this.filters.sort_by,
                        sort_direction: this.filters.sort_direction,
                        per_page: this.filters.per_page
                    });

                    try {
                        const token = localStorage.getItem('auth_token');
                        console.log('Using auth token:', token ? token.substring(0, 10) + '...' : 'none');

                        const response = await fetch(`/api/tasks?${params.toString()}`, {
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            credentials: 'include'
                        });

                        console.log('API response status:', response.status);
                        const data = await response.json();
                        console.log('API response data:', data);
                        // this.tasks = data.data;
                        // this.currentPage = data.current_page;
                        // this.lastPage = data.last_page;
                        if (data.data) {
                            this.tasks = data.data;
                            this.currentPage = data.current_page || this.currentPage;
                            this.lastPage = data.last_page || 1;
                            console.log('Tasks loaded:', this.tasks.length);
                        } else {
                            console.error('Invalid API response format:', data);
                            this.tasks = [];
                        }
                    } catch (error) {
                        console.error('Error loading tasks:', error);
                        this.tasks = [];
                    }
                },

                async deleteTask(id) {
                    if (!confirm('Are you sure you want to delete this task?')) return;

                    try {
                        await fetch(`/api/tasks/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            credentials: 'include'
                        });

                        this.loadTasks(this.currentPage);
                    } catch (error) {
                        console.error('Error deleting task:', error);
                    }
                },

                prevPage() {
                    if (this.currentPage > 1) {
                        this.loadTasks(this.currentPage - 1);
                    }
                },

                nextPage() {
                    if (this.currentPage < this.lastPage) {
                        this.loadTasks(this.currentPage + 1);
                    }
                },

                formatDate(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleDateString();
                },

                init() {
                    window.addEventListener('apply-filters', (event) => {
                        this.filters = {
                            search: event.detail.search,
                            status: event.detail.status,
                            sort_by: event.detail.sortBy,
                            sort_direction: event.detail.sortDirection,
                            per_page: event.detail.perPage
                        };

                        this.loadTasks(1);
                    });
                }
            }
        }
    </script>
</x-app-layout>