<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Task') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div x-data="taskForm()" x-init="init()">
                        <form @submit.prevent="submitForm" class="space-y-6">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                                <input type="text" id="title" x-model="form.title"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p x-show="errors.title" x-text="errors.title" class="mt-1 text-sm text-red-600"></p>
                            </div>

                            <div>
                                <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                                <textarea id="content" x-model="form.content" rows="5"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                <p x-show="errors.content" x-text="errors.content" class="mt-1 text-sm text-red-600"></p>
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select id="status" x-model="form.status"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="to do">To Do</option>
                                    <option value="in progress">In Progress</option>
                                    <option value="done">Done</option>
                                </select>
                                <p x-show="errors.status" x-text="errors.status" class="mt-1 text-sm text-red-600"></p>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" id="is_published" x-model="form.is_published"
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <label for="is_published" class="ml-2 block text-sm text-gray-700">Publish</label>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Images (Max: 4 files, 4MB each)</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                <span>Upload files</span>
                                                <input id="file-upload" type="file" class="sr-only" multiple @change="handleFileUpload" accept="image/png, image/jpeg, image/jpg">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, JPEG up to 4MB</p>
                                    </div>
                                </div>
                                <p x-show="errors.images" x-text="errors.images" class="mt-1 text-sm text-red-600"></p>

                                <!-- Preview selected images -->
                                <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4" x-show="previewImages.length">
                                    <template x-for="(image, index) in previewImages" :key="index">
                                        <div class="relative">
                                            <img :src="image" class="h-24 w-24 object-cover rounded-md">
                                            <button type="button" @click="removeImage(index)" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <a href="{{ route('tasks.index') }}" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-gray-700 bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    Cancel
                                </a>
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Create Task
                                </button>
                            </div>
                        </form>
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

        function taskForm() {
            return {
                form: {
                    title: '',
                    content: '',
                    status: 'to do',
                    is_published: false,
                    images: []
                },
                errors: {},
                previewImages: [],
                isSubmitting: false,

                init() {
                    // Initialize form
                },

                formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                },

                handleImageUpload(event) {
                    const files = event.target.files;
                    const maxSize = 4 * 1024 * 1024; // 4MB
                    let sizeError = false;
                    let errorMessage = '';

                    for (let i = 0; i < files.length; i++) {
                        // Check file size immediately upon selection
                        if (files[i].size > maxSize) {
                            sizeError = true;
                            errorMessage += `Image "${files[i].name}" exceeds the maximum size of 4MB. `;
                            continue; // Skip this file but continue checking others
                        }

                        if (!sizeError) {
                            // Only add valid files to the form
                            this.form.images.push(files[i]);

                            // Create preview
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.previewImages.push({
                                    url: e.target.result,
                                    name: files[i].name,
                                    size: this.formatFileSize(files[i].size)
                                });
                            };
                            reader.readAsDataURL(files[i]);
                        }
                    }

                    // Show error if any files were too large
                    if (sizeError) {
                        this.errors.images = errorMessage.trim();
                    }

                    // Reset file input
                    event.target.value = '';
                },

                handleFileUpload(event) {
                    const files = event.target.files;
                    if (!files.length) return;

                    // Validate file types and sizes
                    const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                    const maxSize = 4 * 1024 * 1024; // 4MB
                    const maxFiles = 4;
                    let sizeErrors = [];
                    let typeErrors = [];
                    let validFilesCount = 0;

                    // First validate all files for size and type
                    for (let i = 0; i < files.length; i++) {
                        let file = files[i];

                        // Check file type
                        if (!validTypes.includes(file.type)) {
                            typeErrors.push(`"${file.name}" is not a valid image format. Only JPG, JPEG, and PNG are allowed.`);
                            continue;
                        }

                        // Check file size
                        if (file.size > maxSize) {
                            sizeErrors.push(`Image "${file.name}" exceeds the maximum size of 4MB.`);
                            continue;
                        }

                        // Count valid files
                        validFilesCount++;
                    }

                    // Check if adding valid files would exceed max files limit
                    if (this.form.images.length + validFilesCount > maxFiles) {
                        this.errors.images = `You can only upload a maximum of ${maxFiles} images. Please remove some images first.`;
                        event.target.value = '';
                        return;
                    }

                    // Display errors if any
                    if (sizeErrors.length > 0) {
                        this.errors.images = sizeErrors.join(' ');
                        event.target.value = '';
                        return;
                    }

                    if (typeErrors.length > 0) {
                        this.errors.images = typeErrors.join(' ');
                        event.target.value = '';
                        return;
                    }

                    // If no errors, add all files
                    for (let i = 0; i < files.length; i++) {
                        let file = files[i];
                        this.form.images.push(file);

                        // Create preview
                        const reader = new FileReader();
                        reader.onload = e => {
                            this.previewImages.push(e.target.result);
                        };
                        reader.readAsDataURL(file);
                    }

                    // Clear any previous errors
                    this.errors.images = '';

                    // Reset file input
                    event.target.value = '';

                    // if (this.form.images.length + files.length > maxFiles) {
                    //     this.errors.images = `You can only upload a maximum of ${maxFiles} images`;
                    //     event.target.value = '';
                    //     return;
                    // }

                    // Array.from(files).forEach(file => {
                    //     if (!validTypes.includes(file.type)) {
                    //         this.errors.images = 'Only JPG, JPEG, and PNG files are allowed';
                    //         return;
                    //     }

                    //     if (file.size > maxSize) {
                    //         this.errors.images = 'File size should not exceed 4MB';
                    //         return;
                    //     }

                    //     this.form.images.push(file);

                    //     // Create preview
                    //     const reader = new FileReader();
                    //     reader.onload = e => {
                    //         this.previewImages.push(e.target.result);
                    //     };
                    //     reader.readAsDataURL(file);
                    // });

                    // this.errors.images = '';
                },

                removeImage(index) {
                    this.form.images.splice(index, 1);
                    this.previewImages.splice(index, 1);
                },

                async submitForm() {
                    this.isSubmitting = true;
                    this.errors = {};

                    // Validate image sizes before submitting
                    const maxSize = 4 * 1024 * 1024; // 4MB
                    for (let i = 0; i < this.form.images.length; i++) {
                        if (this.form.images[i].size > maxSize) {
                            this.errors.images = `Image "${this.form.images[i].name}" exceeds the maximum size of 4MB`;
                            this.isSubmitting = false;
                            return;
                        }
                    }

                    const formData = new FormData();
                    formData.append('title', this.form.title);
                    formData.append('content', this.form.content);
                    formData.append('status', this.form.status);
                    formData.append('is_published', this.form.is_published ? '1' : '0');

                    this.form.images.forEach(image => {
                        formData.append('images[]', image);
                    });

                    try {
                        const token = localStorage.getItem('auth_token');
                        const response = await fetch('/api/tasks', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                // 'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                                'Authorization': `Bearer ${token}`
                            },
                            credentials: 'include',
                            body: formData
                        });

                        const data = await response.json();

                        if (response.ok) {
                            // Redirect to tasks list
                            // window.location.href = '{{ route('tasks.index') }}';
                            // Show success message with a short delay before redirect
                            localStorage.setItem('task_success_message', 'Task created successfully!');
                            setTimeout(() => {
                                window.location.href = '{{ route('tasks.index') }}';
                            }, 500);
                        } else {
                            if (data.errors) {
                                this.errors = data.errors;
                            } else {
                                alert('An error occurred while saving the task');
                            }
                        }
                    } catch (error) {
                        console.error('Error creating task:', error);
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>