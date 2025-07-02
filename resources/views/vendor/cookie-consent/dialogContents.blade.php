@php
    $categories = config('cookie-consent.categories');
@endphp

<div class="js-cookie-consent cookie-consent fixed bottom-0 inset-x-0 pb-2 z-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="p-4 md:p-3 rounded-lg bg-yellow-100 shadow-lg">
            <div class="flex items-center justify-between flex-wrap">
                <div class="w-full">
                    <p class="md:ml-3 text-black cookie-consent__message">
                        {!! trans('cookie-consent::texts.message') !!}
                    </p>
                </div>
            </div>
            
            {{-- Category Toggles --}}
            <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($categories as $key => $label)
                    <div class="flex items-center">
                        <input 
                            type="checkbox"
                            id="cookie-consent-{{ $key }}"
                            name="cookie_consent_{{ $key }}"
                            value="{{ $key }}"
                            class="js-cookie-consent-category"
                            {{ $key === 'necessary' ? 'checked disabled' : 'checked' }}
                        >
                        <label for="cookie-consent-{{ $key }}" class="ml-2 text-sm text-gray-700">{{ $label }}</label>
                    </div>
                @endforeach
            </div>

            {{-- Action Buttons --}}
            <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:gap-4">
                <button class="js-cookie-consent-save cookie-consent__save w-full sm:w-auto cursor-pointer flex items-center justify-center px-4 py-2 rounded-md text-sm font-medium text-yellow-800 bg-yellow-400 hover:bg-yellow-300">
                    {{ trans('cookie-consent::texts.agree') }} {{-- Or a new translation like 'Save Preferences' --}}
                </button>
                <button class="js-cookie-consent-agree-all cookie-consent__agree_all w-full sm:w-auto mt-2 sm:mt-0 cursor-pointer flex items-center justify-center px-4 py-2 rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-500">
                    Accept All
                </button>
            </div>
        </div>
    </div>
</div>