<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Company Document List') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="dashboard-card">
                <div class="card-content" x-data="{
                    authMessage: '',
                    logs: [],
                    documentName: '',
                    logPagination: {},
                    filters: {
                        'all': 'All',
                        'approved': 'Approved',
                        'obsolete': 'Obsolete',
                        'pending_send_back': 'Pending/Send Back',
                        'rejected': 'Rejected',
                        'draft': 'Draft'
                    },
                    currentFilter: '{{ request('status', 'all') }}',
                    handleAuthClick(event, permission, message, redirectUrl = '', deleteModal = '') {
                        if (permission) {
                            if (redirectUrl) {
                                window.location.href = redirectUrl;
                            } else if (deleteModal) {
                                $dispatch('open-modal', deleteModal);
                            }
                        } else {
                            this.authMessage = message;
                            $dispatch('open-modal', 'auth-modal');
                        }
                    },
                    async showLogs(ruleId, ruleName) {
                        this.documentName = ruleName;
                        const response = await fetch(`/api/company-rules/${ruleId}/logs`);
                        const data = await response.json();
                        this.logs = data.data.map(log => {
                            log.created_at_formatted = this.formatDate(log.created_at);
                            log.details_formatted = this.formatDetails(log.details);
                            return log;
                        });
                        this.logPagination = data;
                        $dispatch('open-modal', 'log-modal');
                    },
                    formatDate(dateString) {
                        const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
                        return new Date(dateString).toLocaleDateString(undefined, options);
                    },
                    formatDetails(details) {
                        if (typeof details === 'object' && details !== null) {
                            return Object.entries(details).map(([key, value]) => `<strong>${key}:</strong> ${value}`).join('<br>');
                        }
                        return details;
                    }
                }">
                    
                    <div class="flex justify-between items-center mb-4">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    <span x-text="filters[currentFilter]"></span>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('company-rules.index')">
                                    All
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('company-rules.index', ['status' => 'approved'])">
                                    Approved
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('company-rules.index', ['status' => 'obsolete'])">
                                    Obsolete
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('company-rules.index', ['status' => 'pending_send_back'])">
                                    Pending/Send Back
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('company-rules.index', ['status' => 'rejected'])">
                                    Rejected
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('company-rules.index', ['status' => 'draft'])">
                                    Draft
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                        <form action="{{ route('company-rules.index') }}" method="GET" class="w-1/3">
                            <div class="relative">
                                <x-text-input type="text" name="search" class="w-full pl-10" placeholder="Search by name or number..." value="{{ request('search') }}" />
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </span>
                            </div>
                        </form>
                        <div class="flex space-x-2">
                            <a href="{{ route('company-rules.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-800">
                                + Document
                            </a>
                            <a href="{{ route('company-rules.create-revision') }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600">
                                + Revise
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto border rounded-lg">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>NO.</th>
                                    <th>Document Name</th>
                                    <th>Number</th>
                                    <th>Effective Date</th>
                                    <th>Status</th>
                                    <th>Version</th>
                                    <th>Actions</th>
                                    <th>Log</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rules as $rule)
                                    <tr class="{{ $rule->is_obsolete ? 'text-gray-400 line-through' : '' }}">
                                        <td class="text-gray-500">{{ ($rules->currentPage() - 1) * $rules->perPage() + $loop->iteration }}</td>
                                        <td class="font-medium {{ $rule->is_obsolete ? 'text-gray-400' : 'text-gray-900' }}">{{ $rule->document_name }}</td>
                                        <td class="{{ $rule->is_obsolete ? 'text-gray-400' : 'text-gray-500' }}">{{ $rule->number }}</td>
                                        <td class="{{ $rule->is_obsolete ? 'text-gray-400' : 'text-gray-500' }}">{{ \Carbon\Carbon::parse($rule->effective_date)->format('d F Y') }}</td>
                                        <td>
                                            @if($rule->is_obsolete)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-600">
                                                    Obsolete
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($rule->status == 'Approved') bg-green-100 text-green-800 @elseif(Str::startsWith($rule->status, 'Pending')) bg-yellow-100 text-yellow-800 @elseif($rule->status == 'Rejected' || $rule->status == 'Send Back') bg-red-100 text-red-800 @else bg-gray-100 text-gray-800 @endif">
                                                    {{ $rule->status }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="{{ $rule->is_obsolete ? 'text-gray-400' : 'text-gray-500' }}">{{ $rule->version }}</td>
                                        <td class="font-medium">
                                            <div class="flex items-center space-x-4 actions-container">
                                                <a href="{{ route('company-rules.show', $rule) }}" class="text-indigo-600 hover:text-indigo-800 " title="View">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.022 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                                                </a>
                                                @if ($rule->status !== 'Rejected')
                                                @if($rule->status === 'Approved')
                                                    {{-- Button for Approved status --}}
                                                    <button type="button" 
                                                        @click.prevent="handleAuthClick($event, false, 'This document cannot be edited because it is already Approved.', '{{ route('company-rules.edit', $rule) }}')"
                                                        class="text-yellow-600 hover:text-yellow-800" title="Edit">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" /><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" /></svg>
                                                    </button>
                                                @elseif(\Illuminate\Support\Str::startsWith($rule->status, 'Pending'))
                                                    {{-- Button for Pending status --}}
                                                    <button type="button" 
                                                        @click.prevent="handleAuthClick($event, false, 'This document cannot be edited because it is in Pending Approval status.', '{{ route('company-rules.edit', $rule) }}')"
                                                        class="text-yellow-600 hover:text-yellow-800" title="Edit">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" /><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" /></svg>
                                                    </button>
                                                @else
                                                    {{-- Original button for other statuses (Draft, Send Back) --}}
                                                    <button type="button" 
                                                        @click.prevent="handleAuthClick($event, @json(Auth::user()->can('update', $rule)), 'This document can only be edited by the creator.', '{{ route('company-rules.edit', $rule) }}')"
                                                        class="text-yellow-600 hover:text-yellow-800" title="Edit">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" /><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" /></svg>
                                                    </button>
                                                @endif
                                                @endif
                                                <button type="button"
                                                    @click.prevent="handleAuthClick($event, @json(Auth::user()->can('delete', $rule)), 'Documents can only be deleted by the creator.', '', 'confirm-rule-deletion-{{ $rule->id }}')"
                                                    class="text-red-600 hover:text-red-800" title="Delete">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <button @click="showLogs({{ $rule->id }}, '{{ e($rule->document_name) }}')" class="text-blue-600 hover:text-blue-800" title="View Logs">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm0 2h12v10H4V5zm2 1a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm0 4a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm0 4a1 1 0 011-1h3a1 1 0 110 2H7a1 1 0 01-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center text-gray-500 py-4">No data found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">{{ $rules->links() }}</div>

                    {{-- Modal otorisasi --}}
                    <x-modal name="auth-modal">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 flex items-center justify-center w-12 h-12 mx-0 text-red-600 bg-red-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                </div>
                                <div class="mt-0 ml-4 text-left">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900">Access Denied</h3>
                                    <div class="mt-2">
                                        <p x-text="authMessage" class="text-sm text-gray-500"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end">
                                <button type="button" @click="$dispatch('close')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                    Agree
                                </button>
                            </div>
                        </div>
                    </x-modal>

                    <x-modal name="log-modal">
                        <div class="p-6">
                            <h2 class="text-lg font-medium text-gray-900" x-text="'Activity Log for ' + documentName"></h2>

                            <div class="mt-4 overflow-x-auto border rounded-lg">
                                <table class="custom-table">
                                    <thead>
                                        <tr>
                                            <th>Activity</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="log in logs" :key="log.id">
                                            <tr>
                                                <td x-text="log.activity"></td>
                                                <td x-text="log.created_at_formatted"></td>
                                            </tr>
                                        </template>
                                        <template x-if="logs.length === 0">
                                            <tr>
                                                <td colspan="2" class="text-center text-gray-500 py-4">No activity found.</td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <x-secondary-button @click="$dispatch('close')">{{ __('Close') }}</x-secondary-button>
                            </div>
                        </div>
                    </x-modal>

                </div>
            </div>
        </div>
    </div>

    {{-- Modal untuk konfirmasi hapus --}}
    @foreach ($rules as $rule)
        <x-modal :name="'confirm-rule-deletion-' . $rule->id">
            <form method="post" action="{{ route('company-rules.destroy', $rule) }}" class="p-6">
                @csrf
                @method('delete')
                <h2 class="text-lg font-medium text-gray-900">Are you sure?</h2>
                <p class="mt-1 text-sm text-gray-600">Once the document "{{ $rule->document_name }}" is deleted, it will be permanently removed.</p>
                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                    <x-danger-button class="ms-3">{{ __('Delete Document') }}</x-danger-button>
                </div>
            </form>
        </x-modal>
    @endforeach

    @push('styles')
    <style>.actions-container > * { position: relative; z-index: 10; }</style>
    @endpush
</x-app-layout>